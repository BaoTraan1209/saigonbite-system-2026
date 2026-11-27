<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMenuItemRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'brand_id' => ['required', 'integer', Rule::exists('brands', 'id')],
            'name' => ['required', 'string', 'max:150', Rule::unique('menu_items', 'name')->where('brand_id', $this->input('brand_id'))],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
            'is_active' => ['sometimes', 'boolean'],
            'is_available' => ['sometimes', 'boolean'],
        ];
    }
}
