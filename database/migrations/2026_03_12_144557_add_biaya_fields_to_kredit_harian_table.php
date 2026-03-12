<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kredit_harian', function (Blueprint $table) {
            $table->string('jaminan')->nullable()->after('sisa_pokok');
            $table->decimal('prov_adm', 15, 2)->default(0)->after('jaminan');
            $table->decimal('materai', 15, 2)->default(0)->after('prov_adm');
            $table->decimal('op', 15, 2)->default(0)->after('materai');
            $table->decimal('kyd', 15, 2)->default(0)->after('op');
            $table->decimal('biaya_lain', 15, 2)->default(0)->after('kyd');
            $table->string('keterangan_biaya_lain')->nullable()->after('biaya_lain');
        });
    }

    public function down(): void
    {
        Schema::table('kredit_harian', function (Blueprint $table) {
            $table->dropColumn([
                'jaminan',
                'prov_adm',
                'materai',
                'op',
                'kyd',
                'biaya_lain',
                'keterangan_biaya_lain',
            ]);
        });
    }
};