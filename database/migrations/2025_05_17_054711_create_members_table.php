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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->integer('nia');
            $table->char('nik')->nullable();
            $table->string('nama_lengkap');
            $table->string('tempat_lahir')->nullable();;
            $table->date('tanggal_lahir')->nullable();;
            $table->text('alamat')->nullable();;
            $table->string('no_hp')->nullable();;
            $table->foreignId('jenis_id')->contrained('jenis')->cascadeOnDelete();
            $table->date('tanggal_bergabung');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
