<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class KreditHarian extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'kredit_harian';
    protected $fillable = [
        'member_id',
        'group_id',
        'no_pokok',
        'nama_lengkap',
        'alamat',
        'no_hp',
        'tanggal_pengajuan',
        'jangka_waktu',
        'plafond',
        'bunga_persen',
        'admin_persen',
        'sisa_pokok'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    public function getCicilanHarianAttribute()
    {
        $totalBunga = $this->plafond * ($this->bunga_persen / 100);
        $totalAdmin = $this->plafond * ($this->admin_persen / 100);
        $total = $this->plafond + $totalBunga + $totalAdmin;

        return $total / $this->jangka_waktu;
    }

    public function transaksis()
    {
        return $this->hasMany(KreditHarianTransaksi::class);
    }
    public function getStatusAttribute(): string
    {
        if ($this->sisa_pokok <= 0) {
            return 'lunas';
        }

        $jatuhTempo = Carbon::parse($this->tanggal_pengajuan)
            ->addDays($this->jangka_waktu);

        return $jatuhTempo->isPast()
            ? 'jatuh tempo'
            : 'aktif';
    }

    public function getSisaPokokAttribute(): float
    {
        $plafond = $this->plafond ?? 0;
        $bunga = $this->bunga_persen ?? 0;
        $admin = $this->admin_persen ?? 0;

        $totalKredit = $plafond + ($plafond * ($bunga + $admin) / 100);

        $totalBayar = $this->transaksis()->sum('jumlah');

        return max($totalKredit - $totalBayar, 0);
    }

}
