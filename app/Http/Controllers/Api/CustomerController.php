<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $business = $this->business($request);
        $customers = $business->customers()
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = (string) $request->string('search');
                $query->where(function ($q) use ($search): void {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(min(100, max(1, $request->integer('limit', 20))));

        return $this->success([
            'items' => CustomerResource::collection($customers->items()),
            'pagination' => [
                'page' => $customers->currentPage(),
                'limit' => $customers->perPage(),
                'total' => $customers->total(),
            ],
        ], 'Customers retrieved successfully');
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = $this->business($request)->customers()->create($request->validated());

        return $this->success(new CustomerResource($customer), 'Customer created successfully', 201);
    }

    public function show(Request $request, Customer $customer): JsonResponse
    {
        $business = $this->business($request);
        abort_unless($customer->business_id === $business->id, 404);

        return $this->success(new CustomerResource($customer), 'Customer retrieved successfully');
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        $business = $this->business($request);
        abort_unless($customer->business_id === $business->id, 404);
        $customer->update($request->validated());

        return $this->success(new CustomerResource($customer->fresh()), 'Customer updated successfully');
    }
}
