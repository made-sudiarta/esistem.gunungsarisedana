<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KreditBulananFidusia extends Model
{
    protected $table = 'kredit_bulanan_fidusias';

    protected $fillable = [
        'jaminan_id',
        'merk',
        'type',
        'warna',
        'tahun',
        'no_rangka',
        'no_mesin',
        'no_polisi',
        'no_bpkb',
        'atasnama',
        'taksiran_harga',
        'tempat_penyimpanan',
    ];

    protected $casts = [
        'taksiran_harga' => 'decimal:2',
    ];

    public function jaminan(): BelongsTo
    {
        return $this->belongsTo(KreditBulananJaminan::class, 'jaminan_id');
    }
}