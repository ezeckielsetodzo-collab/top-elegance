<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockTest extends TestCase
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
        $product = Product::first();

        $this->get(route('admin.stock.index'))->assertRedirect(route('admin.login'));
        $this->put(route('admin.stock.update', $product), [])->assertRedirect(route('admin.login'));
    }

    public function test_la_page_stock_saffiche(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.stock.index'))
            ->assertOk()
            ->assertSee('Stock');
    }

    public function test_la_grille_de_stock_dun_modele_saffiche(): void
    {
        $product = Product::first();

        $this->actingAs($this->admin())
            ->get(route('admin.stock.edit', $product))
            ->assertOk()
            ->assertSee($product->name);
    }

    public function test_ladministrateur_enregistre_des_quantites(): void
    {
        $product = Product::with(['colors', 'sizes'])->first();
        $color = $product->colors->first();
        $size = $product->sizes->first();

        $this->actingAs($this->admin())
            ->put(route('admin.stock.update', $product), [
                'quantites' => [$color->id => [$size->id => '12']],
            ])
            ->assertRedirect(route('admin.stock.index'));

        $this->assertSame(12, Stock::where([
            'product_id' => $product->id, 'color_id' => $color->id, 'size_id' => $size->id,
        ])->value('quantity'));

        $this->actingAs($this->admin())
            ->put(route('admin.stock.update', $product), [
                'quantites' => [$color->id => [$size->id => '5']],
            ]);

        $this->assertSame(1, Stock::where('product_id', $product->id)->count());
        $this->assertSame(5, (int) Stock::where('product_id', $product->id)->value('quantity'));
    }

    public function test_une_quantite_negative_est_refusee(): void
    {
        $product = Product::with(['colors', 'sizes'])->first();
        $color = $product->colors->first();
        $size = $product->sizes->first();

        $this->actingAs($this->admin())
            ->put(route('admin.stock.update', $product), [
                'quantites' => [$color->id => [$size->id => '-3']],
            ])
            ->assertSessionHasErrors();

        $this->assertSame(0, Stock::count());
    }

    public function test_un_stock_sans_commande_recente_est_dormant(): void
    {
        $product = Product::with(['colors', 'sizes'])->first();

        Stock::create([
            'product_id' => $product->id,
            'color_id' => $product->colors->first()->id,
            'size_id' => $product->sizes->first()->id,
            'quantity' => 8,
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.stock.index'))
            ->assertSee('Stock dormant');
    }

    public function test_un_stock_avec_commande_recente_nest_pas_dormant(): void
    {
        $product = Product::with(['colors', 'sizes'])->first();
        $color = $product->colors->first();
        $size = $product->sizes->first();

        Stock::create([
            'product_id' => $product->id, 'color_id' => $color->id, 'size_id' => $size->id, 'quantity' => 8,
        ]);

        $customer = Customer::create(['name' => 'Kofi', 'phone' => '90123456', 'consent' => true]);
        $order = Order::create(['customer_id' => $customer->id, 'status' => 'nouvelle', 'total' => 10000, 'terms_accepted_at' => now()]);
        OrderItem::create([
            'order_id' => $order->id, 'product_id' => $product->id, 'color_id' => $color->id,
            'size_id' => $size->id, 'quantity' => 1, 'unit_price' => 10000,
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.stock.index'))
            ->assertDontSee('Stock dormant');
    }
}
