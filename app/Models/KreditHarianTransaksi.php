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
        'user_id',
    ];

    public function kreditHarian()
    {
        return $this->belongsTo(KreditHarian::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
