<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
