<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratTagihanKredit extends Model
{
    use SoftDeletes;

    protected $table = 'surat_tagihan_kredits';

    protected $fillable = [
        'kredit_bulanan_id',
        'jenis_sp',
        'nomor_surat',
        'no_pokok',
        'jumlah_tunggakan_bunga',
        'sisa_tunggakan_bunga',
        'bunga_per_bulan',
        'total_tunggakan_bunga',
        'sisa_pokok_kredit',
        'tanggal_jatuh_tempo',
        'tanggal_surat',
        'status_surat',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_surat' => 'date',
        'sisa_tunggakan_bunga' => 'decimal:2',
        'bunga_per_bulan' => 'decimal:2',
        'total_tunggakan_bunga' => 'decimal:2',
        'sisa_pokok_kredit' => 'decimal:2',
    ];

    public function kreditBulanan(): BelongsTo
    {
        return $this->belongsTo(KreditBulanan::class, 'kredit_bulanan_id');
    }
}