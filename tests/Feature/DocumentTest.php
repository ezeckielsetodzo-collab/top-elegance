<?php

namespace Tests\Feature;

use App\Models\Ambassador;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentTest extends TestCase
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

    private function commande(?Ambassador $ambassador = null): Order
    {
        $product = Product::with(['colors', 'sizes'])->first();
        $client = Customer::create(['name' => 'Kofi Mensah', 'phone' => '90123456', 'consent' => true]);
        $order = Order::create([
            'customer_id' => $client->id,
            'ambassador_id' => $ambassador?->id,
            'status' => 'nouvelle',
            'delivery_address' => 'Tokoin, Lomé',
            'total' => 18000,
            'discount' => 2000,
            'terms_accepted_at' => now(),
        ]);
        OrderItem::create([
            'order_id' => $order->id, 'product_id' => $product->id,
            'color_id' => $product->colors->first()->id, 'size_id' => $product->sizes->first()->id,
            'quantity' => 2, 'unit_price' => 10000,
        ]);

        return $order;
    }

    public function test_un_visiteur_ne_voit_aucun_document(): void
    {
        $order = $this->commande();
        $ambassador = Ambassador::create(['name' => 'Jean', 'code' => 'JEAN10']);

        $this->get(route('admin.documents.order', $order))->assertRedirect(route('admin.login'));
        $this->get(route('admin.documents.delivery', $order))->assertRedirect(route('admin.login'));
        $this->get(route('admin.documents.ambassador', $ambassador))->assertRedirect(route('admin.login'));
    }

    public function test_le_bon_de_commande_saffiche(): void
    {
        $ambassador = Ambassador::create(['name' => 'Jean', 'code' => 'JEAN10']);
        $order = $this->commande($ambassador);

        $this->actingAs($this->admin())
            ->get(route('admin.documents.order', $order))
            ->assertOk()
            ->assertSee('Bon de commande')
            ->assertSee('Kofi Mensah')
            ->assertSee('Tokoin, Lomé')
            ->assertSee('18 000 FCFA')
            ->assertSee('JEAN10');
    }

    public function test_la_fiche_de_livraison_saffiche(): void
    {
        $order = $this->commande();

        $this->actingAs($this->admin())
            ->get(route('admin.documents.delivery', $order))
            ->assertOk()
            ->assertSee('Fiche de livraison')
            ->assertSee('Tokoin, Lomé')
            ->assertSee('18 000 FCFA');
    }

    public function test_la_fiche_de_livraison_dune_commande_payee_ne_demande_pas_dargent(): void
    {
        $order = $this->commande();
        $order->update(['status' => 'payee']);

        $this->actingAs($this->admin())
            ->get(route('admin.documents.delivery', $order))
            ->assertSee('Déjà payé')
            ->assertDontSee('18 000 FCFA');
    }

    public function test_la_fiche_ambassadeur_calcule_la_commission(): void
    {
        $ambassador = Ambassador::create(['name' => 'Jean', 'code' => 'JEAN10', 'commission_percent' => 10]);
        $this->commande($ambassador);

        $this->actingAs($this->admin())
            ->get(route('admin.documents.ambassador', $ambassador))
            ->assertOk()
            ->assertSee('Fiche ambassadeur')
            ->assertSee('Kofi Mensah')
            ->assertSee('1 800 FCFA');
    }

    public function test_une_commande_inconnue_renvoie_404(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/commandes/9999/bon')
            ->assertNotFound();
    }
}
