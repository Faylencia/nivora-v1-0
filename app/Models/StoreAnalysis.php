<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'image_url',
        'overall_score',
        'analysis_result',
        'analyzed_at',
    ];

    protected $casts = [
        'overall_score' => 'decimal:2',
        'analyzed_at' => 'datetime',
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
}
