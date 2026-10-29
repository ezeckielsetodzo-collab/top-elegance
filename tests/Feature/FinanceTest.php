<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\FinanceEntry;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceTest extends TestCase
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

    public function test_un_visiteur_est_renvoye_vers_la_connexion(): void
    {
        $this->get(route('admin.finances.index'))->assertRedirect(route('admin.login'));
        $this->post(route('admin.finances.store'), [])->assertRedirect(route('admin.login'));
    }

    public function test_la_page_finances_saffiche(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.finances.index'))
            ->assertOk()
            ->assertSee('Aucune écriture');
    }

    public function test_ladministrateur_ajoute_une_entree_et_une_sortie(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.finances.store'), [
            'entry_date' => '2026-10-01', 'label' => 'Apport', 'type' => 'entree', 'amount' => 100000,
        ])->assertSessionHasNoErrors();

        $this->actingAs($admin)->post(route('admin.finances.store'), [
            'entry_date' => '2026-10-02', 'label' => 'Achat de tissu', 'type' => 'sortie', 'amount' => 30000,
        ])->assertSessionHasNoErrors();

        $this->assertSame(2, FinanceEntry::count());

        $this->actingAs($admin)
            ->get(route('admin.finances.index'))
            ->assertSee('Achat de tissu')
            ->assertSee('70 000 FCFA');
    }

    public function test_un_montant_invalide_est_refuse(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.finances.store'), [
                'entry_date' => '2026-10-01', 'label' => 'Test', 'type' => 'entree', 'amount' => 0,
            ])
            ->assertSessionHasErrors('amount');

        $this->assertSame(0, FinanceEntry::count());
    }

    public function test_un_type_inconnu_est_refuse(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.finances.store'), [
                'entry_date' => '2026-10-01', 'label' => 'Test', 'type' => 'autre', 'amount' => 10,
            ])
            ->assertSessionHasErrors('type');
    }

    public function test_ladministrateur_supprime_une_ecriture(): void
    {
        $entry = FinanceEntry::create(['entry_date' => '2026-10-01', 'label' => 'Erreur', 'income' => 5, 'expense' => 0]);

        $this->actingAs($this->admin())
            ->delete(route('admin.finances.destroy', $entry))
            ->assertSessionHasNoErrors();

        $this->assertSame(0, FinanceEntry::count());
    }

    public function test_la_marge_par_produit_exclut_les_commandes_annulees(): void
    {
        $product = Product::with(['colors', 'sizes'])->first();
        $product->update(['price' => 15000, 'cost' => 6000]);
        $customer = Customer::create(['name' => 'Kofi', 'phone' => '90123456', 'consent' => true]);

        foreach ([['nouvelle', 2], ['annulee', 5]] as [$statut, $quantite]) {
            $order = Order::create([
                'customer_id' => $customer->id, 'status' => $statut,
                'total' => 15000 * $quantite, 'terms_accepted_at' => now(),
            ]);
            OrderItem::create([
                'order_id' => $order->id, 'product_id' => $product->id,
                'color_id' => $product->colors->first()->id, 'size_id' => $product->sizes->first()->id,
                'quantity' => $quantite, 'unit_price' => 15000,
            ]);
        }

        $this->actingAs($this->admin())
            ->get(route('admin.finances.index'))
            ->assertSee('9 000 FCFA')
            ->assertSee('18 000 FCFA')
            ->assertSee('60 %');
    }
}
