<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class KreditBulanan extends Model
{
    use SoftDeletes;

    protected $table = 'kredit_bulanans';

    protected $fillable = [
        'member_id',
        'group_id',
        'no_pokok',
        'tanggal_pengajuan',
        'jangka_waktu',
        'tanggal_jatuh_tempo',
        'plafond',
        'bunga_persen',
        'tujuan_pinjaman',
        'biaya_adm_persen',
        'biaya_provisi_persen',
        'biaya_op_persen',
        'biaya_kyd',
        'biaya_materai',
        'biaya_asuransi',
        'biaya_lain',
        'keterangan_biaya_lain',
        'status',
        'sisa_tunggakan_bunga',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'plafond' => 'decimal:2',
        'bunga_persen' => 'decimal:2',
        'biaya_adm_persen' => 'decimal:2',
        'biaya_provisi_persen' => 'decimal:2',
        'biaya_op_persen' => 'decimal:2',
        'biaya_kyd' => 'decimal:2',
        'biaya_materai' => 'decimal:2',
        'biaya_asuransi' => 'decimal:2',
        'biaya_lain' => 'decimal:2',
        'sisa_tunggakan_bunga' => 'decimal:2',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function penanggungJawab(): HasOne
    {
        return $this->hasOne(KreditBulananPenanggungJawab::class, 'kredit_bulanan_id');
    }

    public function jaminans(): HasMany
    {
        return $this->hasMany(KreditBulananJaminan::class, 'kredit_bulanan_id');
    }

    public function transaksis(): HasMany
    {
        return $this->hasMany(TransaksiKreditBulanan::class, 'kredit_bulanan_id');
    }

    public function getJumlahTunggakanAttribute(): int
    {
        if ($this->status === 'lunas') {
            return 0;
        }

        $today = now();

        if (! $this->tanggal_jatuh_tempo || Carbon::parse($this->tanggal_pengajuan)->greaterThan($today)) {
            return 0;
        }

        $currentMonth = $today->copy()->startOfMonth();

        $lastTransactionDate = $this->transaksis()
            ->latest('tanggal_transaksi')
            ->value('tanggal_transaksi');

        if ($lastTransactionDate) {
            $lastMonth = Carbon::parse($lastTransactionDate)->startOfMonth();

            return $lastMonth->greaterThanOrEqualTo($currentMonth)
                ? 0
                : $lastMonth->diffInMonths($currentMonth);
        }

        $startMonth = Carbon::parse($this->tanggal_pengajuan)->startOfMonth();

        return $startMonth->greaterThanOrEqualTo($currentMonth)
            ? 0
            : $startMonth->diffInMonths($currentMonth);
    }

    public function getBungaBulananAttribute(): float
    {
        $sisa = (float) $this->getSisaSaldo();
        $persen = (float) ($this->bunga_persen ?? 0);

        $bunga = ($sisa * $persen) / 100;

        return ceil($bunga / 100) * 100;
    }
    public function getBungaTagihanBulanIni(): float
    {
        if ($this->status === 'lunas') {
            return 0;
        }

        $bulanIni = now()->startOfMonth();

        $sudahBayarBulanIni = $this->transaksis()
            ->whereDate('tanggal_transaksi', '>=', $bulanIni)
            ->exists();

        if ($sudahBayarBulanIni) {
            return 0;
        }

        $sisaPokok = (float) $this->getSisaSaldo();
        $bungaPersen = (float) ($this->bunga_persen ?? 0);

        $bunga = ($sisaPokok * $bungaPersen) / 100;

        return ceil($bunga / 100) * 100;
    }
    public function updateSisaTunggakanBungaDariPembayaran(float $bayarBunga): void
    {
        $bayarBunga = max($bayarBunga, 0);

        $sisaLama = (float) ($this->sisa_tunggakan_bunga ?? 0);
        $bungaBulanIni = (float) $this->getBungaTagihanBulanIni();

        $totalKewajibanBunga = $sisaLama + $bungaBulanIni;

        $sisaBaru = max($totalKewajibanBunga - $bayarBunga, 0);

        $this->update([
            'sisa_tunggakan_bunga' => $sisaBaru,
        ]);
    }
    public function getBungaPerBulanTagihan(): float
    {
        $sisaPokok = (float) $this->getSisaSaldo();
        $bungaPersen = (float) ($this->bunga_persen ?? 0);

        $bunga = ($sisaPokok * $bungaPersen) / 100;

        return ceil($bunga / 100) * 100;
    }
    public function hitungTotalKewajibanBungaSebelumTransaksi(bool $includeBungaBulanIni = false): float
    {
        $sisaLama = (float) ($this->sisa_tunggakan_bunga ?? 0);
        $jumlahTunggakan = (int) ($this->jumlah_tunggakan ?? 0);
        $bungaPerBulan = (float) $this->getBungaPerBulanTagihan();

        $total = $sisaLama + ($jumlahTunggakan * $bungaPerBulan);

        if ($includeBungaBulanIni) {
            $total += $bungaPerBulan;
        }

        return $total;
    }
    public function getBungaBulanan(): float
    {
        $sisa = (float) $this->getSisaSaldo();
        $persen = (float) ($this->bunga_persen ?? 0);

        $bunga = ($sisa * $persen) / 100;

        return ceil($bunga / 100) * 100;
    }
    public function hitungTotalTagihan(): float
    {
        $plafond = (float) $this->plafond;
        $adm = (float) $this->biaya_adm_persen;
        $provisi = (float) $this->biaya_provisi_persen;
        $op = (float) $this->biaya_op_persen;

        $materai = (float) $this->biaya_materai;
        $asuransi = (float) $this->biaya_asuransi;
        $biayaLain = (float) $this->biaya_lain;
        $kyd = (float) $this->biaya_kyd;

        $biayaPersen = $plafond * ($adm + $provisi + $op) / 100;

        return $biayaPersen + $materai + $asuransi + $kyd + $biayaLain;
    }

    public function hitungAngsuranPerBulan(): float
    {
        $jangkaWaktu = (int) $this->jangka_waktu;
        $plafond = (float) $this->plafond;
        $bunga = (float) $this->bunga_persen;

        if ($jangkaWaktu <= 0) {
            return 0;
        }
        $bungarp = $plafond*$bunga/100;
        $pokokrp = $plafond/$jangkaWaktu;

        return ceil($bungarp+$pokokrp/1000)*1000;
    }

    public function hitungTotalTerbayar(): float
    {
        return (float) $this->transaksis()->sum('pokok');
    }

    public function hitungSisaPokok(): float
    {
        $sisa = (float) $this->plafond - $this->hitungTotalTerbayar();

        return max($sisa, 0);
    }

    public function refreshPerhitungan(): void
    {
        $totalTagihan = $this->hitungTotalTagihan();
        $angsuranPerBulan = $this->hitungAngsuranPerBulan();
        $sisaPokok = $this->hitungSisaPokok();

        $status = $sisaPokok <= 0 ? 'lunas' : 'aktif';

        $this->update([
            'total_tagihan' => $totalTagihan,
            'angsuran_per_bulan' => $angsuranPerBulan,
            'sisa_pokok' => $sisaPokok,
            'status' => $status,
        ]);
    }
    public function getSaldoAwal(): float
    {
        return (float) $this->plafond;
    }

    public function getTotalPokokTerbayar(): float
    {
        return (float) $this->transaksis()->sum('pokok');
    }

    public function getSisaSaldo(): float
    {
        $sisa = $this->getSaldoAwal() - $this->getTotalPokokTerbayar();
        return max($sisa, 0);
    }
    public function getStatusAttribute($value)
    {
        return $this->getSisaSaldo() <= 0 ? 'lunas' : 'aktif';
    }
}