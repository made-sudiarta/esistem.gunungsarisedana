<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kredit_bulanans', function (Blueprint $table) {
            $table->id();

            // FK ke members
            $table->foreignId('member_id')
                ->constrained('members')
                ->cascadeOnDelete();

            // FK ke groups
            $table->foreignId('group_id')
                ->nullable()
                ->constrained('groups')
                ->nullOnDelete();

            $table->string('no_pokok')->unique();

            $table->date('tanggal_pengajuan');
            $table->unsignedInteger('jangka_waktu')->comment('Dalam bulan');
            $table->date('tanggal_jatuh_tempo')->nullable();

            $table->decimal('plafond', 18, 2)->default(0);
            $table->decimal('bunga_persen', 8, 2)->default(0);

            $table->text('tujuan_pinjaman')->nullable();

            $table->decimal('biaya_adm_persen', 8, 2)->default(0);
            $table->decimal('biaya_provisi_persen', 8, 2)->default(0);
            $table->decimal('biaya_op_persen', 8, 2)->default(0);

            $table->decimal('biaya_kyd', 18, 2)->default(0);
            $table->decimal('biaya_materai', 18, 2)->default(0);
            $table->decimal('biaya_asuransi', 18, 2)->default(0);
            $table->decimal('biaya_lain', 18, 2)->default(0);
            $table->text('keterangan_biaya_lain')->nullable();

            $table->decimal('total_tagihan', 18, 2)->default(0);
            $table->decimal('angsuran_per_bulan', 18, 2)->default(0);
            $table->decimal('sisa_pokok', 18, 2)->default(0);

            $table->enum('status', ['aktif', 'lunas', 'macet', 'jatuh_tempo'])->default('aktif');

            $table->softDeletes();
            $table->timestamps();

            $table->index('member_id');
            $table->index('group_id');
            $table->index('tanggal_pengajuan');
            $table->index('tanggal_jatuh_tempo');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kredit_bulanans');
    }
};