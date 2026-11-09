<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class AiStoreFloorPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'outlet_id',
        'user_id',
        'status',
        'concept',
        'room_length_m',
        'room_width_m',
        'input_summary',
        'original_photos',
        'generated_image_path',
        'analysis_result',
        'plan',
        'generated_at',
    ];

    protected $casts = [
        'room_length_m' => 'decimal:2',
        'room_width_m' => 'decimal:2',
        'input_summary' => 'array',
        'original_photos' => 'array',
        'analysis_result' => 'array',
        'plan' => 'array',
        'generated_at' => 'datetime',
    ];

    /**
     * @return list<string>
     */
    public function originalPhotoUrls(): array
    {
        return collect($this->original_photos ?? [])
            ->filter(fn (mixed $path): bool => is_string($path) && $path !== '')
            ->map(fn (string $path): string => Storage::disk('public')->url($path))
            ->values()
            ->all();
    }

    public function generatedImageUrl(): ?string
    {
        $path = $this->generated_image_path;

        if (! is_string($path) || $path === '') {
            return null;
        }

        return Storage::disk('public')->url($path);
    }

    public function roomAreaM2(): float
    {
        return round((float) $this->room_length_m * (float) $this->room_width_m, 2);
    }

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
