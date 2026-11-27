<?php

namespace App\Repositories;

use App\Models\CustomerAddress;
use App\Repositories\Contracts\CustomerAddressRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CustomerAddressRepository implements CustomerAddressRepositoryInterface
{
    public function forCustomer(int $customerId): Collection
    {
        return CustomerAddress::where('user_id', $customerId)->orderByDesc('is_default')->latest()->get();
    }

    public function createForCustomer(int $customerId, array $data): CustomerAddress
    {
        return CustomerAddress::create([...$data, 'user_id' => $customerId]);
    }

    public function findForCustomer(int $customerId, int $addressId): CustomerAddress
    {
        return CustomerAddress::where('user_id', $customerId)->findOrFail($addressId);
    }
}
