<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMenuItemRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $item = $this->route('menuItem');
        $itemId = is_object($item) ? $item->id : $item;
        $brandId = $this->input('brand_id', is_object($item) ? $item->brand_id : null);

        return [
            'brand_id' => ['sometimes', 'integer', Rule::exists('brands', 'id')],
            'name' => ['sometimes', 'required', 'string', 'max:150', Rule::unique('menu_items', 'name')->where('brand_id', $brandId)->ignore($itemId)],
            'description' => ['sometimes', 'nullable', 'string', 'max:500'],
            'price' => ['sometimes', 'required', 'numeric', 'gt:0', 'max:9999999999.99'],
            'is_active' => ['sometimes', 'boolean'],
            'is_available' => ['prohibited'],
        ];
    }
}
