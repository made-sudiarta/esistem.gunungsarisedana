<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratTagihanNomorCounter extends Model
{
    protected $table = 'surat_tagihan_nomor_counters';

    protected $fillable = [
        'bulan',
        'tahun',
        'last_number',
    ];
}