<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrxSukarela extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sukarela_id','tanggal_trx','debit','kredit','keterangan'
    ];
    public function sukarela(): BelongsTo
    {
        return $this->belongsTo(Sukarela::class, 'sukarela_id');
    }

}
