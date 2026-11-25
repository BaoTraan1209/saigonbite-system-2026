<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Delivery;
use App\Models\Hub;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_is_priced_and_completed_through_kitchen_and_delivery(): void
    {
        [$hub, $brand, $item] = $this->createCatalog();

        $response = $this->postJson('/api/orders', [
            'hub_id' => $hub->id,
            'brand_id' => $brand->id,
            'customer_name' => 'Nguyen Van A',
            'customer_phone' => '0900000000',
            'delivery_address' => 'District 1',
            'payment_method' => 'CASH_ON_DELIVERY',
            'items' => [
                ['menu_item_id' => $item->id, 'quantity' => 2],
            ],
        ])->assertCreated()
            ->assertJsonPath('status', 'submitted')
            ->assertJsonPath('payment_method', 'COD')
            ->assertJsonPath('subtotal', '130000.00')
            ->assertJsonPath('items.0.line_total', '130000.00');

        $orderId = $response->json('id');
        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'payment_status' => 'unpaid',
            'total_amount' => 130000,
        ]);
        $this->assertDatabaseMissing('deliveries', ['order_id' => $orderId]);

        foreach (['accepted', 'preparing', 'ready'] as $status) {
            $this->patchJson("/api/kitchen/orders/{$orderId}/status", [
                'status' => $status,
            ])->assertOk()->assertJsonPath('status', $status);
        }

        $deliveryId = Delivery::where('order_id', $orderId)->value('id');
        $this->assertNotNull($deliveryId);
        $this->patchJson("/api/deliveries/{$deliveryId}/status", [
            'status' => 'assigned',
            'shipper_name' => 'Tran Van B',
            'shipper_phone' => '0911111111',
        ])->assertOk()->assertJsonPath('status', 'assigned');
        $this->patchJson("/api/deliveries/{$deliveryId}/status", [
            'status' => 'picked_up',
        ])->assertOk()->assertJsonPath('order.status', 'delivering');
        $this->patchJson("/api/deliveries/{$deliveryId}/status", [
            'status' => 'delivered',
        ])->assertOk()->assertJsonPath('order.status', 'completed');
    }

    public function test_order_rejects_inactive_hubs_and_unavailable_items(): void
    {
        [$hub, $brand, $item] = $this->createCatalog();
        $hub->update(['status' => 'suspended']);

        $payload = [
            'hub_id' => $hub->id,
            'brand_id' => $brand->id,
            'customer_name' => 'Nguyen Van A',
            'customer_phone' => '0900000000',
            'delivery_address' => 'District 1',
            'payment_method' => 'CARD',
            'items' => [
                ['menu_item_id' => $item->id, 'quantity' => 1],
            ],
        ];
        $this->postJson('/api/orders', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('hub_id');
        $this->assertDatabaseCount('orders', 0);

        $hub->update(['status' => 'active']);
        $item->update(['is_available' => false]);
        $this->postJson('/api/orders', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('items');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_invalid_workflow_transitions_are_rejected(): void
    {
        [$hub, $brand, $item] = $this->createCatalog();
        $order = Order::create([
            'hub_id' => $hub->id,
            'customer_name' => 'Nguyen Van A',
            'customer_phone' => '0900000000',
            'delivery_address' => 'District 1',
            'payment_method' => 'CARD',
            'payment_status' => 'unpaid',
            'subtotal' => 100,
            'delivery_fee' => 0,
            'discount_amount' => 0,
            'total_amount' => 100,
            'status' => 'submitted',
        ]);

        $this->patchJson("/api/kitchen/orders/{$order->id}/status", [
            'status' => 'ready',
        ])->assertUnprocessable()->assertJsonValidationErrors('status');
        $this->assertDatabaseMissing('deliveries', ['order_id' => $order->id]);
    }

    public function test_failed_delivery_updates_order_and_prevents_deleting_ordered_items(): void
    {
        [$hub, $brand, $item] = $this->createCatalog();
        $order = Order::create([
            'hub_id' => $hub->id,
            'customer_name' => 'Nguyen Van A',
            'customer_phone' => '0900000000',
            'delivery_address' => 'District 1',
            'payment_method' => 'COD',
            'payment_status' => 'unpaid',
            'subtotal' => 65000,
            'delivery_fee' => 0,
            'discount_amount' => 0,
            'total_amount' => 65000,
            'status' => 'ready',
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'menu_item_id' => $item->id,
            'item_name' => $item->name,
            'quantity' => 1,
            'unit_price' => 65000,
            'line_total' => 65000,
        ]);
        $this->deleteJson("/api/menu-items/{$item->id}")
            ->assertUnprocessable()
            ->assertJsonValidationErrors('menu_item');
        $this->assertDatabaseHas('menu_items', ['id' => $item->id]);

        $delivery = Delivery::create([
            'order_id' => $order->id,
            'status' => 'waiting',
            'cod_amount' => 65000,
        ]);
        $this->patchJson("/api/deliveries/{$delivery->id}/status", [
            'status' => 'assigned',
        ])->assertOk();
        $this->patchJson("/api/deliveries/{$delivery->id}/status", [
            'status' => 'picked_up',
        ])->assertOk();
        $this->patchJson("/api/deliveries/{$delivery->id}/status", [
            'status' => 'failed',
        ])->assertUnprocessable()->assertJsonValidationErrors('failure_reason');
        $this->patchJson("/api/deliveries/{$delivery->id}/status", [
            'status' => 'failed',
            'failure_reason' => 'Customer unavailable',
        ])->assertOk()->assertJsonPath('order.status', 'delivery_failed');
    }

    public function test_hubs_catalog_and_date_filtered_reports_are_available(): void
    {
        $brand = Brand::create(['name' => 'Saigon Bowl', 'status' => 'active']);
        $this->postJson('/api/hubs', [
            'name' => 'District 1 Hub',
            'address' => 'District 1',
            'capacity' => 5,
            'brand_ids' => [$brand->id],
        ])->assertCreated()
            ->assertJsonPath('status', 'draft')
            ->assertJsonPath('brands.0.id', $brand->id);

        $hub = Hub::firstOrFail();
        $this->patchJson("/api/hubs/{$hub->id}/status", [
            'status' => 'active',
        ])->assertOk()->assertJsonPath('status', 'active');
        $this->getJson('/api/hubs?available=1')->assertOk()->assertJsonPath('0.current_load', 0);

        $item = MenuItem::create([
            'brand_id' => $brand->id,
            'name' => 'Chicken Rice Bowl',
            'price' => 65000,
            'is_available' => true,
        ]);
        $this->getJson("/api/brands?hub_id={$hub->id}")
            ->assertOk()->assertJsonPath('0.id', $brand->id);
        $this->getJson("/api/menu-items?hub_id={$hub->id}&brand_id={$brand->id}")
            ->assertOk()->assertJsonPath('0.id', $item->id);

        $order = Order::create([
            'hub_id' => $hub->id,
            'customer_name' => 'Nguyen Van A',
            'customer_phone' => '0900000000',
            'delivery_address' => 'District 1',
            'payment_method' => 'CARD',
            'payment_status' => 'unpaid',
            'subtotal' => 65000,
            'delivery_fee' => 0,
            'discount_amount' => 0,
            'total_amount' => 65000,
            'status' => 'completed',
        ]);
        Delivery::create([
            'order_id' => $order->id,
            'status' => 'delivered',
            'cod_amount' => 0,
        ]);
        $this->getJson('/api/reports/summary?from=2000-01-01&to=2999-12-31')
            ->assertOk()
            ->assertJsonPath('sales', 65000)
            ->assertJsonPath('delivery_performance.0.status', 'delivered');
        $this->getJson('/api/reports/summary?from=2026-01-02&to=2026-01-01')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('to');
    }

    private function createCatalog(): array
    {
        $brand = Brand::create([
            'name' => 'Saigon Bowl',
            'status' => 'active',
        ]);
        $hub = Hub::create([
            'name' => 'District 1 Hub',
            'address' => 'District 1',
            'capacity' => 5,
            'status' => 'active',
        ]);
        $hub->brands()->attach($brand);
        $item = MenuItem::create([
            'brand_id' => $brand->id,
            'name' => 'Chicken Rice Bowl',
            'price' => 65000,
            'is_available' => true,
        ]);

        return [$hub, $brand, $item];
    }
}
