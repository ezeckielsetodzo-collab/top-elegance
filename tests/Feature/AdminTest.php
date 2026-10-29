<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function creerCommande(): Order
    {
        $this->seed(ProductSeeder::class);
        $product = Product::with(['colors', 'sizes'])->where('preorder_open', true)->firstOrFail();

        $this->post(route('orders.store'), [
            'nom' => 'Kofi Mensah',
            'telephone' => '90123456',
            'quartier' => 'Tokoin, Lomé',
            'modele' => $product->slug,
            'couleur' => $product->colors->first()->name,
            'taille' => $product->sizes->first()->name,
            'quantite' => 1,
            'consentement' => '1',
            'cgv' => '1',
        ]);

        return Order::firstOrFail();
    }

    public function test_un_visiteur_est_renvoye_vers_la_connexion(): void
    {
        $this->get(route('admin.orders.index'))->assertRedirect(route('admin.login'));
    }

    public function test_la_page_de_connexion_saffiche(): void
    {
        $this->get(route('admin.login'))->assertOk()->assertSee('Espace administrateur');
    }

    public function test_un_administrateur_peut_se_connecter(): void
    {
        $admin = $this->admin();

        $this->post(route('admin.login.store'), ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('admin.orders.index'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_un_mauvais_mot_de_passe_est_refuse(): void
    {
        $admin = $this->admin();

        $this->post(route('admin.login.store'), ['email' => $admin->email, 'password' => 'faux'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_un_utilisateur_non_administrateur_ne_peut_pas_se_connecter(): void
    {
        $user = User::factory()->create();

        $this->post(route('admin.login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_un_utilisateur_connecte_non_administrateur_est_refuse(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.orders.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_ladministrateur_voit_les_commandes(): void
    {
        $order = $this->creerCommande();

        $this->actingAs($this->admin())
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee('Kofi Mensah')
            ->assertSee('90123456')
            ->assertSee('Tokoin, Lomé')
            ->assertSee('10 000 FCFA')
            ->assertSee('Nouvelle');

        $this->assertSame('nouvelle', $order->status);
    }

    public function test_le_filtre_par_statut_fonctionne(): void
    {
        $this->creerCommande();

        $this->actingAs($this->admin())
            ->get(route('admin.orders.index', ['statut' => 'livree']))
            ->assertOk()
            ->assertSee('Aucune commande pour le moment.')
            ->assertDontSee('Kofi Mensah');
    }

    public function test_ladministrateur_peut_changer_le_statut(): void
    {
        $order = $this->creerCommande();

        $this->actingAs($this->admin())
            ->patch(route('admin.orders.update', $order), ['status' => 'confirmee'])
            ->assertSessionHas('success');

        $this->assertSame('confirmee', $order->fresh()->status);
    }

    public function test_un_statut_inconnu_est_refuse(): void
    {
        $order = $this->creerCommande();

        $this->actingAs($this->admin())
            ->patch(route('admin.orders.update', $order), ['status' => 'nimporte-quoi'])
            ->assertSessionHasErrors('status');

        $this->assertSame('nouvelle', $order->fresh()->status);
    }

    public function test_un_visiteur_ne_peut_pas_changer_un_statut(): void
    {
        $order = $this->creerCommande();

        $this->patch(route('admin.orders.update', $order), ['status' => 'payee'])
            ->assertRedirect(route('admin.login'));

        $this->assertSame('nouvelle', $order->fresh()->status);
    }

    public function test_la_deconnexion_fonctionne(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.logout'))
            ->assertRedirect(route('admin.login'));

        $this->assertGuest();
    }

    public function test_le_seeder_cree_un_administrateur(): void
    {
        config(['shop.admin_email' => 'admin@topelegance.test', 'shop.admin_password' => 'secret-123']);

        $this->seed(AdminSeeder::class);

        $user = User::where('email', 'admin@topelegance.test')->firstOrFail();
        $this->assertTrue((bool) $user->is_admin);

        $this->post(route('admin.login.store'), ['email' => 'admin@topelegance.test', 'password' => 'secret-123'])
            ->assertRedirect(route('admin.orders.index'));
    }
}