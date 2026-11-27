<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_and_customer_sign_in_to_their_own_use_case(): void
    {
        $admin = $this->makeUser('admin');
        $this->get('/login')->assertOk();
        $this->post('/login', ['login' => 'admin', 'password' => '123456'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
        $this->get('/admin/catalog')->assertOk()->assertSee('Log out')->assertSee('class="admin-profile account-corner"', false);
        $this->get('/order')->assertForbidden();

        $this->post('/logout')->assertRedirect(route('login'));
        $customer = $this->makeUser('customer');
        $this->post('/login', ['login' => 'customer@saigonbite.local', 'password' => '123456'])
            ->assertRedirect(route('order.start'));
        $this->assertAuthenticatedAs($customer);
        $this->get('/order')->assertOk()->assertSee('Log out')->assertSee('class="admin-profile account-corner"', false)->assertSee('>C</span>', false);
        $this->get('/admin/catalog')->assertForbidden();
    }

    public function test_api_requires_a_role_and_limits_catalog_changes_to_admin(): void
    {
        $this->getJson('/api/brands')->assertUnauthorized();

        $customer = $this->makeUser('customer');
        $this->actingAs($customer);
        $this->getJson('/api/brands')->assertUnprocessable()->assertJsonValidationErrors('hub_id');
        $this->postJson('/api/brands', ['name' => 'Customer should not create this'])
            ->assertForbidden();
    }

    private function makeUser(string $role): User
    {
        return User::create([
            'name' => ucfirst($role),
            'username' => $role,
            'email' => $role.'@saigonbite.local',
            'password' => '123456',
            'role' => $role,
        ]);
    }
}
