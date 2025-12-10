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
        Schema::create('sukarelas', function (Blueprint $table) {
            $table->id();
            $table->integer('no_rek');
            $table->date('tanggal_terdaftar');
            $table->foreignId('group_id')->contrained('groups')->cascadeOnDelete();
            $table->foreignId('member_id')->contrained('members')->cascadeOnDelete();
            $table->float('saldo',15,2)->default(0);
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
        Schema::dropIfExists('sukarelas');
    }
};
