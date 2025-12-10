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
        Schema::create('trx_simpanan_pokoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->contrained('members')->cascadeOnDelete();
            $table->date('tanggal_trx');
            $table->float('debit',15,2)->default(0);
            $table->float('kredit',15,2)->default(0);
            // $table->integer('saldo')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_simpanan_pokoks');
    }
};
