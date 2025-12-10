<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Setoran extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'tanggal_trx','group_id','sukarela_total','sinbeswa_total','pokok_total','penyerta_total','wajib_total','acrh_total','status','keterangan'
    ];
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
    public function setoransukarelas(): HasMany
    {
        return $this->hasMany(SetoranSukarela::class);
    }
}
