<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberCoupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_id',
        'code',
        'milestone',
        'discount_value',
        'min_transaction',
        'status',
        'expires_at',
        'earned_at_sale_id',
        'redeemed_sale_id',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_transaction' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function earnedAtSale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'earned_at_sale_id');
    }

    public function redeemedSale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'redeemed_sale_id');
    }
}
