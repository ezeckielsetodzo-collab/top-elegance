<?php

namespace Tests\Feature;

use App\Models\Ambassador;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AmbassadorTest extends TestCase
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
            'quantite' => 2,
            'consentement' => '1',
            'cgv' => '1',
        ], $remplace);
    }

    public function test_un_visiteur_ne_peut_pas_gerer_les_ambassadeurs(): void
    {
        $this->get(route('admin.ambassadors.index'))->assertRedirect(route('admin.login'));
        $this->post(route('admin.ambassadors.store'), [])->assertRedirect(route('admin.login'));
    }

    public function test_ladministrateur_cree_un_ambassadeur(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.ambassadors.store'), [
                'name' => 'Jean', 'phone' => '90000000', 'code' => ' jean10 ',
                'discount_percent' => 10, 'commission_percent' => 8,
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('ambassadors', ['code' => 'JEAN10', 'commission_percent' => 8]);
    }

    public function test_un_code_en_double_est_refuse(): void
    {
        Ambassador::create(['name' => 'Jean', 'code' => 'JEAN10']);

        $this->actingAs($this->admin())
            ->post(route('admin.ambassadors.store'), [
                'name' => 'Autre', 'code' => 'jean10', 'discount_percent' => 0, 'commission_percent' => 10,
            ])
            ->assertSessionHasErrors('code');
    }

    public function test_une_commande_avec_code_est_attribuee_avec_reduction(): void
    {
        $ambassador = Ambassador::create(['name' => 'Jean', 'code' => 'JEAN10', 'discount_percent' => 10]);

        $this->post(route('orders.store'), $this->donnees(['code' => 'jean10']))
            ->assertRedirect(route('orders.confirmation'));

        $order = Order::firstOrFail();
        $this->assertSame($ambassador->id, $order->ambassador_id);
        $this->assertSame(2000, (int) $order->discount);
        $this->assertSame(18000, (int) $order->total);
    }

    public function test_une_commande_sans_code_na_pas_de_reduction(): void
    {
        $this->post(route('orders.store'), $this->donnees())->assertRedirect(route('orders.confirmation'));

        $order = Order::firstOrFail();
        $this->assertNull($order->ambassador_id);
        $this->assertSame(0, (int) $order->discount);
        $this->assertSame(20000, (int) $order->total);
    }

    public function test_un_code_inconnu_est_refuse(): void
    {
        $this->post(route('orders.store'), $this->donnees(['code' => 'FAUX99']))
            ->assertSessionHasErrors('code');

        $this->assertSame(0, Order::count());
    }

    public function test_un_code_desactive_est_refuse(): void
    {
        Ambassador::create(['name' => 'Jean', 'code' => 'JEAN10', 'active' => false]);

        $this->post(route('orders.store'), $this->donnees(['code' => 'JEAN10']))
            ->assertSessionHasErrors('code');
    }

    public function test_la_commission_du_mois_est_calculee_sans_les_commandes_annulees(): void
    {
        Ambassador::create(['name' => 'Jean', 'code' => 'JEAN10', 'commission_percent' => 10]);

        $this->post(route('orders.store'), $this->donnees(['code' => 'JEAN10', 'quantite' => 1]));
        $this->post(route('orders.store'), $this->donnees(['code' => 'JEAN10', 'quantite' => 1, 'telephone' => '90 99 99 99']));
        Order::latest('id')->first()->update(['status' => 'annulee']);

        $this->actingAs($this->admin())
            ->get(route('admin.ambassadors.index'))
            ->assertOk()
            ->assertSee('JEAN10')
            ->assertSee('1 000 FCFA');
    }

    public function test_ladministrateur_modifie_les_taux_et_desactive(): void
    {
        $ambassador = Ambassador::create(['name' => 'Jean', 'code' => 'JEAN10']);

        $this->actingAs($this->admin())
            ->patch(route('admin.ambassadors.update', $ambassador), [
                'discount_percent' => 5, 'commission_percent' => 12,
            ])
            ->assertSessionHasNoErrors();

        $ambassador->refresh();
        $this->assertSame(5, (int) $ambassador->discount_percent);
        $this->assertSame(12, (int) $ambassador->commission_percent);
        $this->assertFalse($ambassador->active);
    }
}
