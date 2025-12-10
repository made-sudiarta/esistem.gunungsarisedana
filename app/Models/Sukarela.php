<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sukarela extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        'no_rek','tanggal_terdaftar','group_id','member_id','saldo','keterangan'
    ];

    public function groups(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
    public function members(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
    public function trx_sukarelas(): HasMany
    {
        return $this->hasMany(TrxSukarela::class);
    }
    public function getNoRekWithGroupAttribute()
    {
        $noRekFormatted = str_pad($this->no_rek, 5, '0', STR_PAD_LEFT);
        $groupCode = $this->groups ? $this->groups->group : 'GG'; // ganti 'code' dengan nama kolom yang sesuai di tabel group

        return "{$noRekFormatted}/{$groupCode}";
    }
    public function setoransukarelas(): HasMany
    {
        return $this->hasMany(SetoranSukarela::class);
    }
}
