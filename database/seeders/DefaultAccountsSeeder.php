<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DefaultAccountsSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'SaigonBite Admin', 'username' => 'admin', 'email' => 'admin@saigonbite.local', 'role' => 'admin'],
            ['name' => 'SaigonBite Customer', 'username' => 'customer', 'email' => 'customer@saigonbite.local', 'role' => 'customer'],
        ] as $account) {
            User::updateOrCreate(
                ['username' => $account['username']],
                [...$account, 'password' => '123456']
            );
        }
    }
}
