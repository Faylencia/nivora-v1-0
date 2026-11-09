<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\AiServiceException;
use App\Http\Resources\AiStoreFloorPlanResource;
use App\Models\AiStoreFloorPlan;
use App\Services\AiStoreAdvisorService;
use App\Services\AiStoreLayoutService;
use App\Services\BusinessContext;
use App\Services\PlatformActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AiStoreFloorPlanController extends ApiController
{
    public function __construct(
        protected readonly AiStoreLayoutService $layout,
        protected readonly AiStoreAdvisorService $advisor,
    ) {
        parent::__construct(
            app(BusinessContext::class),
            app(PlatformActivityLogger::class),
        );
    }

    public function index(Request $request): JsonResponse
    {
        $business = $this->business($request);

        $plans = AiStoreFloorPlan::query()
            ->where('business_id', $business->id)
            ->latest('id')
            ->limit(20)
            ->get();

        return $this->success(AiStoreFloorPlanResource::collection($plans), 'AI store designs retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {
        @set_time_limit(300);

        $business = $this->business($request);
        $user = $this->user($request);

        $data = $request->validate([
            'room_length_m' => ['required', 'numeric', 'min:0.5', 'max:50'],
            'room_width_m' => ['required', 'numeric', 'min:0.5', 'max:50'],
            'store_type' => ['required', 'string', Rule::in(array_keys(AiStoreAdvisorService::STORE_TYPES))],
            'store_type_other' => ['required_if:store_type,lainnya', 'nullable', 'string', 'max:100'],
            'door_position' => ['required', 'string', Rule::in(array_keys(AiStoreAdvisorService::DOOR_POSITIONS))],
            'rack_count' => ['nullable', 'integer', 'min:0', 'max:100'],
            'layout_concept' => ['nullable', 'string', Rule::in(array_keys(AiStoreAdvisorService::LAYOUT_CONCEPTS))],
            'layout_priority' => ['nullable', 'string', Rule::in(array_keys(AiStoreAdvisorService::LAYOUT_PRIORITIES))],
        ], [
            'room_length_m.required' => 'Panjang toko wajib diisi.',
            'room_width_m.required' => 'Lebar toko wajib diisi.',
            'store_type.required' => 'Jenis toko wajib dipilih.',
            'store_type.in' => 'Jenis toko tidak dikenal.',
            'store_type_other.required_if' => 'Tuliskan jenis toko Anda.',
            'door_position.required' => 'Posisi pintu masuk wajib dipilih.',
            'door_position.in' => 'Posisi pintu masuk tidak dikenal.',
            'rack_count.integer' => 'Jumlah rak harus berupa angka.',
            'rack_count.min' => 'Jumlah rak tidak boleh kurang dari 0.',
            'rack_count.max' => 'Jumlah rak maksimal 100.',
            'layout_concept.in' => 'Konsep penataan tidak dikenal.',
            'layout_priority.in' => 'Prioritas penataan tidak dikenal.',
        ]);

        $length = round((float) $data['room_length_m'], 2);
        $width = round((float) $data['room_width_m'], 2);
        $area = round($length * $width, 2);
        $storeType = (string) $data['store_type'];
        $storeTypeOther = isset($data['store_type_other']) ? trim((string) $data['store_type_other']) : null;
        $doorPosition = (string) $data['door_position'];
        $rackCount = array_key_exists('rack_count', $data) && $data['rack_count'] !== null && $data['rack_count'] !== ''
            ? max(0, min(100, (int) $data['rack_count']))
            : null;
        $layoutConcept = isset($data['layout_concept']) && $data['layout_concept'] !== '' ? (string) $data['layout_concept'] : null;
        $layoutPriority = isset($data['layout_priority']) && $data['layout_priority'] !== '' ? (string) $data['layout_priority'] : null;

        $floorPlan = AiStoreFloorPlan::create([
            'business_id' => $business->id,
            'outlet_id' => $business->id,
            'user_id' => $user->id,
            'status' => 'draft',
            'concept' => $layoutConcept ?? 'minimalis',
            'room_length_m' => $length,
            'room_width_m' => $width,
            'input_summary' => [
                'room_length_m' => $length,
                'room_width_m' => $width,
                'room_area_m2' => $area,
                'store_type' => $storeType,
                'store_type_other' => $storeTypeOther,
                'door_position' => $doorPosition,
                'rack_count' => $rackCount,
                'layout_concept' => $layoutConcept,
                'layout_priority' => $layoutPriority,
            ],
            'original_photos' => [],
            'generated_at' => now(),
        ]);

        try {
            ['analysis' => $analysis, 'plan' => $plan] = $this->advisor->analyzeStore([
                'room_length_m' => $length,
                'room_width_m' => $width,
                'store_type' => $storeType,
                'store_type_other' => $storeTypeOther,
                'door_position' => $doorPosition,
                'rack_count' => $rackCount,
                'layout_concept' => $layoutConcept,
                'layout_priority' => $layoutPriority,
            ]);
        } catch (AiServiceException $exception) {
            $floorPlan->update([
                'analysis_result' => [
                    'status' => 'failed',
                    'message' => $exception->getMessage(),
                ],
            ]);

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'data' => [
                    'design_id' => $floorPlan->id,
                    'status' => 'failed',
                ],
            ], 502);
        }

        $floorPlan->update([
            'analysis_result' => $analysis,
            'plan' => $plan,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Analisis Gemini selesai. Denah 2D dan rekomendasi penataan toko berhasil dibuat.',
            'data' => new AiStoreFloorPlanResource($floorPlan->refresh()),
        ], 201);
    }

    public function show(Request $request, AiStoreFloorPlan $floorPlan): JsonResponse
    {
        $business = $this->business($request);
        abort_unless($floorPlan->business_id === $business->id, 404);

        return $this->success(new AiStoreFloorPlanResource($floorPlan), 'AI store design retrieved successfully');
    }

    public function update(Request $request, AiStoreFloorPlan $floorPlan): JsonResponse
    {
        $business = $this->business($request);
        abort_unless($floorPlan->business_id === $business->id, 404);

        $data = $request->validate([
            'plan' => ['required', 'array'],
        ]);

        $length = (float) $floorPlan->room_length_m;
        $width = (float) $floorPlan->room_width_m;
        $planErrors = $this->layout->validatePlan($data['plan'], $length, $width);

        if ($planErrors !== []) {
            throw ValidationException::withMessages([
                'plan' => $planErrors,
            ]);
        }

        $floorPlan->update([
            'plan' => $data['plan'],
            'status' => 'saved',
        ]);

        return $this->success(new AiStoreFloorPlanResource($floorPlan->fresh()), 'Denah berhasil disimpan.');
    }

    public function regenerate(Request $request, AiStoreFloorPlan $floorPlan): JsonResponse
    {
        @set_time_limit(300);

        $business = $this->business($request);
        abort_unless($floorPlan->business_id === $business->id, 404);

        $inputSummary = $floorPlan->input_summary ?? [];

        if (! is_array($inputSummary) || ! isset($inputSummary['room_length_m'], $inputSummary['room_width_m'], $inputSummary['store_type'], $inputSummary['door_position'])) {
            throw ValidationException::withMessages([
                'design' => ['Informasi toko untuk analisis ulang tidak tersedia. Buat desain baru dari form AI Store Advisor.'],
            ]);
        }

        try {
            ['analysis' => $analysis, 'plan' => $plan] = $this->advisor->analyzeStore([
                'room_length_m' => (float) ($inputSummary['room_length_m'] ?? $floorPlan->room_length_m),
                'room_width_m' => (float) ($inputSummary['room_width_m'] ?? $floorPlan->room_width_m),
                'store_type' => (string) ($inputSummary['store_type'] ?? 'kelontong'),
                'store_type_other' => $inputSummary['store_type_other'] ?? null,
                'door_position' => (string) ($inputSummary['door_position'] ?? 'depan'),
                'rack_count' => array_key_exists('rack_count', $inputSummary) ? $inputSummary['rack_count'] : null,
                'layout_concept' => $inputSummary['layout_concept'] ?? null,
                'layout_priority' => $inputSummary['layout_priority'] ?? null,
            ]);
        } catch (AiServiceException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 502);
        }

        $floorPlan->update([
            'analysis_result' => $analysis,
            'plan' => $plan,
            'concept' => $inputSummary['layout_concept'] ?? $floorPlan->concept,
            'generated_at' => now(),
        ]);

        return $this->success(new AiStoreFloorPlanResource($floorPlan->fresh()), 'Analisis Gemini berhasil dijalankan ulang.');
    }

    public function relayout(Request $request, AiStoreFloorPlan $floorPlan): JsonResponse
    {
        $business = $this->business($request);
        abort_unless($floorPlan->business_id === $business->id, 404);

        $analysis = $floorPlan->analysis_result ?? [];

        if (! is_array($analysis) || ($analysis['status'] ?? null) !== 'ok') {
            throw ValidationException::withMessages([
                'analysis' => ['Tidak ada hasil analisis yang bisa dirapikan. Jalankan analisis Gemini terlebih dahulu.'],
            ]);
        }

        $inputSummary = is_array($floorPlan->input_summary) ? $floorPlan->input_summary : [];
        $plan = $this->layout->normalizePlan(
            is_array($floorPlan->plan) ? $floorPlan->plan : [],
            (float) $floorPlan->room_length_m,
            (float) $floorPlan->room_width_m,
            isset($inputSummary['door_position']) ? (string) $inputSummary['door_position'] : null,
        );

        $analysis['layout_source'] = 'normalized_from_existing_plan';
        $analysis['assumptions'] = array_values(array_unique(array_merge(
            is_array($analysis['assumptions'] ?? null) ? $analysis['assumptions'] : [],
            ['Denah dirapikan otomatis tanpa biaya API tambahan: objek disesuaikan agar tetap berada di dalam ruangan.'],
        )));

        $floorPlan->update([
            'analysis_result' => $analysis,
            'plan' => $plan,
            'status' => 'draft',
        ]);

        return $this->success(new AiStoreFloorPlanResource($floorPlan->fresh()), 'Denah berhasil dirapikan otomatis.');
    }

    public function destroy(Request $request, AiStoreFloorPlan $floorPlan): JsonResponse
    {
        $business = $this->business($request);
        abort_unless($floorPlan->business_id === $business->id, 404);

        $paths = collect($floorPlan->original_photos ?? [])
            ->filter(fn (mixed $path): bool => is_string($path) && $path !== '')
            ->all();

        if (is_string($floorPlan->generated_image_path) && $floorPlan->generated_image_path !== '') {
            $paths[] = $floorPlan->generated_image_path;
        }

        if ($paths !== []) {
            Storage::disk('public')->delete($paths);
        }

        $floorPlan->delete();

        return $this->success(null, 'Desain AI dihapus.');
    }
}
