<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderRepository $repo, private OrderService $service) {}

    public function index()
    {
        return response()->json($this->repo->list());
    }

    public function show(Order $order)
    {
        if (auth()->user()?->role === 'customer' && (int) $order->customer_id !== (int) auth()->id()) {
            abort(404);
        }

        return response()->json($order->load(['hub', 'items.menuItem', 'delivery']));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'hub_id' => 'required|exists:hubs,id', 'brand_id' => 'required|exists:brands,id',
            'customer_name' => 'required|string|max:150',
            'customer_phone' => 'required|string|max:30', 'delivery_address' => 'required|string',
            'payment_method' => 'required|in:cash,card,online,COD,CASH_ON_DELIVERY,CARD,ONLINE', 'notes' => 'nullable|string',
            'items' => 'required|array|min:1', 'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1|max:50',
        ]);
        $data['payment_method'] = match (strtoupper($data['payment_method'])) {
            'CASH', 'COD', 'CASH_ON_DELIVERY' => 'COD',
            'CARD' => 'CARD',
            'ONLINE' => 'ONLINE',
        };
        $data['customer_id'] = $request->user()->id;

        return response()->json($this->service->createOrder($data), 201);
    }
}
