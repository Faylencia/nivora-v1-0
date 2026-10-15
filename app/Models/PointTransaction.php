<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointTransaction extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'business_id',
        'from_wallet_id',
        'to_wallet_id',
        'actor_user_id',
        'sale_id',
        'type',
        'points',
        'from_balance_after',
        'to_balance_after',
        'description',
    ];

    protected $casts = [
        'points' => 'integer',
        'from_balance_after' => 'integer',
        'to_balance_after' => 'integer',
        'created_at' => 'datetime',
    ];

    public function fromWallet(): BelongsTo
    {
        return $this->belongsTo(PointWallet::class, 'from_wallet_id');
    }

    public function toWallet(): BelongsTo
    {
        return $this->belongsTo(PointWallet::class, 'to_wallet_id');
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
