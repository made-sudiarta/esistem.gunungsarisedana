<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SetoranSukarela extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'tanggal_trx','setoran_id','sukarela_id','jumlah','keterangan'
    ];
    public function setoran(): BelongsTo
    {
        return $this->belongsTo(Setoran::class, 'setoran_id');
    }
    public function sukarela(): BelongsTo
    {
        return $this->belongsTo(Sukarela::class, 'sukarela_id');
    }
    public function anggota()
    {
        return $this->belongsTo(\App\Models\Member::class, 'member_id');
    }

}
