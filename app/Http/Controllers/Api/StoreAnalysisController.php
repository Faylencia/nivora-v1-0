<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\StoreAnalysisResource;
use App\Models\StoreAnalysis;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreAnalysisController extends ApiController
{
    public function store(Request $request): JsonResponse
    {
        $business = $this->business($request);
        if (! $request->filled('business_id') && $request->filled('outlet_id')) {
            $request->merge(['business_id' => $request->input('outlet_id')]);
        }
        $data = $request->validate([
            'business_id' => ['required', 'integer', 'exists:businesses,id'],
            'image' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
        ]);

        abort_unless((int) $data['business_id'] === $business->id && $business->status === 'active', 404);
        $path = $data['image']->store('store-analysis', 'public');

        $analysis = StoreAnalysis::create([
            'business_id' => $business->id,
            'business_id' => $business->id,
            'user_id' => $request->user()->id,
            'image_url' => Storage::disk('public')->url($path),
            'overall_score' => null,
            'analysis_result' => json_encode([
                'status' => 'pending',
                'message' => 'Image stored successfully. Computer vision provider is not configured yet.',
                'categories' => ['product placement', 'visual merchandising', 'lighting', 'layout', 'product visibility', 'customer flow'],
            ], JSON_THROW_ON_ERROR),
            'analyzed_at' => null,
        ]);

        return $this->success(new StoreAnalysisResource($analysis), 'Store image uploaded successfully; computer vision analysis is pending.', 201);
    }

    public function show(Request $request, StoreAnalysis $analysis): JsonResponse
    {
        $business = $this->business($request);
        abort_unless($analysis->business_id === $business->id, 404);

        return $this->success(new StoreAnalysisResource($analysis), 'Store analysis retrieved successfully');
    }
}
