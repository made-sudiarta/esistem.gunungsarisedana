<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'tanggal_terdaftar','member_id','jabatan_id','keterangan'
    ];

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function members(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
    public function jabatans(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }
}
