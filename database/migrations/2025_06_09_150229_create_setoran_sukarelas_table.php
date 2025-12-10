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
        Schema::create('setoran_sukarelas', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_trx');
            $table->foreignId('setoran_id')->constrained('setorans')->cascadeOnDelete();
            $table->foreignId('sukarela_id')->constrained('sukarelas')->cascadeOnDelete();
            $table->float('jumlah',15,2)->default(0);
            $table->text('keterangan')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setoran_sukarelas');
    }
};
