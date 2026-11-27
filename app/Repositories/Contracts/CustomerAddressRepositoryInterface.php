<?php

namespace App\Repositories\Contracts;

use App\Models\CustomerAddress;
use Illuminate\Database\Eloquent\Collection;

interface CustomerAddressRepositoryInterface
{
    public function forCustomer(int $customerId): Collection;
    public function createForCustomer(int $customerId, array $data): CustomerAddress;
    public function findForCustomer(int $customerId, int $addressId): CustomerAddress;
}
