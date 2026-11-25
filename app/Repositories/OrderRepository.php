<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderRepository
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
