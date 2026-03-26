<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kredit_bulanan_penanggung_jawabs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kredit_bulanan_id')
                ->unique()
                ->constrained('kredit_bulanans')
                ->cascadeOnDelete();

            $table->char('nik')->nullable();
            $table->string('nama');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();

            $table->timestamps();

            $table->index('nama');
            $table->index('nik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kredit_bulanan_penanggung_jawabs');
    }
};