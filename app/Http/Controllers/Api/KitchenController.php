<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KitchenController extends Controller
{
    public function index()
    {
        return response()->json(
            Order::with(['hub', 'items.menuItem'])
                ->whereIn('status', ['submitted', 'accepted', 'preparing', 'ready'])
                ->latest()
                ->get()
        );
    }

    public function updateStatus(Request $request, Order $order)
    {
        $status = $request->validate([
            'status' => 'required|in:accepted,preparing,ready,rejected',
        ])['status'];

        $order = DB::transaction(function () use ($order, $status) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $allowed = [
                'submitted' => ['accepted', 'rejected'],
                'accepted' => ['preparing'],
                'preparing' => ['ready'],
            ];
            if (! in_array($status, $allowed[$order->status] ?? [], true)) {
                throw ValidationException::withMessages([
                    'status' => 'Invalid order status transition.',
                ]);
            }

            $order->update(['status' => $status]);
            if ($status === 'ready') {
                $order->delivery()->firstOrCreate([], [
                    'status' => 'waiting',
                    'cod_amount' => $order->payment_method === 'COD' ? $order->total_amount : 0,
                ]);
            }

            return $order;
        });

        return response()->json($order->refresh()->load('delivery'));
    }
}
