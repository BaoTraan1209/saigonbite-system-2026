<?php
namespace App\Repositories;
use App\Models\Order;
class OrderRepository {
    public function create(array $data): Order { return Order::create($data); }
    public function list() { return Order::with(['hub','items','delivery'])->latest()->paginate(15); }
}
