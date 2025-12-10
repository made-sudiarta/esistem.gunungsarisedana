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
        Schema::create('simpanan_berjangkas', function (Blueprint $table) {
            $table->id();
            $table->string('kode_bilyet')->unique();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->date('tanggal_masuk');
            $table->integer('jangka_waktu'); // dalam bulan misalnya
            $table->decimal('bunga_persen', 5, 2);
            $table->decimal('nominal', 15, 2);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simpanan_berjangkas');
    }
};
