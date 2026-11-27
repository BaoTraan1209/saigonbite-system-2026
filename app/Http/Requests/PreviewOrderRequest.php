<?php

namespace App\Http\Requests;

class PreviewOrderRequest extends StoreOrderRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        unset($rules['idempotency_key']);
        return $rules;
    }
}
