<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiRecommendation extends Model
{
    use HasFactory;

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'business_id',
        'type',
        'title',
        'insight',
        'recommendation',
        'priority',
        'status',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(AiAction::class, 'recommendation_id');
    }
}
