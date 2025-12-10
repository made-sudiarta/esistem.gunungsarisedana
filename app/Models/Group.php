<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'group','employee_id','tanggal_terdaftar','keterangan','user_id'
    ];
    public function employees(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function sukarelas(): HasMany
    {
        return $this->hasMany(Sukarela::class);
    }
    public function setorans(): HasMany
    {
        return $this->hasMany(setorans::class);
    }
    public function simpananberjangka(): HasMany
    {
        return $this->hasMany(SimpananBerjangka::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
