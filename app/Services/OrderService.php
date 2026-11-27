<?php

namespace App\Services;

use App\Models\Hub;
use App\Models\Order;
use App\Repositories\Contracts\CatalogRepositoryInterface;
use App\Repositories\Contracts\CustomerAddressRepositoryInterface;
use App\Repositories\Contracts\HubRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        private OrderRepositoryInterface $orders,
        private HubRepositoryInterface $hubs,
        private CatalogRepositoryInterface $catalog,
        private CustomerAddressRepositoryInterface $addresses,
    ) {}

    public function previewOrder(array $data, int $customerId): array
    {
        return DB::transaction(function () use ($data, $customerId): array {
            $this->orders->lockCustomer($customerId);
            $hub = $this->hubs->lockForOrder((int) $data['hub_id']);
            return $this->calculate($data, $customerId, $hub);
        });
    }

    public function createOrder(array $data, int $customerId): Order
    {
        return DB::transaction(function () use ($data, $customerId): Order {
            // Serializes repeat submissions for this customer; the unique DB key is the final guard.
            $this->orders->lockCustomer($customerId);
            if ($existing = $this->orders->findByIdempotencyKey($customerId, $data['idempotency_key'])) {
                return $existing;
            }

            $hub = $this->hubs->lockForOrder((int) $data['hub_id']);
            $summary = $this->calculate($data, $customerId, $hub);

            $order = $this->orders->create([
                'hub_id' => $hub->id,
                'brand_id' => $data['brand_id'],
                'brand_name' => $hub->brands()->whereKey($data['brand_id'])->value('brands.name'),
                'customer_id' => $customerId,
                'customer_address_id' => $summary['customer_address_id'],
                'voucher_code' => $summary['voucher_code'],
                'idempotency_key' => $data['idempotency_key'],
                'customer_name' => $summary['customer_name'],
                'customer_phone' => $summary['customer_phone'],
                'delivery_address' => $summary['delivery_address'],
                'payment_method' => 'COD',
                'payment_status' => 'unpaid',
                'subtotal' => $summary['subtotal'],
                'delivery_fee' => $summary['delivery_fee'],
                'surcharge' => $summary['surcharge'],
                'discount_amount' => $summary['discount_amount'],
                'total_amount' => $summary['total_amount'],
                'status' => 'submitted',
                'notes' => $data['notes'] ?? null,
            ]);

            $order->items()->createMany($summary['items']);

            return $order->load(['hub', 'customerAddress', 'items.menuItem', 'delivery']);
        });
    }

    private function calculate(array $data, int $customerId, Hub $hub): array
    {
        if ($hub->status !== 'active') {
            throw ValidationException::withMessages(['hub_id' => 'The selected Hub is not active.']);
        }
        if ($this->hubs->currentLoadForOrder($hub->id) >= $hub->capacity) {
            throw ValidationException::withMessages(['hub_id' => 'The selected Hub cannot accept more orders.']);
        }
        if (! $hub->brands()->whereKey($data['brand_id'])->where('brands.status', 'active')->exists()) {
            throw ValidationException::withMessages(['brand_id' => 'This Brand is not active at the selected Hub.']);
        }
        if (! empty($data['voucher_code'])) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Voucher verification is managed by the external voucher service, which is not configured.',
            ]);
        }

        $address = null;
        if (! empty($data['address_id'])) {
            $address = $this->addresses->findForCustomer($customerId, (int) $data['address_id']);
            $customerName = $address->recipient_name;
            $customerPhone = $address->phone;
            $deliveryAddress = $address->formattedAddress();
        } else {
            $customerName = $data['customer_name'];
            $customerPhone = $data['customer_phone'];
            $deliveryAddress = trim($data['delivery_address']);
        }

        // A-02 does not define fee/distance rules; keep the current fee configurable pending approval.
        $deliveryFeeCents = (int) round((float) config('saigonbite.delivery_fee', 15000) * 100);
        $surchargeCents = (int) round((float) config('saigonbite.surcharge', 0) * 100);
        $subtotalCents = 0;
        $orderItems = [];
        foreach ($data['items'] as $line) {
            $menuItem = $this->catalog->lockAvailableItem(
                (int) $line['menu_item_id'],
                (int) $data['brand_id'],
                (int) $hub->id
            );
            if (! $menuItem) {
                throw ValidationException::withMessages([
                    'items' => 'A selected menu item is inactive, unavailable at this Hub, or belongs to another Brand.',
                ]);
            }
            $unitPriceCents = (int) round((float) $menuItem->price * 100);
            $lineTotalCents = $unitPriceCents * (int) $line['quantity'];
            $subtotalCents += $lineTotalCents;
            $orderItems[] = [
                'menu_item_id' => $menuItem->id,
                'item_name' => $menuItem->name,
                'quantity' => (int) $line['quantity'],
                'unit_price' => number_format($unitPriceCents / 100, 2, '.', ''),
                'line_total' => number_format($lineTotalCents / 100, 2, '.', ''),
            ];
        }

        $totalCents = $subtotalCents + $deliveryFeeCents + $surchargeCents;
        return [
            'customer_address_id' => $address?->id,
            'customer_name' => $customerName,
            'customer_phone' => $customerPhone,
            'delivery_address' => $deliveryAddress,
            'voucher_code' => null,
            'subtotal' => number_format($subtotalCents / 100, 2, '.', ''),
            'delivery_fee' => number_format($deliveryFeeCents / 100, 2, '.', ''),
            'surcharge' => number_format($surchargeCents / 100, 2, '.', ''),
            'discount_amount' => '0.00',
            'total_amount' => number_format($totalCents / 100, 2, '.', ''),
            'items' => $orderItems,
        ];
    }
}
