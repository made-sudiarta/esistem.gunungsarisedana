<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE surat_tagihan_kredits
            MODIFY COLUMN jenis_sp
            ENUM('SP1', 'SP2', 'SP3', 'SOMASI')
            NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE surat_tagihan_kredits
            MODIFY COLUMN jenis_sp
            ENUM('SP1', 'SP2', 'SP3')
            NOT NULL
        ");
    }
};