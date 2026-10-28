<?php

namespace Tests\Feature;

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

        $this->withoutVite();
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
            'nom' => 'Kofi Mensah',
            'telephone' => '+228 90 12 34 56',
            'quartier' => 'Tokoin, Lomé',
            'modele' => $product->slug,
            'couleur' => $product->colors->first()->name,
            'taille' => $product->sizes->first()->name,
            'quantite' => 2,
            'consentement' => '1',
            'cgv' => '1',
        ], $remplace);
    }

    public function test_le_formulaire_saffiche_avec_les_modeles_ouverts(): void
    {
        $this->get(route('orders.create'))
            ->assertOk()
            ->assertSee('TE Signature')
            ->assertSee('Black Luxury')
            ->assertSee('228 Edition')
            ->assertDontSee('Lomé Edition');
    }

    public function test_une_precommande_valide_est_enregistree(): void
    {
        $product = $this->ouvert();

        $this->post(route('orders.store'), $this->donnees($product, ['prix' => 1]))
            ->assertRedirect(route('orders.confirmation'));

        $order = Order::with('items')->firstOrFail();

        $this->assertSame('nouvelle', $order->status);
        $this->assertSame('Tokoin, Lomé', $order->delivery_address);
        $this->assertNotNull($order->terms_accepted_at);
        $this->assertSame($product->price * 2, $order->total);
        $this->assertCount(1, $order->items);
        $this->assertSame($product->price, $order->items->first()->unit_price);

        $customer = Customer::firstOrFail();
        $this->assertSame('+22890123456', $customer->phone);
        $this->assertTrue($customer->consent);
        $this->assertNotNull($customer->consented_at);
    }

    public function test_la_page_de_confirmation_affiche_la_commande(): void
    {
        $product = $this->ouvert();

        $this->followingRedirects()
            ->post(route('orders.store'), $this->donnees($product))
            ->assertOk()
            ->assertSee('Merci Kofi Mensah')
            ->assertSee($product->name)
            ->assertSee('Tokoin, Lomé')
            ->assertSee('wa.me', false);
    }

    public function test_un_modele_bientot_disponible_est_refuse(): void
    {
        $this->post(route('orders.store'), $this->donnees($this->ferme()))
            ->assertSessionHasErrors('modele');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_les_conditions_de_vente_sont_obligatoires(): void
    {
        $donnees = $this->donnees($this->ouvert());
        unset($donnees['cgv']);

        $this->post(route('orders.store'), $donnees)->assertSessionHasErrors('cgv');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_le_consentement_est_obligatoire(): void
    {
        $donnees = $this->donnees($this->ouvert());
        unset($donnees['consentement']);

        $this->post(route('orders.store'), $donnees)->assertSessionHasErrors('consentement');

        $this->assertDatabaseCount('customers', 0);
    }

    public function test_une_couleur_qui_nappartient_pas_au_modele_est_refusee(): void
    {
        $product = $this->ouvert();

        $this->post(route('orders.store'), $this->donnees($product, ['couleur' => 'Doré inconnue']))
            ->assertSessionHasErrors('couleur');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_une_taille_inconnue_est_refusee(): void
    {
        $this->post(route('orders.store'), $this->donnees($this->ouvert(), ['taille' => 'XXXL']))
            ->assertSessionHasErrors('taille');
    }

    public function test_un_telephone_invalide_est_refuse(): void
    {
        $this->post(route('orders.store'), $this->donnees($this->ouvert(), ['telephone' => 'abc']))
            ->assertSessionHasErrors('telephone');
    }

    public function test_le_piege_a_robots_bloque_la_commande(): void
    {
        $this->post(route('orders.store'), $this->donnees($this->ouvert(), ['site_web' => 'http://spam.test']))
            ->assertSessionHasErrors('site_web');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_le_meme_telephone_ne_cree_pas_de_doublon_de_client(): void
    {
        $product = $this->ouvert();

        $this->post(route('orders.store'), $this->donnees($product));
        $this->post(route('orders.store'), $this->donnees($product, ['quantite' => 1, 'telephone' => '90123456']));

        $this->assertDatabaseCount('orders', 2);
        $this->assertDatabaseCount('customers', 2);
    }

    public function test_le_meme_telephone_ecrit_pareil_ne_cree_pas_de_doublon(): void
    {
        $product = $this->ouvert();

        $this->post(route('orders.store'), $this->donnees($product));
        $this->post(route('orders.store'), $this->donnees($product, ['telephone' => '+228-90-12-34-56']));

        $this->assertDatabaseCount('customers', 1);
        $this->assertDatabaseCount('orders', 2);
    }

    public function test_la_confirmation_sans_commande_renvoie_a_la_collection(): void
    {
        $this->get(route('orders.confirmation'))->assertRedirect(route('products.index'));
    }
}
