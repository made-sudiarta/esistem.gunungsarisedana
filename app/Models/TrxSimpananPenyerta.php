<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class TrxSimpananPenyerta extends Model
{
    use HasFactory;
    protected $fillable = [
        'member_id','tanggal_trx','debit','kredit','keterangan'
    ];
    public function members(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}
