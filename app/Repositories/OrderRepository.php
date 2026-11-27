<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\User;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderRepository implements OrderRepositoryInterface
{
    public function list(): LengthAwarePaginator
    {
        return Order::with([
            'hub',
            'items.menuItem',
            'delivery'
        ])
            ->latest()
            ->paginate(15);
    }

    public function findById(int $id): Order
    {
        return Order::with([
            'hub',
            'items.menuItem',
            'delivery'
        ])->findOrFail($id);
    }

    public function findByIdempotencyKey(int $customerId, string $key): ?Order
    {
        return Order::with(['hub', 'items.menuItem', 'delivery'])
            ->where('customer_id', $customerId)
            ->where('idempotency_key', $key)
            ->first();
    }

    public function lockCustomer(int $customerId): void
    {
        User::whereKey($customerId)->lockForUpdate()->firstOrFail();
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function update(Order $order, array $data): Order
    {
        $order->update($data);

        return $order->refresh();
    }
}
