<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class SimpananBerjangka extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'kode_bilyet',
        'group_id',
        'member_id',
        'nama_lengkap',
        'tanggal_masuk',
        'jangka_waktu',
        'bunga_persen',
        'nominal',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
