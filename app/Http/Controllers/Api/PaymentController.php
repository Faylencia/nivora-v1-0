<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $business = $this->business($request);
        $payments = Payment::query()
            ->with(['sale.customer', 'sale.outlet'])
            ->whereHas('sale', fn ($query) => $query->where('business_id', $business->id))
            ->orderByDesc('paid_at')
            ->orderByDesc('id')
            ->paginate(min(100, max(1, $request->integer('limit', 20))));

        return $this->success([
            'items' => PaymentResource::collection($payments->items()),
            'pagination' => [
                'page' => $payments->currentPage(),
                'limit' => $payments->perPage(),
                'total' => $payments->total(),
            ],
        ], 'Payments retrieved successfully');
    }
}
