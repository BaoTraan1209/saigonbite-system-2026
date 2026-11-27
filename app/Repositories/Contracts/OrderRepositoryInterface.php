<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function list(): LengthAwarePaginator;
    public function findById(int $id): Order;
    public function findByIdempotencyKey(int $customerId, string $key): ?Order;
    public function lockCustomer(int $customerId): void;
    public function create(array $data): Order;
}
