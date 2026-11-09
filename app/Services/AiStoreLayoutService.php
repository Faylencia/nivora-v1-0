<?php

namespace App\Services;

/**
 * Shared floor-plan primitives: object colour palette and strict validation
 * for owner-edited plans. Layout generation itself is performed by the AI
 * (see AiStoreAdvisorService); nothing here invents object positions.
 */
final class AiStoreLayoutService
{
    /** @var array<string, string> */
    public const TYPE_COLORS = [
        'wall_rack' => '#687EFF',
        'mid_rack' => '#98E4FF',
        'cashier_desk' => '#1F2544',
        'door' => '#F59E0B',
        'window' => '#38BDF8',
        'stockroom' => '#64748B',
        'storage' => '#B6FFFA',
        'aisle' => '#D7E4FF',
        'zone' => '#C7D2FE',
    ];

    /**
     * Normalize an existing plan: clamp objects to room bounds and ensure a door
     * exists for the given position. Used for local "rapikan" without AI calls.
     *
     * @param  list<array<string, mixed>>  $plan
     * @return list<array<string, mixed>>
     */
    public function normalizePlan(array $plan, float $length, float $width, ?string $doorPosition = null): array
    {
        $objects = [];
        $hasDoor = false;

        foreach (array_values($plan) as $index => $object) {
            if (! is_array($object)) {
                continue;
            }

            $type = (string) ($object['type'] ?? 'zone');
            $objectWidth = min(max((float) ($object['width'] ?? 1.0), 0.2), $length);
            $objectHeight = min(max((float) ($object['height'] ?? 1.0), 0.2), $width);
            $x = min(max((float) ($object['x'] ?? 0.0), 0.0), max(0.0, $length - $objectWidth));
            $y = min(max((float) ($object['y'] ?? 0.0), 0.0), max(0.0, $width - $objectHeight));

            $objects[] = [
                'id' => is_string($object['id'] ?? null) && $object['id'] !== '' ? $object['id'] : 'obj-'.($index + 1),
                'name' => is_string($object['name'] ?? null) && trim((string) $object['name']) !== '' ? trim((string) $object['name']) : 'Objek',
                'type' => $type,
                'x' => round($x, 2),
                'y' => round($y, 2),
                'width' => round($objectWidth, 2),
                'height' => round($objectHeight, 2),
                'rotation' => in_array((int) ($object['rotation'] ?? 0), [0, 90, 180, 270], true) ? (int) $object['rotation'] : 0,
                'color' => is_string($object['color'] ?? null) && preg_match('/^#[0-9A-Fa-f]{6}$/', (string) $object['color'])
                    ? (string) $object['color']
                    : (self::TYPE_COLORS[$type] ?? '#94A3B8'),
                'estimated' => (bool) ($object['estimated'] ?? false),
            ];

            if ($type === 'door') {
                $hasDoor = true;
            }
        }

        if (! $hasDoor && is_string($doorPosition) && $doorPosition !== '') {
            $doorWidth = min(1.0, max(0.8, $length * 0.15));
            $doorDepth = 0.3;
            [$x, $y, $w, $h] = match ($doorPosition) {
                'belakang' => [max(0.0, ($length - $doorWidth) / 2), 0.0, $doorWidth, $doorDepth],
                'kiri' => [0.0, max(0.0, ($width - $doorWidth) / 2), $doorDepth, $doorWidth],
                'kanan' => [max(0.0, $length - $doorDepth), max(0.0, ($width - $doorWidth) / 2), $doorDepth, $doorWidth],
                default => [max(0.0, ($length - $doorWidth) / 2), max(0.0, $width - $doorDepth), $doorWidth, $doorDepth],
            };
            $objects[] = [
                'id' => 'obj-door',
                'name' => 'Pintu Masuk',
                'type' => 'door',
                'x' => round((float) $x, 2),
                'y' => round((float) $y, 2),
                'width' => round((float) $w, 2),
                'height' => round((float) $h, 2),
                'rotation' => 0,
                'color' => self::TYPE_COLORS['door'],
                'estimated' => false,
            ];
        }

        return $objects;
    }

    /**
     * Validate an edited floor plan against the room bounds and planning rules.
     *
     * @param  list<array<string, mixed>>  $plan
     * @return list<string>
     */
    public function validatePlan(array $plan, float $length, float $width): array
    {
        $errors = [];

        foreach ($plan as $object) {
            $objectX = (float) ($object['x'] ?? 0);
            $objectY = (float) ($object['y'] ?? 0);
            $objectWidth = (float) ($object['width'] ?? 0);
            $objectHeight = (float) ($object['height'] ?? 0);

            if ($objectX < -0.001 || $objectY < -0.001 || $objectX + $objectWidth > $length + 0.001 || $objectY + $objectHeight > $width + 0.001) {
                $errors[] = 'Objek '.(string) ($object['name'] ?? $object['id'] ?? 'Denah').' berada di luar batas ruangan.';

                continue;
            }

            if (($object['type'] ?? '') === 'door' && $objectWidth > 0) {
                $overlapsDoor = collect($plan)
                    ->filter(fn (array $candidate): bool => ($candidate['type'] ?? '') !== 'door')
                    ->contains(function (array $candidate) use ($objectX, $objectY, $objectWidth, $objectHeight): bool {
                        $candidateWidth = (float) ($candidate['width'] ?? 0);
                        $candidateHeight = (float) ($candidate['height'] ?? 0);

                        if ($candidateWidth <= 0 || $candidateHeight <= 0) {
                            return false;
                        }

                        $candidateX = (float) ($candidate['x'] ?? 0);
                        $candidateY = (float) ($candidate['y'] ?? 0);

                        return $candidateX < $objectX + $objectWidth
                            && $candidateX + $candidateWidth > $objectX
                            && $candidateY < $objectY + $objectHeight
                            && $candidateY + $candidateHeight > $objectY;
                    });

                if ($overlapsDoor) {
                    $errors[] = 'Objek denah '.(string) ($object['name'] ?? $object['id'] ?? 'Denah').' tidak boleh menutupi pintu masuk.';
                }
            }
        }

        $rackObjects = array_values(array_filter(
            $plan,
            fn (array $object): bool => in_array(($object['type'] ?? ''), ['wall_rack', 'mid_rack', 'storage', 'stockroom'], true),
        ));
        $rackCount = count($rackObjects);

        for ($leftIndex = 0; $leftIndex < $rackCount; $leftIndex++) {
            for ($rightIndex = $leftIndex + 1; $rightIndex < $rackCount; $rightIndex++) {
                $first = $rackObjects[$leftIndex];
                $second = $rackObjects[$rightIndex];
                $firstX = (float) ($first['x'] ?? 0);
                $firstY = (float) ($first['y'] ?? 0);
                $secondX = (float) ($second['x'] ?? 0);
                $secondY = (float) ($second['y'] ?? 0);
                $firstWidth = (float) ($first['width'] ?? 0);
                $firstHeight = (float) ($first['height'] ?? 0);
                $secondWidth = (float) ($second['width'] ?? 0);
                $secondHeight = (float) ($second['height'] ?? 0);

                if ($firstX < $secondX + $secondWidth - 0.001
                    && $secondX < $firstX + $firstWidth - 0.001
                    && $firstY < $secondY + $secondHeight - 0.001
                    && $secondY < $firstY + $firstHeight - 0.001) {
                    $errors[] = 'Objek denah '.(string) ($first['name'] ?? $first['id']).' (x: '.round((float) $firstX, 2).', y: '.round((float) $firstY, 2).') bertabrakan dengan objek '.(string) ($second['name'] ?? $second['id']).' (x: '.round((float) $secondX, 2).', y: '.round((float) $secondY, 2).').';
                }
            }
        }

        return $errors;
    }
}
