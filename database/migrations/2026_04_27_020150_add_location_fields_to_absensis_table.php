<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->decimal('latitude_masuk', 10, 7)->nullable()->after('jam_masuk');
            $table->decimal('longitude_masuk', 10, 7)->nullable()->after('latitude_masuk');
            $table->integer('jarak_masuk')->nullable()->after('longitude_masuk');

            $table->decimal('latitude_keluar', 10, 7)->nullable()->after('jam_keluar');
            $table->decimal('longitude_keluar', 10, 7)->nullable()->after('latitude_keluar');
            $table->integer('jarak_keluar')->nullable()->after('longitude_keluar');

            $table->string('sumber_absen')->nullable()->after('jarak_keluar');
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn([
                'latitude_masuk',
                'longitude_masuk',
                'jarak_masuk',
                'latitude_keluar',
                'longitude_keluar',
                'jarak_keluar',
                'sumber_absen',
            ]);
        });
    }
};