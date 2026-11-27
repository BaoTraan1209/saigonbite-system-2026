<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerAddressRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'recipient_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'street' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:150'],
            'city' => ['required', 'string', 'max:150'],
            'additional_details' => ['nullable', 'string', 'max:500'],
            'is_default' => ['sometimes', 'boolean'],
        ];
    }
}
