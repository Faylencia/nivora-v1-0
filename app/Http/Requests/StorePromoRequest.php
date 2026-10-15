<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePromoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'code' => ['nullable', 'string', 'max:32', 'alpha_dash'],
            'description' => ['nullable', 'string'],
            'type' => ['required', Rule::in(['product', 'transaction'])],
            // The ERD has one discount_value column and no discount_type column.
            // MVP convention: value is a percentage (0-100).
            'discount_value' => ['required', 'numeric', 'min:0', 'max:100'],
            'min_transaction' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['sometimes', Rule::in(['active', 'inactive'])],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer', 'distinct', 'exists:products,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($this->input('type') === 'product' && count((array) $this->input('product_ids', [])) === 0) {
                $validator->errors()->add('product_ids', 'Product promo harus memiliki minimal satu produk.');
            }

            if ($this->input('type') === 'transaction' && $this->filled('product_ids')) {
                $validator->errors()->add('product_ids', 'Transaction promo tidak memakai product_ids.');
            }
        });
    }
}
