<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreOutletRequest;
use App\Http\Requests\UpdateOutletRequest;
use App\Http\Resources\OutletResource;
use App\Models\Business;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Keeps the old endpoint shape while the store and its single location share one record.
 */
class OutletController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $business = $this->business($request);

        return $this->success([
            'items' => OutletResource::collection(collect([$business])),
            'pagination' => ['page' => 1, 'limit' => 1, 'total' => 1],
        ], 'Lokasi toko berhasil diambil.');
    }

    public function store(StoreOutletRequest $request): JsonResponse
    {
        $business = $this->business($request);
        $business->update([
            'location_name' => $request->validated('name'),
            'address' => $request->validated('address'),
            'phone' => $request->validated('phone'),
        ]);

        return $this->success(new OutletResource($business->fresh()), 'Lokasi toko diperbarui.', 200);
    }

    public function show(Request $request, Business $outlet): JsonResponse
    {
        abort_unless($outlet->id === $this->business($request)->id, 404);

        return $this->success(new OutletResource($outlet), 'Lokasi toko berhasil diambil.');
    }

    public function update(UpdateOutletRequest $request, Business $outlet): JsonResponse
    {
        abort_unless($outlet->id === $this->business($request)->id, 404);
        $data = $request->validated();
        $businessData = [];

        foreach (['name' => 'location_name', 'address' => 'address', 'phone' => 'phone', 'status' => 'status'] as $legacyField => $businessField) {
            if (array_key_exists($legacyField, $data)) {
                $businessData[$businessField] = $data[$legacyField];
            }
        }

        $outlet->update($businessData);

        return $this->success(new OutletResource($outlet->fresh()), 'Data toko diperbarui.');
    }

    public function destroy(Request $request, Business $outlet): JsonResponse
    {
        abort_unless($outlet->id === $this->business($request)->id, 404);
        $outlet->update(['status' => 'inactive']);

        return $this->success(null, 'Toko dinonaktifkan.');
    }
}
