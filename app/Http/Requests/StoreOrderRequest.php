<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $customerId = $this->user()->id;

        return [
            'hub_id' => ['required', 'integer', 'exists:hubs,id'],
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'customer_name' => ['required_without:address_id', 'nullable', 'string', 'max:150'],
            'customer_phone' => ['required_without:address_id', 'nullable', 'string', 'max:30'],
            'delivery_address' => ['required_without:address_id', 'nullable', 'string', 'max:1000'],
            'address_id' => [
                'required_without:delivery_address', 'nullable', 'integer',
                Rule::exists('customer_addresses', 'id')->where('user_id', $customerId),
            ],
            'voucher_code' => ['sometimes', 'nullable', 'string', 'max:100'],
            'payment_method' => ['required', Rule::in(['COD', 'CASH_ON_DELIVERY'])],
            'idempotency_key' => ['required', 'uuid'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.menu_item_id' => ['required', 'integer', 'distinct', 'exists:menu_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:50'],
        ];
    }
}
