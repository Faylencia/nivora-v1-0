<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiStoreFloorPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'outlet_id',
        'user_id',
        'status',
        'room_length_m',
        'room_width_m',
        'input_summary',
        'analysis_result',
        'plan',
        'generated_at',
    ];

    protected $casts = [
        'room_length_m' => 'decimal:2',
        'room_width_m' => 'decimal:2',
        'input_summary' => 'array',
        'analysis_result' => 'array',
        'plan' => 'array',
        'generated_at' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function outlet(): BelongsTo
    {
        // Outlets were merged into businesses, so outlet_id references the business id.
        return $this->belongsTo(Business::class, 'outlet_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
