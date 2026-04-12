<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_tagihan_nomor_counters', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('bulan');
            $table->unsignedSmallInteger('tahun');
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();

            $table->unique(['bulan', 'tahun'], 'uniq_surat_tagihan_counter_bulan_tahun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_tagihan_nomor_counters');
    }
};