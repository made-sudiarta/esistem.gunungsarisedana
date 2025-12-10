<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jabatan extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'jabatan','keterangan'
    ];
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
