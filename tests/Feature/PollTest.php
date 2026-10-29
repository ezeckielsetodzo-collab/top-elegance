<?php

namespace Tests\Feature;

use App\Models\ColorVote;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PollTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(ProductSeeder::class);
    }

    private function produit(): Product
    {
        return Product::with('colors')->first();
    }

    public function test_un_visiteur_peut_voter(): void
    {
        $product = $this->produit();
        $couleur = $product->colors->first();

        $this->postJson(route('poll.vote'), ['modele' => $product->slug, 'couleur' => $couleur->name])
            ->assertOk()
            ->assertJson(['ok' => true, 'total' => 1])
            ->assertJsonPath('resultats.'.$couleur->name, 1);

        $this->assertSame(1, ColorVote::count());
    }

    public function test_on_ne_vote_quune_fois_par_modele(): void
    {
        $product = $this->produit();
        $couleurs = $product->colors;

        $this->postJson(route('poll.vote'), ['modele' => $product->slug, 'couleur' => $couleurs[0]->name])->assertOk();

        $this->postJson(route('poll.vote'), ['modele' => $product->slug, 'couleur' => $couleurs[1]->name])
            ->assertStatus(409)
            ->assertJson(['ok' => false]);

        $this->assertSame(1, ColorVote::count());
    }

    public function test_on_peut_voter_pour_un_autre_modele(): void
    {
        $produits = Product::with('colors')->take(2)->get();

        foreach ($produits as $product) {
            $this->postJson(route('poll.vote'), ['modele' => $product->slug, 'couleur' => $product->colors->first()->name])->assertOk();
        }

        $this->assertSame(2, ColorVote::count());
    }

    public function test_une_couleur_qui_nappartient_pas_au_modele_est_refusee(): void
    {
        $product = $this->produit();

        $this->postJson(route('poll.vote'), ['modele' => $product->slug, 'couleur' => 'Fuchsia'])->assertStatus(422);

        $this->assertSame(0, ColorVote::count());
    }

    public function test_un_modele_inconnu_est_refuse(): void
    {
        $this->postJson(route('poll.vote'), ['modele' => 'nexiste-pas', 'couleur' => 'Noir'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('modele');
    }

    public function test_le_piege_a_robots_bloque_le_vote(): void
    {
        $product = $this->produit();

        $this->postJson(route('poll.vote'), [
            'modele' => $product->slug, 'couleur' => $product->colors->first()->name, 'site_web' => 'http://spam.test',
        ])->assertStatus(422);

        $this->assertSame(0, ColorVote::count());
    }

    public function test_un_vote_par_formulaire_classique_revient_a_la_page(): void
    {
        $product = $this->produit();

        $this->from('/')->post(route('poll.vote'), ['modele' => $product->slug, 'couleur' => $product->colors->first()->name])
            ->assertRedirect('/')
            ->assertSessionHas('sondage_ok');
    }

    public function test_les_resultats_sont_publics(): void
    {
        $product = $this->produit();
        $couleur = $product->colors->first();
        $this->postJson(route('poll.vote'), ['modele' => $product->slug, 'couleur' => $couleur->name]);

        $this->getJson(route('poll.results', $product))
            ->assertOk()
            ->assertJson(['total' => 1])
            ->assertJsonPath('resultats.'.$couleur->name, 1);
    }

    public function test_ladmin_voit_les_resultats(): void
    {
        $product = $this->produit();
        $this->postJson(route('poll.vote'), ['modele' => $product->slug, 'couleur' => $product->colors->first()->name]);

        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->get(route('admin.polls.index'))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee('100 %');
    }

    public function test_un_visiteur_ne_voit_pas_ladmin_du_sondage(): void
    {
        $this->get(route('admin.polls.index'))->assertRedirect(route('admin.login'));
    }
}
