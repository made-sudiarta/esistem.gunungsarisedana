<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimpananBerjangka extends Model
{
    use HasFactory, SoftDeletes;

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

    protected $casts = [
        'tanggal_masuk' => 'date',
        'jangka_waktu' => 'integer',
        'bunga_persen' => 'decimal:2',
        'nominal' => 'decimal:2',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}