<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePromoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'code' => ['sometimes', 'nullable', 'string', 'max:32', 'alpha_dash'],
            'description' => ['nullable', 'string'],
            'type' => ['sometimes', Rule::in(['product', 'transaction'])],
            'discount_value' => ['sometimes', 'required', 'numeric', 'min:0', 'max:100'],
            'min_transaction' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['sometimes', 'required', 'date'],
            'end_date' => ['sometimes', 'required', 'date', 'after_or_equal:start_date'],
            'status' => ['sometimes', Rule::in(['active', 'inactive'])],
            'product_ids' => ['sometimes', 'array'],
            'product_ids.*' => ['integer', 'distinct', 'exists:products,id'],
        ];
    }
}
