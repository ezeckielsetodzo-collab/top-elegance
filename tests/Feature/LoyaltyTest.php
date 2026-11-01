<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyTest extends TestCase
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

    private function donnees(array $remplace = []): array
    {
        $product = Product::with(['colors', 'sizes'])->where('preorder_open', true)->firstOrFail();
        $product->update(['price' => 10000]);

        return array_merge([
            'nom' => 'Kofi Mensah',
            'telephone' => '+228 90 12 34 56',
            'quartier' => 'Tokoin, Lomé',
            'modele' => $product->slug,
            'couleur' => $product->colors->first()->name,
            'taille' => $product->sizes->first()->name,
            'quantite' => 1,
            'consentement' => '1',
            'cgv' => '1',
        ], $remplace);
    }

    private function achats(Customer $client, int $nombre, string $statut = 'livree'): void
    {
        for ($i = 0; $i < $nombre; $i++) {
            Order::create([
                'customer_id' => $client->id, 'status' => $statut, 'total' => 10000, 'terms_accepted_at' => now(),
            ]);
        }
    }

    public function test_un_client_recoit_un_code_de_parrainage_a_sa_premiere_commande(): void
    {
        $this->post(route('orders.store'), $this->donnees())->assertRedirect(route('orders.confirmation'));

        $client = Customer::firstOrFail();
        $this->assertMatchesRegularExpression('/^P[A-Z2-9]{5}$/', $client->referral_code);
    }

    public function test_un_nouveau_client_peut_etre_parraine(): void
    {
        $parrain = Customer::create(['name' => 'Ama', 'phone' => '90000001', 'consent' => true, 'referral_code' => 'PAMA22']);

        $this->post(route('orders.store'), $this->donnees(['parrain' => 'pama22']))
            ->assertRedirect(route('orders.confirmation'));

        $filleul = Customer::where('phone', '+22890123456')->firstOrFail();
        $this->assertSame($parrain->id, $filleul->referred_by);
        $this->assertSame(1, $parrain->referrals()->count());
    }

    public function test_un_code_de_parrainage_inconnu_est_refuse(): void
    {
        $this->post(route('orders.store'), $this->donnees(['parrain' => 'PXXXXX']))
            ->assertSessionHasErrors('parrain');

        $this->assertSame(0, Order::count());
    }

    public function test_on_ne_peut_pas_se_parrainer_soi_meme(): void
    {
        Customer::create(['name' => 'Kofi', 'phone' => '+22890123456', 'consent' => true, 'referral_code' => 'PKOFI2']);

        $this->post(route('orders.store'), $this->donnees(['parrain' => 'PKOFI2']))
            ->assertSessionHasErrors('parrain');
    }

    public function test_un_client_existant_ne_peut_pas_etre_parraine_apres_coup(): void
    {
        Customer::create(['name' => 'Ama', 'phone' => '90000001', 'consent' => true, 'referral_code' => 'PAMA22']);
        $deja = Customer::create(['name' => 'Kofi', 'phone' => '+22890123456', 'consent' => true, 'referral_code' => 'PKOFI2']);

        $this->post(route('orders.store'), $this->donnees(['parrain' => 'PAMA22']))
            ->assertRedirect(route('orders.confirmation'));

        $this->assertNull($deja->fresh()->referred_by);
        $this->assertSame('PKOFI2', $deja->fresh()->referral_code);
    }

    public function test_la_confirmation_donne_le_code_de_parrainage(): void
    {
        $this->post(route('orders.store'), $this->donnees());

        $this->get(route('orders.confirmation'))->assertOk();
        $this->assertNotEmpty(Customer::first()->referral_code);
    }

    public function test_un_client_devient_fidele_apres_trois_achats(): void
    {
        $fidele = Customer::create(['name' => 'Fidele Un', 'phone' => '90000010', 'consent' => true]);
        $this->achats($fidele, 3);
        $debutant = Customer::create(['name' => 'Debutant Deux', 'phone' => '90000011', 'consent' => true]);
        $this->achats($debutant, 2);
        $this->achats($debutant, 5, 'nouvelle');

        $this->actingAs($this->admin())
            ->get(route('admin.customers.index'))
            ->assertOk()
            ->assertSee('Fidele Un')
            ->assertSee('Fidèle');

        $this->actingAs($this->admin())
            ->get(route('admin.customers.index', ['filtre' => 'fideles']))
            ->assertSee('Fidele Un')
            ->assertDontSee('Debutant Deux');
    }

    public function test_un_visiteur_ne_voit_pas_les_clients(): void
    {
        $this->get(route('admin.customers.index'))->assertRedirect(route('admin.login'));
    }
}
