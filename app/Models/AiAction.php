<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'recommendation_id',
        'user_id',
        'action',
        'result',
        'status',
        'executed_at',
    ];

    protected $casts = [
        'executed_at' => 'datetime',
    ];

    public function recommendation(): BelongsTo
    {
        return $this->belongsTo(AiRecommendation::class, 'recommendation_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
