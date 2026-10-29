<?php

namespace Tests\Feature;

use App\Models\Ambassador;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use App\Support\MonthlyIndicators;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndicatorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(ProductSeeder::class);
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function commande(Customer $client, Product $product, int $quantite, string $statut = 'nouvelle', ?Ambassador $ambassador = null, ?string $date = null, int $remise = 0): Order
    {
        $order = Order::create([
            'customer_id' => $client->id,
            'ambassador_id' => $ambassador?->id,
            'status' => $statut,
            'total' => 10000 * $quantite - $remise,
            'discount' => $remise,
            'terms_accepted_at' => now(),
        ]);

        if ($date) {
            $order->forceFill(['created_at' => $date])->save();
        }

        OrderItem::create([
            'order_id' => $order->id, 'product_id' => $product->id,
            'color_id' => $product->colors->first()->id, 'size_id' => $product->sizes->first()->id,
            'quantity' => $quantite, 'unit_price' => 10000,
        ]);

        return $order;
    }

    public function test_un_visiteur_est_renvoye_vers_la_connexion(): void
    {
        $this->get(route('admin.indicators.index'))->assertRedirect(route('admin.login'));
    }

    public function test_la_page_saffiche_sans_donnees(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.indicators.index'))
            ->assertOk()
            ->assertSee('Indicateurs');
    }

    public function test_un_mois_invalide_retombe_sur_le_mois_courant(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.indicators.index', ['mois' => 'abc']))
            ->assertOk();

        $this->actingAs($this->admin())
            ->get(route('admin.indicators.index', ['mois' => '2026-13']))
            ->assertOk();
    }

    public function test_les_chiffres_du_mois_sont_calcules(): void
    {
        $produits = Product::with(['colors', 'sizes'])->take(2)->get();
        $produits[0]->update(['cost' => 4000]);
        $ambassador = Ambassador::create(['name' => 'Jean', 'code' => 'JEAN10', 'commission_percent' => 10]);

        $ancien = Customer::create(['name' => 'Ancien', 'phone' => '90000001', 'consent' => true]);
        $nouveau = Customer::create(['name' => 'Nouveau', 'phone' => '90000002', 'consent' => true]);

        $this->commande($ancien, $produits[0], 1, 'livree', null, '2026-09-10 10:00:00');
        $this->commande($ancien, $produits[0], 2, 'nouvelle', $ambassador, '2026-10-05 10:00:00', 1000);
        $this->commande($nouveau, $produits[1], 1, 'nouvelle', null, '2026-10-06 10:00:00');
        $this->commande($nouveau, $produits[1], 5, 'annulee', null, '2026-10-07 10:00:00');

        $i = MonthlyIndicators::pourMois('2026-10')->calculer();

        $this->assertSame(2, $i['commandes']);
        $this->assertSame(29000, $i['ca']);
        $this->assertSame(14500, $i['panier_moyen']);
        $this->assertSame(2, $i['clients']);
        $this->assertSame(1, $i['nouveaux_clients']);
        $this->assertSame(50, $i['taux_reachat']);
        $this->assertSame(1000, $i['reductions']);
        $this->assertSame(1900, $i['commissions']);
        $this->assertSame(2900, $i['cout_acquisition']);
        $this->assertSame($produits[0]->name, $i['meilleur_produit']['nom']);
        $this->assertSame(2, $i['meilleur_produit']['quantite']);
        $this->assertCount(1, $i['ambassadeurs']);
        $this->assertTrue($i['couts_manquants']);
    }

    public function test_le_stock_dormant_est_compte(): void
    {
        $product = Product::with(['colors', 'sizes'])->first();

        Stock::create([
            'product_id' => $product->id,
            'color_id' => $product->colors->first()->id,
            'size_id' => $product->sizes->first()->id,
            'quantity' => 5,
        ]);

        $this->assertSame(1, MonthlyIndicators::pourMois(now()->format('Y-m'))->calculer()['stocks_dormants']);

        $client = Customer::create(['name' => 'Kofi', 'phone' => '90123456', 'consent' => true]);
        $this->commande($client, $product, 1);

        $this->assertSame(0, MonthlyIndicators::pourMois(now()->format('Y-m'))->calculer()['stocks_dormants']);
    }
}
