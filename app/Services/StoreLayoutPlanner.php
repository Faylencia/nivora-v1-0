<?php

namespace App\Services;

/**
 * Deterministic layout planner: turns Gemini's photo observations into a
 * non-overlapping floor plan instead of trusting model coordinates.
 */
final class StoreLayoutPlanner
{
    /**
     * @param  array<string, mixed>  $detected
     * @param  list<array<string, mixed>>  $notes
     * @return list<array<string, mixed>>
     */
    public function plan(float $length, float $width, array $detected, ?string $doorWall, array &$notes): array
    {
        $plan = [];
        $reserved = [];

        $door = $this->doorObject($length, $width, $detected, $doorWall, $notes);

        if ($door !== null) {
            $plan[] = $door;
            $reserved[] = $door;
        }

        $cashier = $this->cashierObject($length, $width, $door);
        $plan[] = $cashier;
        $reserved[] = $cashier;

        foreach ($this->windowObjects($length, $width, $detected, $notes) as $window) {
            $plan[] = $window;
            $reserved[] = $window;
        }

        $stockroom = $this->stockroomObject($length, $width, $door, $cashier);

        if ($stockroom !== null && ! $this->intersectsAny($stockroom, $reserved)) {
            $plan[] = $stockroom;
            $reserved[] = $stockroom;
        }

        foreach ($this->wallRackObjects($length, $width, $door, $reserved) as $rack) {
            $plan[] = $rack;
            $reserved[] = $rack;
        }

        foreach ($this->midRackObjects($length, $width, $door, $reserved) as $rack) {
            $plan[] = $rack;
            $reserved[] = $rack;
        }

        return $this->withIds($plan);
    }

    /**
     * @param  array<string, mixed>  $detected
     * @param  list<string>  $notes
     * @return array<string, mixed>|null
     */
    private function doorObject(float $length, float $width, array $detected, ?string $doorWall, array &$notes): ?array
    {
        $rawDoor = $detected['door'] ?? null;
        $rawDoor = is_array($rawDoor) ? $rawDoor : [];
        $confidence = strtolower((string) ($rawDoor['confidence'] ?? 'low'));
        $detectedWall = strtolower((string) ($rawDoor['wall'] ?? ''));
        $detected = (bool) ($rawDoor['detected'] ?? false);
        $confirmedWall = is_string($doorWall) && in_array($doorWall, ['top', 'bottom', 'left', 'right'], true);
        $usableDetected = $detected && in_array($confidence, ['medium', 'high'], true) && in_array($detectedWall, ['top', 'bottom', 'left', 'right'], true);
        $wall = $confirmedWall ? $doorWall : ($usableDetected ? $detectedWall : null);

        if ($wall === null) {
            if (! $detected || $confidence === 'low') {
                $notes[] = 'Pintu tidak terlihat cukup jelas; posisi pintu tidak dibuat agar AI tidak mengarang.';
            }

            return null;
        }

        if ($confirmedWall && ! $usableDetected) {
            $notes[] = 'Pintu ditempatkan di dinding '.$this->wallLabel($wall).' sesuai konfirmasi Anda dan ditandai perkiraan.';
        }

        $doorWidth = $this->clampFloat($rawDoor['width_m'] ?? null, 0.7, 2.0, 0.9);
        $axisLimit = $wall === 'left' || $wall === 'right' ? $width : $length;
        $offset = $this->clampFloat($rawDoor['offset_m'] ?? null, 0.0, max(0.0, $axisLimit - $doorWidth), null);

        if ($offset === null) {
            $offset = $wall === 'left' || $wall === 'right'
                ? round($width / 2 - $doorWidth / 2, 2)
                : round($length / 2 - $doorWidth / 2, 2);
        }

        return match ($wall) {
            'top' => $this->object('Pintu Masuk', 'door', $offset, 0.0, $doorWidth, 0.3),
            'bottom' => $this->object('Pintu Masuk', 'door', $offset, $width - 0.3, $doorWidth, 0.3),
            'left' => $this->object('Pintu Masuk', 'door', 0.0, $offset, 0.3, $doorWidth),
            default => $this->object('Pintu Masuk', 'door', $length - 0.3, $offset, 0.3, $doorWidth),
        };
    }

    /**
     * @param  array<string, mixed>|null  $door
     * @return array<string, mixed>
     */
    private function cashierObject(float $length, float $width, ?array $door): array
    {
        $wall = $door === null ? 'top' : $this->wallOf($door, $length, $width);

        return match ($wall) {
            'top' => $this->object('Meja Kasir', 'cashier_desk', $length - 1.2, $width - 0.85, 1.0, 0.7),
            'bottom' => $this->object('Meja Kasir', 'cashier_desk', $length - 1.2, 0.15, 1.0, 0.7),
            'left' => $this->object('Meja Kasir', 'cashier_desk', $length - 0.9, 0.15, 0.75, 1.1),
            default => $this->object('Meja Kasir', 'cashier_desk', 0.15, 0.15, 0.75, 1.1),
        };
    }

    /**
     * @param  array<string, mixed>  $detected
     * @param  list<string>  $notes
     * @return list<array<string, mixed>>
     */
    private function windowObjects(float $length, float $width, array $detected, array &$notes): array
    {
        $windows = $detected['windows'] ?? [];

        if (! is_array($windows)) {
            return [];
        }

        $objects = [];

        foreach (array_slice($windows, 0, 6) as $rawWindow) {
            if (! is_array($rawWindow)) {
                continue;
            }

            $wall = strtolower((string) ($rawWindow['wall'] ?? ''));
            $confidence = strtolower((string) ($rawWindow['confidence'] ?? 'low'));
            $widthM = $this->clampFloat($rawWindow['width_m'] ?? null, 0.5, 4.0, 1.2);
            $axisLimit = in_array($wall, ['left', 'right'], true) ? $width : $length;
            $offset = $this->clampFloat($rawWindow['offset_m'] ?? null, 0.0, max(0.0, $axisLimit - $widthM), null);

            if (! in_array($confidence, ['medium', 'high'], true) || ! in_array($wall, ['top', 'bottom', 'left', 'right'], true)) {
                continue;
            }

            if ($offset === null) {
                continue;
            }

            $objects[] = match ($wall) {
                'top' => $this->object('Jendela', 'window', $offset, 0.0, $widthM, 0.2),
                'bottom' => $this->object('Jendela', 'window', $offset, $width - 0.2, $widthM, 0.2),
                'left' => $this->object('Jendela', 'window', 0.0, $offset, 0.2, $widthM),
                default => $this->object('Jendela', 'window', $length - 0.2, $offset, 0.2, $widthM),
            };
        }

        if ($objects === [] && $windows !== []) {
            $notes[] = 'Jendela terdeteksi tetapi posisinya tidak cukup jelas, jadi tidak dipaksakan ke denah.';
        }

        return $objects;
    }

    /**
     * @param  array<string, mixed>|null  $door
     * @param  array<string, mixed>  $cashier
     * @return array<string, mixed>|null
     */
    private function stockroomObject(float $length, float $width, ?array $door, array $cashier): ?array
    {
        if ($length < 6 || $width < 4) {
            return null;
        }

        $candidate = $door === null
            ? $this->object('Gudang', 'stockroom', $length - 1.7, 0.2, 1.5, 1.5)
            : $this->stockroomOppositeDoor($length, $width, $door);

        return $this->intersectsAny($candidate, [$cashier]) ? null : $candidate;
    }

    /**
     * @param  array<string, mixed>  $door
     */
    private function stockroomOppositeDoor(float $length, float $width, array $door): array
    {
        $wall = $this->wallOf($door, $length, $width);

        return match ($wall) {
            'top' => $this->object('Gudang', 'stockroom', 0.2, $width - 1.7, 1.5, 1.5),
            'bottom' => $this->object('Gudang', 'stockroom', 0.2, 0.2, 1.5, 1.5),
            'left' => $this->object('Gudang', 'stockroom', $length - 1.7, $width - 1.7, 1.5, 1.5),
            default => $this->object('Gudang', 'stockroom', 0.2, $width - 1.7, 1.5, 1.5),
        };
    }

    /**
     * @param  array<string, mixed>|null  $door
     * @param  list<array<string, mixed>>  $reserved
     * @return list<array<string, mixed>>
     */
    private function wallRackObjects(float $length, float $width, ?array $door, array $reserved): array
    {
        $objects = [];
        $doorWall = $door === null ? null : $this->wallOf($door, $length, $width);
        $walls = ['top', 'bottom', 'left', 'right'];

        foreach ($walls as $wall) {
            if ($wall === $doorWall) {
                continue;
            }

            if ($wall === 'top' || $wall === 'bottom') {
                for ($x = 0.0; $x + 1.0 <= $length + 0.001; $x += 1.1) {
                    $candidate = $this->object('Rak Dinding', 'wall_rack', round($x, 2), $wall === 'top' ? 0.0 : round($width - 0.55, 2), 1.0, 0.55);

                    if (! $this->intersectsAny($candidate, array_merge($reserved, $objects))) {
                        $objects[] = $candidate;
                    }
                }
            } else {
                for ($y = 0.0; $y + 1.0 <= $width + 0.001; $y += 1.1) {
                    $candidate = $this->object('Rak Dinding', 'wall_rack', $wall === 'left' ? 0.0 : round($length - 0.55, 2), round($y, 2), 0.55, 1.0);

                    if (! $this->intersectsAny($candidate, array_merge($reserved, $objects))) {
                        $objects[] = $candidate;
                    }
                }
            }
        }

        return $objects;
    }

    /**
     * @param  array<string, mixed>|null  $door
     * @param  list<array<string, mixed>>  $reserved
     * @return list<array<string, mixed>>
     */
    private function midRackObjects(float $length, float $width, ?array $door, array $reserved): array
    {
        if ($length < 5 || $width < 3.5) {
            return [];
        }

        $objects = [];
        $rackWidth = min(2.4, max(1.2, $length - 2.0));
        $rackDepth = 0.8;
        $rows = $width >= 5.4 ? [1.15, 3.15] : [max(0.8, $width / 2 - $rackDepth / 2)];

        if ($width - 0.8 < $rows[0] + $rackDepth) {
            $rows = [max(0.8, $width / 2 - $rackDepth / 2)];
        }

        foreach ($rows as $y) {
            $x = max(0.8, ($length - $rackWidth) / 2);
            $candidate = $this->object('Rak Tengah', 'mid_rack', round($x, 2), round($y, 2), round($rackWidth, 2), $rackDepth);

            if (! $this->intersectsAny($candidate, array_merge($reserved, $objects))) {
                $objects[] = $candidate;
            }
        }

        // Second module on each row only when the room is wide enough.
        if ($length >= 8.5) {
            foreach ($rows as $y) {
                $candidate = $this->object('Rak Tengah', 'mid_rack', 0.8, round($y, 2), round($rackWidth, 2), $rackDepth);

                if (! $this->intersectsAny($candidate, array_merge($reserved, $objects))) {
                    $objects[] = $candidate;
                }
            }
        }

        return $objects;
    }

    /**
     * @param  array<string, mixed>  $object
     */
    private function object(string $name, string $type, float $x, float $y, float $width, float $height): array
    {
        return [
            'id' => 'obj-'.bin2hex(random_bytes(6)),
            'name' => $name,
            'type' => $type,
            'x' => round($x, 2),
            'y' => round($y, 2),
            'width' => round($width, 2),
            'height' => round($height, 2),
            'rotation' => 0,
            'color' => AiStoreLayoutService::TYPE_COLORS[$type] ?? '#94A3B8',
            'estimated' => true,
        ];
    }

    private function withIds(array $plan): array
    {
        foreach ($plan as $index => &$object) {
            $object['id'] = 'obj-'.($index + 1);
        }

        return $plan;
    }

    /**
     * @param  array<string, mixed>  $a
     * @param  array<string, mixed>  $b
     */
    private function intersects(array $a, array $b): bool
    {
        return $a['x'] < $b['x'] + $b['width'] - 0.001
            && $b['x'] < $a['x'] + $a['width'] - 0.001
            && $a['y'] < $b['y'] + $b['height'] - 0.001
            && $b['y'] < $a['y'] + $a['height'] - 0.001;
    }

    /**
     * @param  array<string, mixed>  $candidate
     * @param  list<array<string, mixed>>  $reserved
     */
    private function intersectsAny(array $candidate, array $reserved): bool
    {
        foreach ($reserved as $item) {
            if ($this->intersects($candidate, $item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $door
     */
    private function wallOf(array $door, float $length, float $width): string
    {
        if ((float) $door['y'] <= 0.001) {
            return 'top';
        }

        if ((float) $door['y'] >= $width - 0.301) {
            return 'bottom';
        }

        if ((float) $door['x'] <= 0.001) {
            return 'left';
        }

        return 'right';
    }

    private function wallLabel(string $wall): string
    {
        return match ($wall) {
            'top' => 'atas',
            'bottom' => 'bawah',
            'left' => 'kiri',
            'right' => 'kanan',
            default => $wall,
        };
    }

    private function clampFloat(mixed $value, float $min, float $max, ?float $default): ?float
    {
        $parsed = is_numeric($value) ? (float) $value : null;

        if ($parsed === null) {
            return $default;
        }

        return min(max($parsed, $min), $max);
    }
}
