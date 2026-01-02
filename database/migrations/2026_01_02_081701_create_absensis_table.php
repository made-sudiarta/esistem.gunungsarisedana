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
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('tanggal');
            $table->time('jam_masuk');
            $table->time('jam_keluar')->nullable();

            $table->decimal('jumlah_jam', 5, 2)->nullable();
            $table->decimal('jumlah_setoran', 15, 2)->nullable();

            $table->timestamps();
            $table->softDeletes(); // 👈 INI
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
