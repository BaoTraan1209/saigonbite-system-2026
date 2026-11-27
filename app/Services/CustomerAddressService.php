<?php

namespace App\Services;

use App\Models\CustomerAddress;
use App\Repositories\Contracts\CustomerAddressRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CustomerAddressService
{
    public function __construct(private CustomerAddressRepositoryInterface $addresses) {}

    public function create(int $customerId, array $data): CustomerAddress
    {
        return DB::transaction(function () use ($customerId, $data): CustomerAddress {
            $isFirstAddress = ! $this->addresses->forCustomer($customerId)->isNotEmpty();
            $makeDefault = (bool) ($data['is_default'] ?? false) || $isFirstAddress;
            if ($makeDefault) {
                \App\Models\User::whereKey($customerId)->lockForUpdate()->firstOrFail();
                \App\Models\CustomerAddress::where('user_id', $customerId)->update(['is_default' => false]);
            }

            $data['is_default'] = $makeDefault;
            return $this->addresses->createForCustomer($customerId, $data);
        });
    }
}
