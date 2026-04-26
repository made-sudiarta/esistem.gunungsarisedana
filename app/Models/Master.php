<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Master extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'group',
        'key',
        'value',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function getValue(string $group, string $key, mixed $default = null): mixed
    {
        return static::query()
            ->where('group', $group)
            ->where('key', $key)
            ->where('is_active', true)
            ->value('value') ?? $default;
    }
}