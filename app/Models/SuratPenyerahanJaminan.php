<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class SuratPenyerahanJaminan extends Model
{
    use SoftDeletes;

    protected $table = 'surat_penyerahan_jaminans';

    protected $fillable = [
        'kredit_bulanan_id',
        'nomor_surat',
        'tanggal_surat',
        'status_surat',
        'jenis_kendaraan',
        'merek_tipe',
        'nomor_polisi',
        'nomor_rangka',
        'nomor_mesin',
        'nomor_bpkb',
        'nomor_stnk',
        'atas_nama_dokumen',
        'kondisi_kendaraan',
        'kelengkapan_diserahkan',
        'catatan',
        'tanggal_serah_terima',
        'waktu_serah_terima',
        'tempat_serah_terima',
        'nama_penerima',
        'jabatan_penerima',
        'created_by',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'tanggal_serah_terima' => 'date',
    ];

    public function kreditBulanan(): BelongsTo
    {
        return $this->belongsTo(
            KreditBulanan::class,
            'kredit_bulanan_id'
        );
    }
    protected static function booted(): void
    {
        static::creating(function (self $surat) {
            if (blank($surat->nomor_surat)) {
                $tanggal = $surat->tanggal_surat
                    ? Carbon::parse($surat->tanggal_surat)
                    : now();

                $surat->tanggal_surat ??= $tanggal->toDateString();

                $tahun = $tanggal->year;

                $bulanRomawi = [
                    1 => 'I', 2 => 'II', 3 => 'III',
                    4 => 'IV', 5 => 'V', 6 => 'VI',
                    7 => 'VII', 8 => 'VIII', 9 => 'IX',
                    10 => 'X', 11 => 'XI', 12 => 'XII',
                ][$tanggal->month];

                $nomorTerakhir = static::query()
                    ->where('nomor_surat', 'like', "%/KGS/SPJ/%/{$tahun}")
                    ->pluck('nomor_surat')
                    ->map(fn ($nomor) => (int) explode('/', $nomor)[0])
                    ->max() ?? 0;

                $surat->nomor_surat = sprintf(
                    '%03d/KGS/SPJ/%s/%d',
                    $nomorTerakhir + 1,
                    $bulanRomawi,
                    $tahun
                );
            }

            if (blank($surat->created_by)) {
                $surat->created_by = auth()->id();
            }
        });
    }
}