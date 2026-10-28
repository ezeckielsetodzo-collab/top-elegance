<?php

namespace Tests\Feature;

use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(ProductSeeder::class);
    }

    public function test_laccueil_saffiche(): void
    {
        $this->get(route('home'))->assertOk();
    }

    public function test_le_compte_a_rebours_utilise_la_date_du_drop(): void
    {
        config(['shop.drop_date' => '2026-12-01T18:00:00Z']);

        $this->get(route('home'))->assertOk()->assertSee('2026-12-01T18:00:00Z', false);
    }

    public function test_la_date_du_drop_a_une_valeur_par_defaut_si_env_est_vide(): void
    {
        $this->assertNotEmpty(config('shop.drop_date'));
    }

    public function test_la_collection_affiche_les_dix_modeles(): void
    {
        $response = $this->get(route('products.index'))->assertOk();

        foreach (['TE Signature', 'Lomé Edition', 'Black Luxury', 'Oversize Royal', 'African Elegance', 'TE 01', 'No Rules', 'Minimal TE', '228 Edition', 'Founder Edition'] as $nom) {
            $response->assertSee($nom);
        }
    }

    public function test_une_fiche_produit_saffiche(): void
    {
        $this->get(route('products.show', '228-edition'))
            ->assertOk()
            ->assertSee('228 Edition')
            ->assertSee('Togo, unité, fierté');
    }

    public function test_un_modele_inconnu_renvoie_404(): void
    {
        $this->get('/modeles/nexiste-pas')->assertNotFound();
    }

    public function test_les_pages_editoriales_saffichent(): void
    {
        $this->get(route('brand'))->assertOk();
        $this->get(route('terms'))->assertOk();
    }
}
