<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (! $this->filled('business_id') && $this->filled('outlet_id')) {
            $this->merge(['business_id' => $this->input('outlet_id')]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_id' => ['required', 'integer', 'exists:businesses,id'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'member_phone' => ['nullable', 'string', 'max:30'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'promo_ids' => ['nullable', 'array'],
            'promo_ids.*' => ['integer', 'distinct', 'exists:promos,id'],
            'coupon_code' => ['nullable', 'string', 'max:32'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', 'max:30'],
            'paid' => ['required', 'numeric', 'min:0'],
            'points_to_member' => ['nullable', 'integer', 'min:0', 'max:1000000000'],
            'points_to_redeem' => ['nullable', 'integer', 'min:0', 'max:1000000000'],
        ];
    }
}
