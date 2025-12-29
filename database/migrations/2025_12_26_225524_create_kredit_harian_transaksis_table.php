<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kredit_harian_transaksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kredit_harian_id')->constrained('kredit_harian')->cascadeOnDelete();
            $table->datetime('tanggal_transaksi');
            $table->decimal('jumlah', 15, 2);
            // $table->decimal('sisa_pokok', 15, 2);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kredit_harian_transaksis');
    }
};
