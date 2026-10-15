<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\BusinessResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessController extends ApiController
{
    public function show(Request $request): JsonResponse
    {
        $business = $this->business($request);

        return $this->success(new BusinessResource($business), 'Business retrieved successfully');
    }

    public function update(Request $request): JsonResponse
    {
        $business = $this->business($request);
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:150'],
            'location_name' => ['sometimes', 'nullable', 'string', 'max:150'],
            'business_type' => ['sometimes', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'logo' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'in:active,inactive'],
        ]);

        $business->update($data);

        return $this->success(new BusinessResource($business->fresh()), 'Business updated successfully');
    }
}
