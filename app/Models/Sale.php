<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sale extends Model
{
    use HasFactory;

    public const CREATED_AT = null;

    public const UPDATED_AT = null;

    protected $fillable = [
        'business_id',
        'user_id',
        'customer_id',
        'member_user_id',
        'invoice_number',
        'subtotal',
        'discount',
        'tax',
        'total',
        'paid',
        'change_amount',
        'points_to_member',
        'points_redeemed',
        'payment_method',
        'status',
        'transaction_date',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'points_to_member' => 'integer',
        'points_redeemed' => 'integer',
        'transaction_date' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function salePromos(): HasMany
    {
        return $this->hasMany(SalePromo::class);
    }

    public function promos(): BelongsToMany
    {
        return $this->belongsToMany(Promo::class, 'sale_promos')
            ->withPivot(['discount_amount', 'created_at']);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function memberCoupon(): HasOne
    {
        return $this->hasOne(MemberCoupon::class, 'redeemed_sale_id');
    }
}
