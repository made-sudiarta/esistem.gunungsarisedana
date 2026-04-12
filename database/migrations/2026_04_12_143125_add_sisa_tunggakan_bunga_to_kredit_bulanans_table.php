<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('kredit_bulanans', function (Blueprint $table) {
            $table->decimal('sisa_tunggakan_bunga', 15, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('kredit_bulanans', function (Blueprint $table) {
            $table->dropColumn('sisa_tunggakan_bunga');
        });
    }
};