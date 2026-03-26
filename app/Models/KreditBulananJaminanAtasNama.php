<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KreditBulananJaminanAtasNama extends Model
{
    protected $table = 'kredit_bulanan_jaminan_atas_namas';

    protected $fillable = [
        'jaminan_id',
        'atas_nama',
    ];

    public function jaminan(): BelongsTo
    {
        return $this->belongsTo(KreditBulananJaminan::class, 'jaminan_id');
    }
}