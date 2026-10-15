<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdjustInventoryRequest extends FormRequest
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
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'business_id' => ['required', 'integer', 'exists:businesses,id'],
            'quantity' => ['required', 'integer'],
            'minimum_stock' => ['nullable', 'integer', 'min:0'],
            'maximum_stock' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
