<?php

namespace App\Services;

final class AiStoreLayoutService
{
    public const AISLE_WIDTH_M = 1.2;

    public const RACK_WIDTH_M = 1.0;

    public const RACK_DEPTH_M = 0.9;

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
    ];

    /**
     * Generate the structured floor plan and the advisory analysis for a store room.
     *
     * @param  array<string, mixed>  $input
     * @return array{analysis: array<string, mixed>, plan: list<array<string, mixed>>}
     */
    public function generate(array $input): array
    {
        $length = (float) $input['room_length_m'];
        $width = (float) $input['room_width_m'];
        $doorInput = (array) ($input['door'] ?? []);
        $windows = (array) ($input['windows'] ?? []);
        $permanentAreas = (array) ($input['permanent_areas'] ?? []);

        $door = [
            'x' => (float) ($doorInput['x'] ?? 0),
            'y' => (float) ($doorInput['y'] ?? 0),
            'width' => (float) ($doorInput['width'] ?? 0.9),
            'height' => (float) ($doorInput['height'] ?? 2.1),
        ];

        $plan = [
            $this->doorObject($door),
            $this->cashierObject($door, $length, $width),
            ...$this->wallRackObjects($door, $length, $width, $windows, $permanentAreas),
            $this->midRackObject($length, $width),
            ...$this->storageObjects($permanentAreas),
            ...$this->windowObjects($windows),
            $this->aisleObject($door, $length, $width),
        ];

        return [
            'analysis' => $this->buildAnalysis($input, $door, $plan),
            'plan' => $plan,
        ];
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

    private function doorObject(array $door): array
    {
        return [
            'id' => 'floor-plan-object-door',
            'name' => 'Pintu Masuk',
            'type' => 'door',
            'x' => round((float) $door['x'], 2),
            'y' => round((float) $door['y'], 2),
            'width' => round((float) $door['width'], 2),
            'height' => round((float) $door['height'], 2),
            'rotation' => 0,
            'color' => self::TYPE_COLORS['door'],
        ];
    }

    private function cashierObject(array $door, float $length, float $width): array
    {
        if ($this->isOnWall($door, 'left', $length)) {
            return [
                'id' => 'floor-plan-object-cashier',
                'name' => 'Meja Kasir',
                'type' => 'cashier_desk',
                'x' => 0.35,
                'y' => round(max(0, (float) $door['y'] - 0.5), 2),
                'width' => 0.7,
                'height' => 1.0,
                'rotation' => 0,
                'color' => self::TYPE_COLORS['cashier_desk'],
            ];
        }

        if ($this->isOnWall($door, 'right', $length)) {
            return [
                'id' => 'floor-plan-object-cashier',
                'name' => 'Meja Kasir',
                'type' => 'cashier_desk',
                'x' => round(max(0, $length - 1.05), 2),
                'y' => round(max(0, (float) $door['y'] - 0.5), 2),
                'width' => 0.7,
                'height' => 1.0,
                'rotation' => 0,
                'color' => self::TYPE_COLORS['cashier_desk'],
            ];
        }

        return [
            'id' => 'floor-plan-object-cashier',
            'name' => 'Meja Kasir',
            'type' => 'cashier_desk',
            'x' => round(max(0, (float) $door['x'] - 0.5), 2),
            'y' => round(max(0, (float) $door['y'] - 0.35), 2),
            'width' => 1.0,
            'height' => 0.7,
            'rotation' => 0,
            'color' => self::TYPE_COLORS['cashier_desk'],
        ];
    }

    private function isOnWall(array $door, string $wall, float $size): bool
    {
        $position = (float) ($wall === 'left' || $wall === 'right' ? $door['x'] : $door['y']);

        if ($wall === 'left') {
            return $position <= $size / 2;
        }

        if ($wall === 'right') {
            return $position >= $size / 2;
        }

        if ($wall === 'top') {
            return $position <= $size / 2;
        }

        return $position >= $size / 2;
    }

    private function overlapsDoorOrArea(float $x, float $y, float $width, float $height, array $door, array $windows, array $permanentAreas): bool
    {
        if ($x < (float) $door['x'] + (float) $door['width'] - 0.001
            && $x + $width > (float) $door['x'] - 0.001
            && $y < (float) $door['y'] + (float) $door['height'] - 0.001
            && $y + $height > (float) $door['y'] - 0.001) {
            return true;
        }

        foreach ($windows as $window) {
            $windowX = (float) ($window['x'] ?? 0);
            $windowY = (float) ($window['y'] ?? 0);
            $windowWidth = (float) ($window['width'] ?? 0);
            $windowHeight = (float) ($window['height'] ?? 0);

            if ($x < $windowX + $windowWidth - 0.001
                && $x + $width > $windowX - 0.001
                && $y < $windowY + $windowHeight - 0.001
                && $y + $height > $windowY - 0.001) {
                return true;
            }
        }

        foreach ($permanentAreas as $area) {
            $areaX = (float) ($area['x'] ?? 0);
            $areaY = (float) ($area['y'] ?? 0);
            $areaWidth = (float) ($area['width'] ?? 0);
            $areaHeight = (float) ($area['height'] ?? 0);

            if ($x < $areaX + $areaWidth - 0.001
                && $x + $width > $areaX - 0.001
                && $y < $areaY + $areaHeight - 0.001
                && $y + $height > $areaY - 0.001) {
                return true;
            }
        }

        return false;
    }

    private function wallRackObjects(array $door, float $length, float $width, array $windows, array $permanentAreas): array
    {
        $objects = [];
        $frontWalls = [
            'top' => $this->isOnWall($door, 'top', $width),
            'bottom' => $this->isOnWall($door, 'bottom', $width),
            'left' => $this->isOnWall($door, 'left', $length),
            'right' => $this->isOnWall($door, 'right', $length),
        ];

        if (! $frontWalls['top']) {
            for ($index = 0; $index < (int) floor($length / self::RACK_WIDTH_M); $index++) {
                $rackX = round($index * self::RACK_WIDTH_M, 2);
                $rackY = 0.0;

                if ($this->overlapsDoorOrArea($rackX, $rackY, self::RACK_WIDTH_M, self::RACK_DEPTH_M, $door, $windows, $permanentAreas)) {
                    continue;
                }

                $objects[] = [
                    'id' => 'floor-plan-object-wall-rack-'.count($objects),
                    'name' => 'Rak Dinding',
                    'type' => 'wall_rack',
                    'x' => $rackX,
                    'y' => $rackY,
                    'width' => self::RACK_WIDTH_M,
                    'height' => self::RACK_DEPTH_M,
                    'rotation' => 0,
                    'color' => self::TYPE_COLORS['wall_rack'],
                ];
            }
        }

        if (! $frontWalls['bottom']) {
            for ($index = 0; $index < (int) floor($length / self::RACK_WIDTH_M); $index++) {
                $rackX = round($index * self::RACK_WIDTH_M, 2);
                $rackY = round($width - self::RACK_DEPTH_M, 2);

                if ($this->overlapsDoorOrArea($rackX, $rackY, self::RACK_WIDTH_M, self::RACK_DEPTH_M, $door, $windows, $permanentAreas)) {
                    continue;
                }

                $objects[] = [
                    'id' => 'floor-plan-object-wall-rack-'.count($objects),
                    'name' => 'Rak Dinding',
                    'type' => 'wall_rack',
                    'x' => $rackX,
                    'y' => $rackY,
                    'width' => self::RACK_WIDTH_M,
                    'height' => self::RACK_DEPTH_M,
                    'rotation' => 0,
                    'color' => self::TYPE_COLORS['wall_rack'],
                ];
            }
        }

        if (! $frontWalls['left']) {
            for ($index = 0; $index < (int) floor($width / self::RACK_WIDTH_M); $index++) {
                $rackX = 0.0;
                $rackY = round($index * self::RACK_WIDTH_M, 2);

                if ($this->overlapsDoorOrArea($rackX, $rackY, self::RACK_DEPTH_M, self::RACK_WIDTH_M, $door, $windows, $permanentAreas)) {
                    continue;
                }

                $objects[] = [
                    'id' => 'floor-plan-object-wall-rack-'.count($objects),
                    'name' => 'Rak Dinding',
                    'type' => 'wall_rack',
                    'x' => $rackX,
                    'y' => $rackY,
                    'width' => self::RACK_DEPTH_M,
                    'height' => self::RACK_WIDTH_M,
                    'rotation' => 0,
                    'color' => self::TYPE_COLORS['wall_rack'],
                ];
            }
        }

        if (! $frontWalls['right']) {
            for ($index = 0; $index < (int) floor($width / self::RACK_WIDTH_M); $index++) {
                $rackX = round($length - self::RACK_DEPTH_M, 2);
                $rackY = round($index * self::RACK_WIDTH_M, 2);

                if ($this->overlapsDoorOrArea($rackX, $rackY, self::RACK_DEPTH_M, self::RACK_WIDTH_M, $door, $windows, $permanentAreas)) {
                    continue;
                }

                $objects[] = [
                    'id' => 'floor-plan-object-wall-rack-'.count($objects),
                    'name' => 'Rak Dinding',
                    'type' => 'wall_rack',
                    'x' => $rackX,
                    'y' => $rackY,
                    'width' => self::RACK_DEPTH_M,
                    'height' => self::RACK_WIDTH_M,
                    'rotation' => 0,
                    'color' => self::TYPE_COLORS['wall_rack'],
                ];
            }
        }

        return $objects;
    }

    private function midRackObject(float $length, float $width): array
    {
        return [
            'id' => 'floor-plan-object-mid-rack-1',
            'name' => 'Rak Tengah',
            'type' => 'mid_rack',
            'x' => round($length / 2 - self::RACK_WIDTH_M / 2, 2),
            'y' => round($width / 2 - self::RACK_DEPTH_M / 2, 2),
            'width' => self::RACK_WIDTH_M,
            'height' => self::RACK_DEPTH_M,
            'rotation' => 0,
            'color' => self::TYPE_COLORS['mid_rack'],
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $permanentAreas
     * @return list<array<string, mixed>>
     */
    private function storageObjects(array $permanentAreas): array
    {
        $objects = [];

        foreach ($permanentAreas as $area) {
            $type = strtolower(trim((string) ($area['type'] ?? '')));
            $areaType = in_array($type, ['stockroom', 'warehouse', 'backroom', 'storage_room', 'gudang'], true) ? 'stockroom' : 'storage';

            $objects[] = [
                'id' => 'floor-plan-object-'.($areaType === 'stockroom' ? 'stockroom-'.count($objects) : 'storage-'.count($objects)),
                'name' => $areaType === 'stockroom' ? 'Gudang' : 'Area Penyimpanan',
                'type' => $areaType,
                'x' => round((float) ($area['x'] ?? 0), 2),
                'y' => round((float) ($area['y'] ?? 0), 2),
                'width' => round((float) ($area['width'] ?? 1), 2),
                'height' => round((float) ($area['height'] ?? 1), 2),
                'rotation' => 0,
                'color' => self::TYPE_COLORS[$areaType],
            ];
        }

        return $objects;
    }

    /**
     * @param  array<int, array<string, mixed>>  $windows
     * @return list<array<string, mixed>>
     */
    private function windowObjects(array $windows): array
    {
        $objects = [];

        foreach ($windows as $window) {
            $objects[] = [
                'id' => 'floor-plan-object-window-'.count($objects),
                'name' => 'Jendela',
                'type' => 'window',
                'x' => round((float) ($window['x'] ?? 0), 2),
                'y' => round((float) ($window['y'] ?? 0), 2),
                'width' => round((float) ($window['width'] ?? 1.2), 2),
                'height' => round((float) ($window['height'] ?? 1.2), 2),
                'rotation' => 0,
                'color' => self::TYPE_COLORS['window'],
            ];
        }

        return $objects;
    }

    private function aisleObject(array $door, float $length, float $width): array
    {
        if ($this->isOnWall($door, 'left', $length) || $this->isOnWall($door, 'right', $length)) {
            return [
                'id' => 'floor-plan-object-aisle',
                'name' => 'Lorong Pelanggan',
                'type' => 'aisle',
                'x' => 0,
                'y' => round(max(0, (float) $door['y'] + (float) $door['height']), 2),
                'width' => round($length, 2),
                'height' => round(min(self::AISLE_WIDTH_M, max(0.0, $width - (float) $door['y'] - (float) $door['height'])), 2),
                'rotation' => 0,
                'color' => self::TYPE_COLORS['aisle'],
            ];
        }

        return [
            'id' => 'floor-plan-object-aisle',
            'name' => 'Lorong Pelanggan',
            'type' => 'aisle',
            'x' => min(round(max(0, (float) $door['x'] + (float) $door['width']), 2), max(0, $length - self::AISLE_WIDTH_M)),
            'y' => 0,
            'width' => self::AISLE_WIDTH_M,
            'height' => round($width, 2),
            'rotation' => 0,
            'color' => self::TYPE_COLORS['aisle'],
        ];
    }

    /**
     * @param  array<string, mixed>  $input
     * @param  array<string, mixed>  $door
     * @param  list<array<string, mixed>>  $plan
     * @return array<string, mixed>
     */
    private function buildAnalysis(array $input, array $door, array $plan): array
    {
        $length = (float) $input['room_length_m'];
        $width = (float) $input['room_width_m'];
        $permanentAreas = (array) ($input['permanent_areas'] ?? []);

        $storageArea = collect($permanentAreas)
            ->map(fn (array $area): array => [
                'type' => strtolower(trim((string) ($area['type'] ?? ''))),
                'x' => (float) ($area['x'] ?? 0),
                'y' => (float) ($area['y'] ?? 0),
                'width' => (float) ($area['width'] ?? 0),
                'height' => (float) ($area['height'] ?? 0),
            ])
            ->reject(fn (array $area): bool => $area['width'] <= 0 || $area['height'] <= 0)
            ->values();

        $racks = array_values(array_filter($plan, fn (array $object): bool => in_array($object['type'], ['wall_rack', 'mid_rack'], true)));
        $cashier = collect($plan)->first(fn (array $object): bool => $object['type'] === 'cashier_desk');

        return [
            'shape' => 'rectangle',
            'room_area_m2' => round($length * $width, 2),
            'aisle_width_m' => self::AISLE_WIDTH_M,
            'door_position' => [
                'x' => (float) $door['x'],
                'y' => (float) $door['y'],
                'width' => (float) $door['width'],
            ],
            'window_count' => count((array) ($input['windows'] ?? [])),
            'recommended_racks' => [
                'wall_racks' => count($racks),
                'mid_racks' => count(array_filter($plan, fn (array $object): bool => $object['type'] === 'mid_rack')),
            ],
            'customer_flow' => [
                'aisle_width_m' => self::AISLE_WIDTH_M,
                'route' => 'Pintu masuk -> lorong utama -> area produk -> area penyimpanan',
            ],
            'cashier_position' => [
                'x' => (float) ($cashier['x'] ?? 0),
                'y' => (float) ($cashier['y'] ?? 0),
            ],
            'storage_area' => $storageArea,
            'usable_area_m2' => round(max(0, $length * $width - count($racks) * self::RACK_WIDTH_M * self::RACK_DEPTH_M), 2),
            'notes' => [
                'Denah dirancang dengan jalur pelanggan selebar 1.2 meter.',
                'Rekomendasi ukuran rak 1 meter x 0.9 meter per modul.',
            ],
        ];
    }
}
