<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kredit_bulanan_jaminans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kredit_bulanan_id')
                ->constrained('kredit_bulanans')
                ->cascadeOnDelete();

            $table->text('keterangan_jaminan');
            $table->timestamps();

            $table->index('kredit_bulanan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kredit_bulanan_jaminans');
    }
};