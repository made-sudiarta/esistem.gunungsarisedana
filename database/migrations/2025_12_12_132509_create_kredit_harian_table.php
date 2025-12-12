<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKreditHarianTable extends Migration
{
    public function up(): void
    {
        Schema::create('kredit_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->string('nama_lengkap');
            $table->text('alamat');
            $table->string('no_hp')->nullable();
            $table->date('tanggal_pengajuan');
            $table->integer('jangka_waktu'); // dalam hari
            $table->date('tanggal_jatuhtempo');
            $table->decimal('plafond', 15, 2);
            $table->decimal('bunga_persen', 5, 2);
            $table->decimal('admin_persen', 5, 2);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kredit_harian');
    }
}
