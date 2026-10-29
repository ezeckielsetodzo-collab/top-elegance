<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('color_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('color_id')->constrained()->cascadeOnDelete();
            // Empreinte anonyme du visiteur : permet un seul vote par modèle sans stocker d'IP.
            $table->string('voter_hash', 64);
            $table->timestamps();

            $table->unique(['product_id', 'voter_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('color_votes');
    }
};
