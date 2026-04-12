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
        Schema::create('surat_tagihan_kredits', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kredit_bulanan_id')
                ->constrained('kredit_bulanans')
                ->cascadeOnDelete();

            $table->enum('jenis_sp', ['SP1', 'SP2', 'SP3']);
            $table->string('nomor_surat')->unique();
            $table->string('no_pokok');

            $table->unsignedInteger('jumlah_tunggakan_bunga')->default(0);
            $table->decimal('sisa_tunggakan_bunga', 18, 2)->default(0);
            $table->decimal('bunga_per_bulan', 18, 2)->default(0);
            $table->decimal('total_tunggakan_bunga', 18, 2)->default(0);
            $table->decimal('sisa_pokok_kredit', 18, 2)->default(0);

            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->date('tanggal_surat');

            $table->string('status_surat')->default('draft');
            $table->text('keterangan')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_tagihan_kredits');
    }
};