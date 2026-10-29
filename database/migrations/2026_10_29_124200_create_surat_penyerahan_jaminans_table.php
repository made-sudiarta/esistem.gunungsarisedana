<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_penyerahan_jaminans', function (Blueprint $table) {
            $table->id();

            // Relasi ke data kredit yang sudah ada
            $table->foreignId('kredit_bulanan_id')
                ->constrained('kredit_bulanans')
                ->restrictOnDelete();

            // Identitas surat
            $table->string('nomor_surat')->unique();
            $table->date('tanggal_surat');
            $table->string('status_surat')->default('draft');

            // Identitas kendaraan
            $table->string('jenis_kendaraan')->default('Sepeda Motor');
            $table->string('merek_tipe')->nullable();
            $table->string('nomor_polisi')->nullable();
            $table->string('nomor_rangka')->nullable();
            $table->string('nomor_mesin')->nullable();
            $table->string('nomor_bpkb')->nullable();
            $table->string('nomor_stnk')->nullable();
            $table->string('atas_nama_dokumen')->nullable();

            // Kondisi dan kelengkapan serah terima
            $table->text('kondisi_kendaraan')->nullable();
            $table->text('kelengkapan_diserahkan')->nullable();
            $table->text('catatan')->nullable();

            // Waktu dan tempat serah terima
            $table->date('tanggal_serah_terima')->nullable();
            $table->time('waktu_serah_terima')->nullable();
            $table->text('tempat_serah_terima')->nullable();

            // Petugas/pihak yang menerima atas nama koperasi
            $table->string('nama_penerima')->nullable();
            $table->string('jabatan_penerima')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_penyerahan_jaminans');
    }
};