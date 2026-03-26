<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KreditBulananJaminan extends Model
{
    protected $table = 'kredit_bulanan_jaminans';

    protected $fillable = [
        'kredit_bulanan_id',
        'keterangan_jaminan',
    ];

    public function kreditBulanan(): BelongsTo
    {
        return $this->belongsTo(KreditBulanan::class, 'kredit_bulanan_id');
    }

    public function atasNamas(): HasMany
    {
        return $this->hasMany(KreditBulananJaminanAtasNama::class, 'jaminan_id');
    }

    public function fidusia(): HasOne
    {
        return $this->hasOne(KreditBulananFidusia::class, 'jaminan_id');
    }
}