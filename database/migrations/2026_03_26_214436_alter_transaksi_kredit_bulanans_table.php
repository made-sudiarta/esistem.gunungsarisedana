<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_kredit_bulanans', function (Blueprint $table) {
            $table->dropColumn('angsuran_ke');

            $table->decimal('saldo_awal', 18, 2)->default(0)->after('tanggal_transaksi');
            $table->decimal('pokok', 18, 2)->default(0)->after('saldo_awal');
            $table->decimal('bunga', 18, 2)->default(0)->after('pokok');
            $table->decimal('sisa_saldo', 18, 2)->default(0)->after('denda');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_kredit_bulanans', function (Blueprint $table) {
            $table->unsignedInteger('angsuran_ke')->nullable()->after('tanggal_transaksi');

            $table->dropColumn([
                'saldo_awal',
                'pokok',
                'bunga',
                'sisa_saldo',
            ]);
        });
    }
};