<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kredit_bulanans', function (Blueprint $table) {
            $table->dropColumn([
                'total_tagihan',
                'angsuran_per_bulan',
                'sisa_pokok',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('kredit_bulanans', function (Blueprint $table) {
            $table->decimal('total_tagihan', 18, 2)->default(0);
            $table->decimal('angsuran_per_bulan', 18, 2)->default(0);
            $table->decimal('sisa_pokok', 18, 2)->default(0);
        });
    }
};