<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kredit_bulanan_fidusias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('jaminan_id')
                ->unique()
                ->constrained('kredit_bulanan_jaminans')
                ->cascadeOnDelete();

            $table->string('merk')->nullable();
            $table->string('type')->nullable();
            $table->string('warna')->nullable();
            $table->string('tahun')->nullable();

            $table->string('no_rangka')->nullable();
            $table->string('no_mesin')->nullable();
            $table->string('no_polisi')->nullable();
            $table->string('no_bpkb')->nullable();

            $table->string('atasnama')->nullable();
            $table->decimal('taksiran_harga', 18, 2)->default(0);
            $table->text('tempat_penyimpanan')->nullable();

            $table->timestamps();

            $table->index('merk');
            $table->index('no_polisi');
            $table->index('no_bpkb');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kredit_bulanan_fidusias');
    }
};