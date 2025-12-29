<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KreditHarianTransaksi extends Model
{
    use HasFactory;
    protected $fillable = [
        'kredit_harian_id',
        'tanggal_transaksi',
        'jumlah',
    ];

    public function kreditHarian()
    {
        return $this->belongsTo(KreditHarian::class);
    }
}
