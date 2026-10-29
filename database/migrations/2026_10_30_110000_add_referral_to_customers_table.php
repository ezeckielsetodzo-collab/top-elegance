<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('referral_code', 12)->nullable()->unique()->after('consented_at');
            $table->foreignId('referred_by')->nullable()->after('referral_code')->constrained('customers')->nullOnDelete();
        });

        // Les clients déjà enregistrés reçoivent leur code de parrainage.
        foreach (DB::table('customers')->whereNull('referral_code')->pluck('id') as $id) {
            do {
                $code = 'P'.strtoupper(substr(str_replace(['0', 'O', '1', 'I', 'L'], '', bin2hex(random_bytes(8))), 0, 5));
            } while (strlen($code) < 6 || DB::table('customers')->where('referral_code', $code)->exists());

            DB::table('customers')->where('id', $id)->update(['referral_code' => $code]);
        }
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('referred_by');
            $table->dropUnique(['referral_code']);
            $table->dropColumn('referral_code');
        });
    }
};
