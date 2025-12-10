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
        Schema::create('setorans', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_trx');
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->float('sukarela_total',15,2)->default(0);
            $table->float('sinbeswa_total',15,2)->default(0);
            $table->float('pokok_total',15,2)->default(0);
            $table->float('penyerta_total',15,2)->default(0);
            $table->float('wajib_total',15,2)->default(0);
            $table->float('acrh_total',15,2)->default(0);
            $table->enum('status',['0','1']);
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
        Schema::dropIfExists('setorans');
    }
};
