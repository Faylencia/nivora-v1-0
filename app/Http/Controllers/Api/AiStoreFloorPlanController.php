<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\AiStoreFloorPlanResource;
use App\Models\AiStoreFloorPlan;
use App\Services\AiStoreLayoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AiStoreFloorPlanController extends ApiController
{
    public function __construct(
        protected readonly AiStoreLayoutService $layout,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $business = $this->business($request);

        $plans = AiStoreFloorPlan::query()
            ->where('business_id', $business->id)
            ->latest('id')
            ->limit(20)
            ->get();

        return $this->success(AiStoreFloorPlanResource::collection($plans), 'AI store floor plans retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {
        $business = $this->business($request);
        $user = $this->user($request);
        $data = $request->validate([
            'room_length_m' => ['required', 'numeric', 'min:0.5', 'max:50'],
            'room_width_m' => ['required', 'numeric', 'min:0.5', 'max:50'],
            'door' => ['required', 'array'],
            'door.width' => ['nullable', 'numeric', 'min:0.5', 'max:3'],
            'door.height' => ['nullable', 'numeric', 'min:1', 'max:4'],
            'windows' => ['nullable', 'array'],
            'windows.*' => ['nullable', 'array'],
            'windows.*.x' => ['nullable', 'numeric', 'min:0'],
            'windows.*.y' => ['nullable', 'numeric', 'min:0'],
            'windows.*.width' => ['nullable', 'numeric', 'min:0.5', 'max:10'],
            'windows.*.height' => ['nullable', 'numeric', 'min:0.5', 'max:10'],
            'permanent_areas' => ['nullable', 'array'],
            'permanent_areas.*' => ['nullable', 'array'],
            'permanent_areas.*.type' => ['required_with:permanent_areas', 'string', 'max:50'],
            'permanent_areas.*.x' => ['required_with:permanent_areas', 'numeric', 'min:0'],
            'permanent_areas.*.y' => ['required_with:permanent_areas', 'numeric', 'min:0'],
            'permanent_areas.*.width' => ['required_with:permanent_areas', 'numeric', 'min:0.3', 'max:20'],
            'permanent_areas.*.height' => ['required_with:permanent_areas', 'numeric', 'min:0.3', 'max:20'],
        ]);

        $length = (float) $data['room_length_m'];
        $width = (float) $data['room_width_m'];
        $door = (array) ($data['door'] ?? []);

        if ((float) ($door['x'] ?? 0) > $length || (float) ($door['y'] ?? 0) > $width) {
            throw ValidationException::withMessages([
                'door' => ['Posisi pintu masuk berada di luar batas ruangan.'],
            ]);
        }

        foreach ((array) ($data['windows'] ?? []) as $window) {
            if ((float) ($window['x'] ?? 0) > $length || (float) ($window['y'] ?? 0) > $width) {
                throw ValidationException::withMessages([
                    'windows' => ['Posisi jendela berada di luar batas ruangan.'],
                ]);
            }
        }

        foreach ((array) ($data['permanent_areas'] ?? []) as $area) {
            if ((float) ($area['x'] ?? 0) > $length || (float) ($area['y'] ?? 0) > $width) {
                throw ValidationException::withMessages([
                    'permanent_areas' => ['Posisi area permanen berada di luar batas ruangan.'],
                ]);
            }
        }

        $layout = $this->layout->generate($data);
        $planErrors = $this->layout->validatePlan($layout['plan'], $length, $width);

        if ($planErrors !== []) {
            throw ValidationException::withMessages([
                'plan' => $planErrors,
            ]);
        }

        $floorPlan = AiStoreFloorPlan::create([
            'business_id' => $business->id,
            'outlet_id' => $business->id,
            'user_id' => $user->id,
            'status' => 'draft',
            'room_length_m' => $length,
            'room_width_m' => $width,
            'input_summary' => $data,
            'analysis_result' => $layout['analysis'],
            'plan' => $layout['plan'],
            'generated_at' => now(),
        ]);

        return $this->success(new AiStoreFloorPlanResource($floorPlan), 'AI store floor plan generated successfully', 201);
    }

    public function show(Request $request, AiStoreFloorPlan $floorPlan): JsonResponse
    {
        $business = $this->business($request);
        abort_unless($floorPlan->business_id === $business->id, 404);

        return $this->success(new AiStoreFloorPlanResource($floorPlan), 'AI store floor plan retrieved successfully');
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

        return $this->success(new AiStoreFloorPlanResource($floorPlan->fresh()), 'AI store floor plan updated successfully');
    }

    public function destroy(Request $request, AiStoreFloorPlan $floorPlan): JsonResponse
    {
        $business = $this->business($request);
        abort_unless($floorPlan->business_id === $business->id, 404);

        $floorPlan->delete();

        return $this->success(null, 'AI store floor plan deleted successfully');
    }
}
