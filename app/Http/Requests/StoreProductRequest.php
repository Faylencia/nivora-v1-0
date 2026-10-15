<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:80'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:30'],
            'image' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'is_active' => ['nullable', 'boolean'],
            'business_id' => ['nullable', 'integer', 'exists:businesses,id', 'required_with:initial_stock'],
            'initial_stock' => ['nullable', 'integer', 'min:0', 'required_with:business_id'],
            'minimum_stock' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
