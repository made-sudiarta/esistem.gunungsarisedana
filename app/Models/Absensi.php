<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



class Absensi extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'jumlah_jam',
        'jumlah_setoran',
        'penarikan',
        'latitude_masuk',
        'longitude_masuk',
        'jarak_masuk',
        'latitude_keluar',
        'longitude_keluar',
        'jarak_keluar',
        'sumber_absen',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
