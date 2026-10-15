<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalePromo extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'sale_id',
        'promo_id',
        'discount_amount',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }
}
