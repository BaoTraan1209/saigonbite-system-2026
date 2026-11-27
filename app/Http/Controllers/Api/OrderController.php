<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PreviewOrderRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(private OrderRepositoryInterface $repo, private OrderService $service) {}

    public function index()
    {
        return response()->json($this->repo->list());
    }

    public function show(Order $order)
    {
        if (auth()->user()?->role === 'customer' && (int) $order->customer_id !== (int) auth()->id()) {
            abort(404);
        }

        return response()->json($order->load(['hub', 'brand', 'customerAddress', 'items.menuItem', 'delivery']));
    }

    public function preview(PreviewOrderRequest $request)
    {
        $data = $request->validated();
        $data['payment_method'] = 'COD';
        return response()->json($this->service->previewOrder($data, (int) $request->user()->id));
    }

    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();
        $data['payment_method'] = 'COD';
        $order = $this->service->createOrder($data, (int) $request->user()->id);

        return response()->json($order, 201);
    }
}
