<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeliveryController extends Controller
{
    public function index()
    {
        return response()->json(
            Delivery::with('order.hub')
                ->whereIn('status', ['waiting', 'assigned', 'picked_up'])
                ->latest()
                ->get()
        );
    }

    public function updateStatus(Request $request, Delivery $delivery)
    {
        $data = $request->validate([
            'status' => 'required|in:assigned,picked_up,delivered,failed',
            'shipper_name' => 'sometimes|nullable|string|max:150',
            'shipper_phone' => 'sometimes|nullable|string|max:30',
            'cod_amount' => 'sometimes|nullable|numeric|min:0',
            'cash_collected' => 'required_if:status,delivered|nullable|numeric|min:0',
            'failure_reason' => 'required_if:status,failed|nullable|string',
        ]);

        $delivery = DB::transaction(function () use ($delivery, $data) {
            $delivery = Delivery::whereKey($delivery->id)->lockForUpdate()->firstOrFail();
            $order = $delivery->order()->lockForUpdate()->firstOrFail();
            $allowed = [
                'waiting' => ['assigned', 'picked_up'],
                'assigned' => ['picked_up'],
                'picked_up' => ['delivered', 'failed'],
            ];
            if (! in_array($data['status'], $allowed[$delivery->status] ?? [], true)) {
                throw ValidationException::withMessages([
                    'status' => 'Invalid delivery status transition.',
                ]);
            }

            if ($data['status'] === 'delivered' && $order->payment_method === 'COD'
                && (float) $data['cash_collected'] !== (float) $order->total_amount) {
                throw ValidationException::withMessages([
                    'cash_collected' => 'The collected COD amount must match the order total.',
                ]);
            }

            $updates = array_intersect_key($data, array_flip([
                'status', 'shipper_name', 'shipper_phone', 'cod_amount', 'cash_collected', 'failure_reason',
            ]));
            if ($data['status'] === 'picked_up') {
                $updates['picked_up_at'] = now();
            } elseif ($data['status'] === 'delivered') {
                $updates['delivered_at'] = now();
            }
            $delivery->update($updates);

            $orderStatus = match ($data['status']) {
                'picked_up' => 'delivering',
                'delivered' => 'completed',
                'failed' => 'delivery_failed',
                default => null,
            };
            if ($orderStatus !== null) {
                $orderUpdates = ['status' => $orderStatus];
                if ($data['status'] === 'delivered' && $order->payment_method === 'COD') {
                    $orderUpdates['payment_status'] = 'paid';
                }
                $order->update($orderUpdates);
            }

            return $delivery;
        });

        return response()->json($delivery->refresh()->load('order'));
    }
}
