<?php

namespace App\Services;

use App\Exceptions\AiServiceException;
use App\Models\AiStoreFloorPlan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * AI Store Advisor: generates a 2D floor-plan JSON and recommendations from
 * owner-provided store information (no photos, no image generation).
 */
final class AiStoreAdvisorService
{
    /** @var array<string, string> */
    public const STORE_TYPES = [
        'kelontong' => 'Kelontong',
        'minimarket' => 'Minimarket',
        'sembako' => 'Sembako',
        'lainnya' => 'Lainnya',
    ];

    /** @var array<string, string> */
    public const DOOR_POSITIONS = [
        'depan' => 'Depan',
        'kiri' => 'Kiri',
        'kanan' => 'Kanan',
        'belakang' => 'Belakang',
    ];

    /** @var array<string, string> */
    public const LAYOUT_CONCEPTS = [
        'modern' => 'Modern',
        'minimalis' => 'Minimalis',
        'tradisional' => 'Tradisional',
        'efisien' => 'Efisien',
    ];

    /** @var array<string, string> */
    public const LAYOUT_PRIORITIES = [
        'kapasitas_produk' => 'Kapasitas produk',
        'kenyamanan_pelanggan' => 'Kenyamanan pelanggan',
        'keseimbangan' => 'Keseimbangan',
    ];

    /**
     * Legacy concept labels kept for backward-compatible resource rendering.
     *
     * @var array<string, string>
     */
    public const CONCEPTS = [
        'minimalis' => 'Minimalis',
        'modern' => 'Modern',
        'modern_minimarket' => 'Modern Minimarket',
        'traditional_grocery' => 'Traditional Grocery',
        'industrial' => 'Industrial',
        'custom' => 'Custom',
        'tradisional' => 'Tradisional',
        'efisien' => 'Efisien',
    ];

    public function __construct(
        private readonly AiClientInterface $ai,
        private readonly AiStoreLayoutService $layout,
    ) {}

    public function isConfigured(): bool
    {
        return $this->ai->isConfigured();
    }

    public function supportsImageEditing(): bool
    {
        return $this->ai->supportsImageEditing();
    }

    /**
     * Generate a 2D floor plan and recommendations from store inputs.
     *
     * @param  array{
     *     room_length_m: float,
     *     room_width_m: float,
     *     store_type: string,
     *     store_type_other?: ?string,
     *     door_position: string,
     *     rack_count?: ?int,
     *     layout_concept?: ?string,
     *     layout_priority?: ?string
     * }  $input
     * @return array{analysis: array<string, mixed>, plan: list<array<string, mixed>>}
     */
    public function analyzeStore(array $input): array
    {
        $length = round((float) $input['room_length_m'], 2);
        $width = round((float) $input['room_width_m'], 2);
        $area = round($length * $width, 2);
        $storeType = (string) $input['store_type'];
        $storeTypeLabel = self::STORE_TYPES[$storeType] ?? $storeType;
        $storeTypeOther = trim((string) ($input['store_type_other'] ?? ''));

        if ($storeType === 'lainnya' && $storeTypeOther !== '') {
            $storeTypeLabel = $storeTypeOther;
        }

        $doorPosition = (string) $input['door_position'];
        $doorLabel = self::DOOR_POSITIONS[$doorPosition] ?? $doorPosition;
        $rackCount = $this->nullableRackCount($input['rack_count'] ?? null);
        $layoutConcept = $this->nullableEnum($input['layout_concept'] ?? null, array_keys(self::LAYOUT_CONCEPTS));
        $layoutPriority = $this->nullableEnum($input['layout_priority'] ?? null, array_keys(self::LAYOUT_PRIORITIES));

        $decoded = $this->ai->chatJson([
            ['role' => 'system', 'content' => $this->systemPrompt()],
            ['role' => 'user', 'content' => $this->analysisPrompt(
                $length,
                $width,
                $area,
                $storeTypeLabel,
                $doorPosition,
                $doorLabel,
                $rackCount,
                $layoutConcept,
                $layoutPriority,
            )],
        ], temperature: 0.3, maxTokens: 8192);

        $assumptions = $this->mergeAssumptions(
            $this->stringList($decoded['assumptions'] ?? $decoded['asumsi'] ?? [], 15),
            $doorPosition,
            $rackCount,
            $layoutConcept,
            $layoutPriority,
        );
        $plan = $this->normalizePlan($decoded['plan'] ?? $decoded['layout'] ?? $decoded['objects'] ?? [], $length, $width, $doorPosition);
        $customerPaths = $this->normalizeCustomerPaths($decoded['customer_paths'] ?? $decoded['jalur_pelanggan'] ?? [], $plan);
        $areas = $this->normalizeAreas($decoded['areas'] ?? $decoded['pembagian_area'] ?? [], $length, $width);
        $recommendations = $this->normalizeRecommendations($decoded['recommendations'] ?? $decoded['saran'] ?? []);

        $analysis = [
            'status' => 'ok',
            'room' => [
                'length_m' => $length,
                'width_m' => $width,
                'area_m2' => $area,
            ],
            'store_type' => $storeType,
            'store_type_label' => $storeTypeLabel,
            'door_position' => $doorPosition,
            'door_position_label' => $doorLabel,
            'rack_count' => $rackCount,
            'layout_concept' => $layoutConcept,
            'layout_concept_label' => $layoutConcept !== null ? (self::LAYOUT_CONCEPTS[$layoutConcept] ?? $layoutConcept) : null,
            'layout_priority' => $layoutPriority,
            'layout_priority_label' => $layoutPriority !== null ? (self::LAYOUT_PRIORITIES[$layoutPriority] ?? $layoutPriority) : null,
            'assumptions' => $assumptions,
            'customer_paths' => $customerPaths,
            'areas' => $areas,
            'recommendations' => $recommendations,
            'layout_source' => 'gemini_json',
            'analyzed_at' => now()->toIso8601String(),
        ];

        return ['analysis' => $analysis, 'plan' => $plan];
    }

    /**
     * Kept for backward compatibility with older flows; not used by the photo-free advisor.
     *
     * @return string Path of the stored image on the public disk.
     */
    public function generateVisualization(AiStoreFloorPlan $design): string
    {
        /** @var list<string> $photos */
        $photos = $design->original_photos ?? [];
        $absolutePaths = collect($photos)
            ->map(fn (string $path): string => Storage::disk('public')->path($path))
            ->filter(fn (string $path): bool => is_file($path))
            ->values()
            ->all();

        if ($absolutePaths === []) {
            throw new AiServiceException('Visualisasi gambar tidak didukung pada mekanisme AI Store Advisor saat ini.');
        }

        $binary = $this->ai->imageEdit($absolutePaths, 'Store layout visualization');

        $path = 'ai-store-designs/'.$design->business_id.'/after-'.Str::uuid().'.png';
        Storage::disk('public')->put($path, $binary);

        $previous = $design->generated_image_path;
        if (is_string($previous) && $previous !== '' && $previous !== $path) {
            Storage::disk('public')->delete($previous);
        }

        return $path;
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
Anda adalah konsultan tata letak toko ritel untuk pemilik toko di Indonesia.
Tugas Anda: membuat denah toko 2D dan rekomendasi penataan berdasarkan informasi yang diberikan pemilik toko.

Aturan wajib:
- Gunakan HANYA informasi yang diberikan. Jangan mengklaim kondisi fisik toko (misalnya lantai retak, pencahayaan redup, barang berantakan) yang tidak diberikan.
- Semua koordinat dan ukuran objek menggunakan satuan METER.
- Sistem koordinat denah 2D: x ke kanan, y ke bawah. Titik (0,0) adalah sudut kiri-atas ruangan.
- Semua objek harus berada di dalam batas ruangan dan tidak saling bertumpuk tanpa alasan.
- Posisi pintu harus sesuai input pemilik toko.
- Untuk objek yang ukurannya/posisinya tidak diketahui pasti, gunakan asumsi wajar dan cantumkan di "assumptions".

Balas HANYA dengan JSON valid (tanpa markdown) dengan struktur:
{
  "assumptions": [string],
  "areas": [{"name": string, "x": number, "y": number, "width": number, "height": number, "notes": string}],
  "customer_paths": [{"name": string, "description": string, "points": [{"x": number, "y": number}]}],
  "plan": [
    {"name": string, "type": "door|wall_rack|mid_rack|cashier_desk|stockroom|storage|aisle|window|zone", "x": number, "y": number, "width": number, "height": number, "rotation": 0, "estimated": boolean}
  ],
  "recommendations": [
    {"title": string, "explanation": string, "reason": string, "priority": "tinggi|sedang|rendah"}
  ]
}

Panduan denah:
- Sertakan objek pintu masuk ("door") pada posisi yang diminta.
- Sertakan rak produk ("wall_rack"/"mid_rack"), meja kasir ("cashier_desk"), area penyimpanan ("stockroom"/"storage") bila diperlukan, dan lorong/jalur pelanggan ("aisle").
- Lebar lorong pelanggan sebaiknya memadai (umumnya >= 0.8 m, sesuaikan ukuran toko).
- Gunakan penamaan objek yang jelas dalam Bahasa Indonesia.

Topik rekomendasi yang harus dicakup bila relevan:
1) Penempatan rak. 2) Posisi kasir. 3) Pemanfaatan ruang. 4) Jalur pelanggan. 5) Pengelompokan produk. 6) Kenyamanan pelanggan. 7) Efisiensi toko.
PROMPT;
    }

    private function analysisPrompt(
        float $length,
        float $width,
        float $area,
        string $storeTypeLabel,
        string $doorPosition,
        string $doorLabel,
        ?int $rackCount,
        ?string $layoutConcept,
        ?string $layoutPriority,
    ): string {
        $conceptLine = $layoutConcept !== null
            ? 'Konsep penataan: '.(self::LAYOUT_CONCEPTS[$layoutConcept] ?? $layoutConcept).'.'
            : 'Konsep penataan: tidak ditentukan (pilih yang paling sesuai).';
        $priorityLine = $layoutPriority !== null
            ? 'Prioritas penataan: '.(self::LAYOUT_PRIORITIES[$layoutPriority] ?? $layoutPriority).'.'
            : 'Prioritas penataan: seimbang antara kapasitas, kenyamanan, dan efisiensi.';
        $rackLine = $rackCount !== null
            ? "Jumlah rak yang dimiliki pemilik toko: {$rackCount} rak. Rencanakan denah dengan mempertimbangkan jumlah rak ini (gunakan asumsi wajar bila ukuran rak tidak diberikan). Beri nama rak yang jelas dan ringkas, misalnya Rak Dinding 1, Rak Tengah 1."
            : 'Jumlah rak: tidak disebutkan (buat asumsi jumlah rak yang masuk akal untuk ukuran dan jenis toko). Beri nama rak yang jelas dan ringkas.';
        $doorWall = match ($doorPosition) {
            'depan' => 'sisi BAWAH denah (dinding y = lebar ruangan)',
            'belakang' => 'sisi ATAS denah (dinding y = 0)',
            'kiri' => 'sisi KIRI denah (dinding x = 0)',
            'kanan' => 'sisi KANAN denah (dinding x = panjang ruangan)',
            default => 'sesuai input pemilik toko',
        };

        return <<<PROMPT
Buat denah toko 2D dan rekomendasi penataan untuk toko berikut.

Ukuran toko: panjang {$length} m, lebar {$width} m, luas {$area} m² (dihitung otomatis).
Jenis toko: {$storeTypeLabel}.
Posisi pintu masuk: {$doorLabel}, letakkan pintu pada {$doorWall}.
{$rackLine}
{$conceptLine}
{$priorityLine}

Keluarkan JSON sesuai struktur yang diminta: assumptions, areas (pembagian area), customer_paths (jalur pelanggan), plan (objek denah dengan koordinat meter), dan recommendations (judul, penjelasan, alasan, prioritas).
PROMPT;
    }

    /**
     * @return list<string>
     */
    private function mergeAssumptions(array $assumptions, string $doorPosition, ?int $rackCount, ?string $layoutConcept, ?string $layoutPriority): array
    {
        $items = $assumptions;

        if ($rackCount === null) {
            $items[] = 'Jumlah rak tidak diisi; AI membuat asumsi jumlah rak yang sesuai dengan ukuran dan jenis toko.';
        }

        if ($layoutConcept === null) {
            $items[] = 'Konsep penataan tidak dipilih; AI memilih pendekatan yang paling sesuai dengan ukuran dan jenis toko.';
        }

        if ($layoutPriority === null) {
            $items[] = 'Prioritas penataan tidak dipilih; rekomendasi dibuat seimbang antara kapasitas, kenyamanan, dan efisiensi.';
        }

        $items[] = 'Posisi pintu masuk mengikuti input pemilik toko: '.(self::DOOR_POSITIONS[$doorPosition] ?? $doorPosition).'.';
        $items[] = 'Ukuran dan posisi rak/objek adalah asumsi wajar untuk perencanaan; sesuaikan dengan kondisi nyata toko.';

        return array_values(array_unique(array_filter($items, fn (string $item): bool => $item !== '')));
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function normalizePlan(mixed $value, float $length, float $width, string $doorPosition): array
    {
        $raw = is_array($value) ? array_values($value) : [];
        $objects = [];
        $hasDoor = false;

        foreach ($raw as $item) {
            if (! is_array($item)) {
                continue;
            }

            $type = $this->normalizeObjectType((string) ($item['type'] ?? 'zone'));
            $objectWidth = $this->clampNumber($item['width'] ?? $item['w'] ?? 1.0, 0.2, $length, 1.0);
            $objectHeight = $this->clampNumber($item['height'] ?? $item['h'] ?? 1.0, 0.2, $width, 1.0);
            $x = $this->clampNumber($item['x'] ?? 0.0, 0.0, max(0.0, $length - $objectWidth), 0.0);
            $y = $this->clampNumber($item['y'] ?? 0.0, 0.0, max(0.0, $width - $objectHeight), 0.0);

            $name = $this->shortString($item['name'] ?? $item['label'] ?? $this->defaultObjectName($type), 80);
            $color = is_string($item['color'] ?? null) && preg_match('/^#[0-9A-Fa-f]{6}$/', (string) $item['color'])
                ? (string) $item['color']
                : (AiStoreLayoutService::TYPE_COLORS[$type] ?? '#94A3B8');

            $objects[] = [
                'id' => 'obj-'.(count($objects) + 1),
                'name' => $name !== '' ? $name : $this->defaultObjectName($type),
                'type' => $type,
                'x' => round($x, 2),
                'y' => round($y, 2),
                'width' => round($objectWidth, 2),
                'height' => round($objectHeight, 2),
                'rotation' => in_array((int) ($item['rotation'] ?? 0), [0, 90, 180, 270], true) ? (int) $item['rotation'] : 0,
                'color' => $color,
                'estimated' => (bool) ($item['estimated'] ?? true),
            ];

            if ($type === 'door') {
                $hasDoor = true;
            }
        }

        if (! $hasDoor) {
            $objects[] = $this->doorObjectForPosition($doorPosition, $length, $width, count($objects) + 1);
        }

        return $objects;
    }

    /**
     * @return array<string, mixed>
     */
    private function doorObjectForPosition(string $doorPosition, float $length, float $width, int $index): array
    {
        $doorWidth = min(1.0, max(0.8, $length * 0.15));
        $doorDepth = 0.3;

        [$x, $y, $w, $h] = match ($doorPosition) {
            'belakang' => [round(max(0.0, ($length - $doorWidth) / 2), 2), 0.0, $doorWidth, $doorDepth],
            'kiri' => [0.0, round(max(0.0, ($width - $doorWidth) / 2), 2), $doorDepth, $doorWidth],
            'kanan' => [round(max(0.0, $length - $doorDepth), 2), round(max(0.0, ($width - $doorWidth) / 2), 2), $doorDepth, $doorWidth],
            default => [round(max(0.0, ($length - $doorWidth) / 2), 2), round(max(0.0, $width - $doorDepth), 2), $doorWidth, $doorDepth],
        };

        return [
            'id' => 'obj-'.$index,
            'name' => 'Pintu Masuk',
            'type' => 'door',
            'x' => round((float) $x, 2),
            'y' => round((float) $y, 2),
            'width' => round((float) $w, 2),
            'height' => round((float) $h, 2),
            'rotation' => 0,
            'color' => AiStoreLayoutService::TYPE_COLORS['door'] ?? '#F59E0B',
            'estimated' => false,
        ];
    }

    private function normalizeObjectType(string $type): string
    {
        $type = strtolower(trim($type));

        return match ($type) {
            'door', 'pintu' => 'door',
            'wall_rack', 'rak_dinding', 'shelf_wall' => 'wall_rack',
            'mid_rack', 'rak_tengah', 'gondola', 'shelf_mid' => 'mid_rack',
            'cashier_desk', 'cashier', 'kasir', 'meja_kasir' => 'cashier_desk',
            'stockroom', 'gudang', 'backroom' => 'stockroom',
            'storage', 'penyimpanan', 'area_penyimpanan' => 'storage',
            'aisle', 'lorong', 'jalur', 'path' => 'aisle',
            'window', 'jendela' => 'window',
            'zone', 'area' => 'zone',
            default => array_key_exists($type, AiStoreLayoutService::TYPE_COLORS) ? $type : 'zone',
        };
    }

    private function defaultObjectName(string $type): string
    {
        return match ($type) {
            'door' => 'Pintu Masuk',
            'wall_rack' => 'Rak Dinding',
            'mid_rack' => 'Rak Tengah',
            'cashier_desk' => 'Meja Kasir',
            'stockroom' => 'Gudang',
            'storage' => 'Area Penyimpanan',
            'aisle' => 'Lorong Pelanggan',
            'window' => 'Jendela',
            default => 'Area Toko',
        };
    }

    /**
     * @param  list<array<string, mixed>>  $plan
     * @return list<array<string, mixed>>
     */
    private function normalizeCustomerPaths(mixed $value, array $plan): array
    {
        if (is_array($value) && $value !== []) {
            $paths = [];

            foreach ($value as $item) {
                if (! is_array($item)) {
                    continue;
                }

                $paths[] = [
                    'name' => $this->shortString($item['name'] ?? 'Jalur Pelanggan', 80),
                    'description' => $this->shortString($item['description'] ?? '', 300),
                    'points' => $this->normalizePoints($item['points'] ?? []),
                ];

                if (count($paths) >= 8) {
                    break;
                }
            }

            if ($paths !== []) {
                return $paths;
            }
        }

        $aisles = collect($plan)->where('type', 'aisle')->values();

        return $aisles->isEmpty() ? [] : [[
            'name' => 'Jalur Pelanggan Utama',
            'description' => 'Area lorong utama untuk pergerakan pelanggan di dalam toko.',
            'points' => [],
        ]];
    }

    /**
     * @return list<array{x: float, y: float}>
     */
    private function normalizePoints(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $points = [];

        foreach ($value as $point) {
            if (! is_array($point) || ! is_numeric($point['x'] ?? null) || ! is_numeric($point['y'] ?? null)) {
                continue;
            }

            $points[] = ['x' => round((float) $point['x'], 2), 'y' => round((float) $point['y'], 2)];

            if (count($points) >= 20) {
                break;
            }
        }

        return $points;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function normalizeAreas(mixed $value, float $length, float $width): array
    {
        if (! is_array($value)) {
            return [];
        }

        $areas = [];

        foreach ($value as $item) {
            if (! is_array($item)) {
                continue;
            }

            $name = $this->shortString($item['name'] ?? '', 80);

            if ($name === '') {
                continue;
            }

            $areaWidth = $this->clampNumber($item['width'] ?? $item['w'] ?? 1.0, 0.2, $length, 1.0);
            $areaHeight = $this->clampNumber($item['height'] ?? $item['h'] ?? 1.0, 0.2, $width, 1.0);

            $areas[] = [
                'name' => $name,
                'x' => round($this->clampNumber($item['x'] ?? 0.0, 0.0, max(0.0, $length - $areaWidth), 0.0), 2),
                'y' => round($this->clampNumber($item['y'] ?? 0.0, 0.0, max(0.0, $width - $areaHeight), 0.0), 2),
                'width' => round($areaWidth, 2),
                'height' => round($areaHeight, 2),
                'notes' => $this->shortString($item['notes'] ?? '', 200),
            ];

            if (count($areas) >= 12) {
                break;
            }
        }

        return $areas;
    }

    /**
     * @return list<array{title: string, explanation: string, reason: string, priority: string}>
     */
    private function normalizeRecommendations(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $recommendations = [];

        foreach ($value as $item) {
            if (is_string($item)) {
                $text = trim($item);

                if ($text !== '') {
                    $recommendations[] = [
                        'title' => $this->shortString($text, 80),
                        'explanation' => $this->shortString($text, 300),
                        'reason' => 'Meningkatkan kenyamanan dan efisiensi penataan toko.',
                        'priority' => 'sedang',
                    ];
                }
            } elseif (is_array($item)) {
                $title = $this->shortString($item['title'] ?? $item['judul'] ?? '', 120);
                $explanation = $this->shortString($item['explanation'] ?? $item['penjelasan'] ?? $item['description'] ?? '', 500);
                $reason = $this->shortString($item['reason'] ?? $item['alasan'] ?? '', 300);
                $priority = strtolower(trim((string) ($item['priority'] ?? $item['prioritas'] ?? 'sedang')));

                if (! in_array($priority, ['tinggi', 'sedang', 'rendah'], true)) {
                    $priority = 'sedang';
                }

                if ($title === '' && $explanation === '') {
                    continue;
                }

                if ($title === '') {
                    $title = $this->shortString($explanation, 80);
                }

                $recommendations[] = [
                    'title' => $title,
                    'explanation' => $explanation !== '' ? $explanation : $title,
                    'reason' => $reason !== '' ? $reason : 'Mendukung penataan toko yang lebih rapi dan efisien.',
                    'priority' => $priority,
                ];
            }

            if (count($recommendations) >= 12) {
                break;
            }
        }

        return $recommendations;
    }

    private function nullableEnum(mixed $value, array $allowed): ?string
    {
        $value = is_string($value) ? strtolower(trim($value)) : null;

        return $value !== null && $value !== '' && in_array($value, $allowed, true) ? $value : null;
    }

    private function nullableRackCount(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (! is_numeric($value)) {
            return null;
        }

        return max(0, min(100, (int) $value));
    }

    private function shortString(mixed $value, int $max = 500): string
    {
        return mb_substr(trim((string) $value), 0, $max);
    }

    /**
     * @return list<string>
     */
    private function stringList(mixed $value, int $max = 20): array
    {
        if (! is_array($value)) {
            return [];
        }

        $items = [];

        foreach ($value as $item) {
            $text = trim((string) $item);

            if ($text !== '') {
                $items[] = mb_substr($text, 0, 300);
            }

            if (count($items) >= $max) {
                break;
            }
        }

        return $items;
    }

    private function clampNumber(mixed $value, float $min, float $max, float $default): float
    {
        $parsed = is_numeric($value) ? (float) $value : $default;

        return min(max($parsed, $min), $max);
    }
}
