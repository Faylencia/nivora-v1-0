<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PlatformActivityLogger
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function record(
        ?User $actor,
        string $event,
        string $summary,
        ?Model $subject = null,
        array $metadata = [],
    ): void {
        DB::table('platform_activities')->insert([
            'actor_id' => $actor?->id,
            'actor_name' => $actor?->name ?? 'Pengguna sistem',
            'event' => $event,
            'summary' => $summary,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'subject_name' => $subject?->getAttribute('name') ?? $subject?->getAttribute('business_name'),
            'metadata' => $metadata === [] ? null : json_encode($metadata, JSON_THROW_ON_ERROR),
            'created_at' => now(),
        ]);
    }
}
