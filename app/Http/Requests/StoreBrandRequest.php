<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBrandRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150', Rule::unique('brands', 'name')],
            'description' => ['nullable', 'string', 'max:500'],
            'status' => ['sometimes', Rule::in(['active', 'inactive'])],
        ];
    }
}
