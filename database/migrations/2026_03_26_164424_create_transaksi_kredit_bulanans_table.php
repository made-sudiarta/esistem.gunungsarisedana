<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_kredit_bulanans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kredit_bulanan_id')
                ->constrained('kredit_bulanans')
                ->cascadeOnDelete();

            $table->date('tanggal_transaksi');
            $table->unsignedInteger('angsuran_ke')->nullable();

            $table->decimal('nominal_bayar', 18, 2)->default(0);
            $table->decimal('denda', 18, 2)->default(0);
            $table->text('keterangan')->nullable();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('kredit_bulanan_id');
            $table->index('tanggal_transaksi');
            $table->index('angsuran_ke');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_kredit_bulanans');
    }
};