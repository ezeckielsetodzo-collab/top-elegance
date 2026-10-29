<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportTest extends TestCase
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

    private function commande(string $nom): Order
    {
        $product = Product::with(['colors', 'sizes'])->first();
        $client = Customer::create(['name' => $nom, 'phone' => '90123456', 'consent' => true]);
        $order = Order::create([
            'customer_id' => $client->id, 'status' => 'nouvelle', 'delivery_address' => 'Tokoin',
            'total' => 20000, 'terms_accepted_at' => now(),
        ]);
        OrderItem::create([
            'order_id' => $order->id, 'product_id' => $product->id,
            'color_id' => $product->colors->first()->id, 'size_id' => $product->sizes->first()->id,
            'quantity' => 2, 'unit_price' => 10000,
        ]);

        return $order;
    }

    public function test_un_visiteur_ne_peut_rien_exporter(): void
    {
        $this->get(route('admin.exports.orders'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.exports.indicators'))->assertRedirect(route('admin.login'));
    }

    public function test_lexport_des_commandes_est_un_csv(): void
    {
        $this->commande('Kofi Mensah');

        $response = $this->actingAs($this->admin())->get(route('admin.exports.orders'));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));

        $contenu = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $contenu);
        $this->assertStringContainsString('Kofi Mensah', $contenu);
        $this->assertStringContainsString('20000', $contenu);
        $this->assertStringContainsString(';', $contenu);
    }

    public function test_un_nom_commencant_par_un_signe_de_formule_est_neutralise(): void
    {
        $this->commande('=HYPERLINK("http://exemple.test")');

        $contenu = $this->actingAs($this->admin())->get(route('admin.exports.orders'))->streamedContent();

        $this->assertStringContainsString("'=HYPERLINK", $contenu);
        $this->assertStringNotContainsString(';=HYPERLINK', $contenu);
    }

    public function test_lexport_des_indicateurs_contient_les_chiffres(): void
    {
        $this->commande('Kofi Mensah');

        $contenu = $this->actingAs($this->admin())
            ->get(route('admin.exports.indicators', ['mois' => now()->format('Y-m')]))
            ->streamedContent();

        $this->assertStringContainsString("Chiffre d'affaires", $contenu);
        $this->assertStringContainsString('20000', $contenu);
    }

    public function test_un_mois_invalide_retombe_sur_le_mois_courant(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.exports.orders', ['mois' => 'n-importe-quoi']))
            ->assertOk();
    }

    public function test_la_page_indicateurs_propose_les_exports(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.indicators.index'))
            ->assertSee('Exporter les indicateurs');
    }
}
