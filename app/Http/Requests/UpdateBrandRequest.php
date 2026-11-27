<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBrandRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $brand = $this->route('brand');
        $brandId = is_object($brand) ? $brand->id : $brand;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:150', Rule::unique('brands', 'name')->ignore($brandId)],
            'description' => ['sometimes', 'nullable', 'string', 'max:500'],
            'status' => ['sometimes', Rule::in(['active', 'inactive'])],
        ];
    }
}
