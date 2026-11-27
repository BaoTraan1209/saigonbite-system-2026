<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Hub;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_brand_and_menu_item_crud_endpoints_are_connected_to_the_database(): void
    {
        $this->signInAsAdmin();
        $brandResponse = $this->postJson('/api/brands', [
            'name' => 'UC14 Test Brand',
            'description' => 'Temporary catalog test brand.',
            'status' => 'active',
        ])->assertCreated()->assertJsonPath('name', 'UC14 Test Brand');

        $brandId = $brandResponse->json('id');
        $this->assertDatabaseHas('brands', ['id' => $brandId, 'name' => 'UC14 Test Brand']);
        $this->getJson('/api/brands')->assertOk()->assertJsonFragment(['name' => 'UC14 Test Brand']);
        $this->postJson('/api/brands', ['name' => 'UC14 Test Brand'])->assertUnprocessable()->assertJsonValidationErrors('name');

        $this->putJson("/api/brands/{$brandId}", [
            'name' => 'UC14 Updated Brand',
            'description' => 'Updated description.',
            'status' => 'inactive',
        ])->assertOk()->assertJsonPath('name', 'UC14 Updated Brand')->assertJsonPath('status', 'inactive');

        $itemResponse = $this->postJson('/api/menu-items', [
            'brand_id' => $brandId,
            'name' => 'UC14 Test Item',
            'description' => 'Temporary menu item.',
            'price' => 42000,
            'is_active' => true,
        ])->assertCreated()->assertJsonPath('name', 'UC14 Test Item');

        $itemId = $itemResponse->json('id');
        $this->assertDatabaseHas('menu_items', ['id' => $itemId, 'brand_id' => $brandId]);
        $this->postJson('/api/menu-items', [
            'brand_id' => $brandId,
            'name' => 'UC14 Test Item',
            'price' => 42000,
        ])->assertUnprocessable()->assertJsonValidationErrors('name');
        $this->getJson("/api/menu-items?brand_id={$brandId}")->assertOk()->assertJsonPath('0.id', $itemId);
        $this->putJson("/api/menu-items/{$itemId}", [
            'name' => 'UC14 Updated Item',
            'description' => 'Updated item details.',
            'price' => 45000,
            'is_active' => false,
        ])->assertOk()->assertJsonPath('name', 'UC14 Updated Item')->assertJsonPath('is_active', false);

        $this->deleteJson("/api/menu-items/{$itemId}")->assertOk()->assertJsonPath('is_active', false);
        $this->deleteJson("/api/brands/{$brandId}")->assertOk()->assertJsonPath('status', 'inactive');
        $this->assertDatabaseHas('brands', ['id' => $brandId, 'status' => 'inactive']);
        $this->assertDatabaseHas('menu_items', ['id' => $itemId]);
    }

    public function test_customer_cannot_manage_brands_or_menu_items(): void
    {
        $customer = User::create([
            'name' => 'Catalog Customer',
            'username' => 'catalog_customer',
            'email' => 'catalog-customer@example.test',
            'password' => 'test-password',
            'role' => 'customer',
        ]);
        $this->actingAs($customer);

        $this->postJson('/api/brands', ['name' => 'Forbidden Brand'])->assertForbidden();
        $this->postJson('/api/menu-items', [
            'brand_id' => 1,
            'name' => 'Forbidden Item',
            'price' => 100,
        ])->assertForbidden();
    }

    public function test_admin_controls_item_availability_and_customers_receive_the_selected_status(): void
    {
        $this->signInAsAdmin();
        $brand = Brand::create(['name' => 'New Menu Brand', 'status' => 'active']);
        $hub = Hub::create([
            'name' => 'New Menu Hub',
            'address' => 'District 1',
            'capacity' => 10,
            'status' => 'active',
        ]);
        $hub->brands()->attach($brand);

        $item = $this->postJson('/api/menu-items', [
            'brand_id' => $brand->id,
            'name' => 'New Admin Item',
            'description' => 'Visible while awaiting Hub availability.',
            'price' => 35000,
            'is_active' => true,
            'is_available' => false,
        ])->assertCreated()->json();

        $this->assertDatabaseHas('hub_menu_items', [
            'hub_id' => $hub->id,
            'menu_item_id' => $item['id'],
            'is_available' => false,
        ]);

        $customer = User::create([
            'name' => 'Menu Customer',
            'username' => 'menu_customer',
            'email' => 'menu-customer@example.test',
            'password' => 'test-password',
            'role' => 'customer',
        ]);
        $this->actingAs($customer);

        $this->getJson("/api/brands?hub_id={$hub->id}")
            ->assertOk()
            ->assertJsonFragment(['id' => $brand->id]);
        $this->getJson("/api/menu-items?hub_id={$hub->id}&brand_id={$brand->id}")
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $item['id'])
            ->assertJsonPath('0.is_available', false);

        $this->actingAs(User::where('role', 'admin')->firstOrFail());
        $this->putJson("/api/menu-items/{$item['id']}", ['is_available' => true])
            ->assertOk()
            ->assertJsonPath('is_available', true);
        $this->assertDatabaseHas('hub_menu_items', [
            'hub_id' => $hub->id,
            'menu_item_id' => $item['id'],
            'is_available' => true,
        ]);

        $this->actingAs($customer);
        $this->getJson("/api/menu-items?hub_id={$hub->id}&brand_id={$brand->id}")
            ->assertOk()
            ->assertJsonPath('0.is_available', true);
    }

    public function test_catalog_endpoints_return_real_database_relations(): void
    {
        $this->signInAsAdmin();
        $brand = Brand::create(['name' => 'UC14 Relation Brand', 'status' => 'active']);
        $item = MenuItem::create([
            'brand_id' => $brand->id,
            'name' => 'UC14 Relation Item',
            'description' => 'Related through its brand.',
            'price' => 29000,
            'is_available' => true,
        ]);

        $this->getJson('/api/brands/'.$brand->id)
            ->assertOk()
            ->assertJsonPath('menu_items.0.id', $item->id);
        $this->getJson('/api/menu-items?brand_id='.$brand->id)
            ->assertOk()
            ->assertJsonPath('0.brand.id', $brand->id);
    }

    private function signInAsAdmin(): void
    {
        $this->actingAs(User::create([
            'name' => 'Catalog Admin',
            'username' => 'catalog_admin',
            'email' => 'catalog-admin@example.test',
            'password' => 'test-password',
            'role' => 'admin',
        ]));
    }
}
