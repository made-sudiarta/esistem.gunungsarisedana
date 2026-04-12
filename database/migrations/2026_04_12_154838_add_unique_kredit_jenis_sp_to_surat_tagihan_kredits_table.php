<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_tagihan_kredits', function (Blueprint $table) {
            $table->unique(['kredit_bulanan_id', 'jenis_sp'], 'uniq_kredit_jenis_sp');
        });
    }

    public function down(): void
    {
        Schema::table('surat_tagihan_kredits', function (Blueprint $table) {
            $table->dropUnique('uniq_kredit_jenis_sp');
        });
    }
};