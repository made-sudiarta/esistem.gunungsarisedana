<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KreditBulananPenanggungJawab extends Model
{
    protected $table = 'kredit_bulanan_penanggung_jawabs';

    protected $fillable = [
        'kredit_bulanan_id',
        'nik',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'pekerjaan',
        'no_hp',
        'alamat',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function kreditBulanan(): BelongsTo
    {
        return $this->belongsTo(KreditBulanan::class, 'kredit_bulanan_id');
    }
}