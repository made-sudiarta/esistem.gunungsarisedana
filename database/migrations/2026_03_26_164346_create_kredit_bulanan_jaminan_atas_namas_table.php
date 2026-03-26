<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kredit_bulanan_jaminan_atas_namas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('jaminan_id')
                ->constrained('kredit_bulanan_jaminans')
                ->cascadeOnDelete();

            $table->string('atas_nama');
            $table->timestamps();

            $table->index('jaminan_id');
            $table->index('atas_nama');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kredit_bulanan_jaminan_atas_namas');
    }
};