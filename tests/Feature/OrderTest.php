<?php

namespace Tests\Feature;

use App\Models\Color;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ProductSeeder::class);
    }

    private function ouvert(): Product
    {
        return Product::with(['colors', 'sizes'])->where('preorder_open', true)->firstOrFail();
    }

    private function ferme(): Product
    {
        return Product::with(['colors', 'sizes'])->where('preorder_open', false)->firstOrFail();
    }

    private function donnees(Product $product, array $remplace = []): array
    {
        return array_merge([
            'name' => 'Kofi Mensah',
            'phone' => '+228 90 12 34 56',
            'email' => 'kofi@example.com',
            'city' => 'Lomé',
            'product_id' => $product->id,
            'color_id' => $product->colors->first()->id,
            'size_id' => $product->sizes->first()->id,
            'quantity' => 2,
            'consent' => 'on',
            'terms' => 'on',
        ], $remplace);
    }

    public function test_une_precommande_valide_est_enregistree(): void
    {
        $product = $this->ouvert();

        $this->post(route('orders.store'), $this->donnees($product, ['price' => 1]))
            ->assertRedirect(route('orders.confirmation'));

        $order = Order::with('items')->firstOrFail();

        $this->assertSame('nouvelle', $order->status);
        $this->assertNotNull($order->terms_accepted_at);
        $this->assertSame($product->price * 2, $order->total);
        $this->assertCount(1, $order->items);
        $this->assertSame($product->price, $order->items->first()->unit_price);

        $customer = Customer::firstOrFail();
        $this->assertTrue($customer->consent);
        $this->assertNotNull($customer->consented_at);
    }

    public function test_sans_consentement_le_client_est_enregistre_sans_accord(): void
    {
        $product = $this->ouvert();
        $donnees = $this->donnees($product);
        unset($donnees['consent']);

        $this->post(route('orders.store'), $donnees)->assertRedirect(route('orders.confirmation'));

        $customer = Customer::firstOrFail();
        $this->assertFalse($customer->consent);
        $this->assertNull($customer->consented_at);
    }

    public function test_un_modele_bientot_disponible_est_refuse(): void
    {
        $this->post(route('orders.store'), $this->donnees($this->ferme()))
            ->assertSessionHasErrors('product_id');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_les_conditions_de_vente_sont_obligatoires(): void
    {
        $donnees = $this->donnees($this->ouvert());
        unset($donnees['terms']);

        $this->post(route('orders.store'), $donnees)->assertSessionHasErrors('terms');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_une_couleur_qui_nappartient_pas_au_modele_est_refusee(): void
    {
        $product = $this->ouvert();
        $autre = Color::whereNotIn('id', $product->colors->pluck('id'))->firstOrFail();

        $this->post(route('orders.store'), $this->donnees($product, ['color_id' => $autre->id]))
            ->assertSessionHasErrors('color_id');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_un_telephone_invalide_est_refuse(): void
    {
        $this->post(route('orders.store'), $this->donnees($this->ouvert(), ['phone' => 'abc']))
            ->assertSessionHasErrors('phone');
    }

    public function test_le_meme_telephone_ne_cree_pas_de_doublon_de_client(): void
    {
        $product = $this->ouvert();

        $this->post(route('orders.store'), $this->donnees($product));
        $this->post(route('orders.store'), $this->donnees($product, ['quantity' => 1]));

        $this->assertDatabaseCount('customers', 1);
        $this->assertDatabaseCount('orders', 2);
    }

    public function test_la_confirmation_sans_commande_renvoie_a_la_collection(): void
    {
        $this->get(route('orders.confirmation'))->assertRedirect(route('products.index'));
    }
}