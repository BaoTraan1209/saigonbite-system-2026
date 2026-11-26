<?php

namespace App\Services;

use App\Models\Hub;
use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {

            $hub = Hub::whereKey($data['hub_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($hub->status !== 'active') {
                throw ValidationException::withMessages([
                    'hub_id' => 'The selected Hub is not active.',
                ]);
            }

            if ($hub->current_load >= $hub->capacity) {
                throw ValidationException::withMessages([
                    'hub_id' => 'The selected Hub cannot accept more orders.',
                ]);
            }

            $brandAvailable = $hub->brands()
                ->where('brands.id', $data['brand_id'])
                ->where('brands.status', 'active')
                ->exists();

            if (! $brandAvailable) {
                throw ValidationException::withMessages([
                    'brand_id' => 'This Brand is not available at the selected Hub.',
                ]);
            }

            $subtotalCents = 0;
            $orderItems = [];

            foreach ($data['items'] as $item) {

                $menuItem = MenuItem::whereKey($item['menu_item_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ((int) $menuItem->brand_id !== (int) $data['brand_id']) {
                    throw ValidationException::withMessages([
                        'items' => 'A selected menu item does not belong to the selected Brand.',
                    ]);
                }

                if (! $menuItem->is_available) {
                    throw ValidationException::withMessages([
                        'items' => $menuItem->name.' is currently unavailable.',
                    ]);
                }

                $quantity = (int) $item['quantity'];

                $unitPriceCents = (int) round((float) $menuItem->price * 100);
                $lineTotalCents = $unitPriceCents * $quantity;
                $subtotalCents += $lineTotalCents;

                $orderItems[] = [
                    'menu_item_id' => $menuItem->id,
                    'item_name' => $menuItem->name,
                    'quantity' => $quantity,
                    'unit_price' => number_format($unitPriceCents / 100, 2, '.', ''),
                    'line_total' => number_format($lineTotalCents / 100, 2, '.', ''),
                ];
            }

            $subtotal = number_format($subtotalCents / 100, 2, '.', '');
            $deliveryFee = '15000.00';
            $discountAmount = '0.00';
            $totalAmount = number_format(($subtotalCents + 15000 * 100) / 100, 2, '.', '');

            $order = Order::create([
                'hub_id' => $hub->id,
                'customer_id' => $data['customer_id'] ?? null,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'delivery_address' => $data['delivery_address'],
                'payment_method' => $data['payment_method'],
                'payment_status' => 'unpaid',
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'status' => 'submitted',
                'notes' => $data['notes'] ?? null,
            ]);

            $order->items()->createMany($orderItems);

            return $order->load([
                'hub',
                'items.menuItem',
                'delivery',
            ]);
        });
    }
}
