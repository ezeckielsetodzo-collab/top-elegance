<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ambassadors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('phone', 30)->nullable();
            $table->string('code', 30)->unique();
            $table->unsignedTinyInteger('discount_percent')->default(0);
            $table->unsignedTinyInteger('commission_percent')->default(10);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('ambassador_id')->nullable()->after('customer_id')->constrained()->nullOnDelete();
            $table->unsignedInteger('discount')->default(0)->after('total');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ambassador_id');
            $table->dropColumn('discount');
        });

        Schema::dropIfExists('ambassadors');
    }
};
