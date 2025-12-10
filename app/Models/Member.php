<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;



class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['nia','nik','nama_lengkap','tempat_lahir','tanggal_lahir','alamat','no_hp','jenis_id','tanggal_bergabung'];

    public function jenis(): BelongsTo
    {
        return $this->belongsTo(Jenis::class);
    }
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
    public function sukarelas(): HasMany
    {
        return $this->hasMany(Sukarela::class);
    }
    public function trx_simpanan_pokoks(): HasMany
    {
        return $this->hasMany(TrxSimpananPokok::class);
    }
    public function trxSimpananPokoks(): HasMany
    {
        return $this->hasMany(TrxSimpananPokok::class, 'member_id', 'id');
    }
    public function trx_simpanan_penyertas(): HasMany
    {
        return $this->hasMany(TrxSimpananPenyerta::class);
    }
    public function trxSimpananPenyertas(): HasMany
    {
        return $this->hasMany(TrxSimpananPenyerta::class, 'member_id', 'id');
    }
    public function trx_simpanan_wajibs(): HasMany
    {
        return $this->hasMany(TrxSimpananWajib::class);
    }
     public function simpananberjangka(): HasMany
    {
        return $this->hasMany(SimpananBerjangka::class);
    }
    public function trxSimpananWajibs(): HasMany
    {
        return $this->hasMany(TrxSimpananWajib::class, 'member_id', 'id');
    }

}
