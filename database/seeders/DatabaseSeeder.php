<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Hub;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $restaurants = [
            [
                'brand' => ['name' => 'KFC', 'description' => 'KFC Việt Nam', 'status' => 'active'],
                'hub' => ['name' => 'KFC Lê Lai', 'address' => '78 Lê Lai, Phường Bến Thành, Quận 1, Thành phố Hồ Chí Minh', 'phone' => null, 'capacity' => 20, 'status' => 'active'],
                'items' => [
                    ['name' => '1 Miếng Gà Rán', 'description' => '1 miếng gà rán kèm tương.', 'price' => 37000],
                    ['name' => 'Burger Gà Yo', 'description' => 'Burger Gà Yo.', 'price' => 30000],
                    ['name' => 'Mì Ý Xúc Xích Gà', 'description' => 'Mì Ý xúc xích gà.', 'price' => 38000],
                    ['name' => '1 Cơm Gà Rán', 'description' => 'Cơm gà rán kèm tương.', 'price' => 49000],
                ],
            ],
            [
                'brand' => ['name' => 'Jollibee', 'description' => 'Jollibee Việt Nam', 'status' => 'active'],
                'hub' => ['name' => 'Jollibee Pasteur', 'address' => '194D Pasteur, Phường Xuân Hòa, Thành phố Hồ Chí Minh', 'phone' => '02838237000', 'capacity' => 20, 'status' => 'active'],
                'items' => [
                    ['name' => '1 Miếng Gà Giòn Vui Vẻ', 'description' => '1 miếng gà giòn vui vẻ kèm tương chua ngọt.', 'price' => 33000],
                    ['name' => '1 Cơm Gà Vui Vẻ', 'description' => 'Cơm gà vui vẻ kèm tương chua ngọt.', 'price' => 48000],
                    ['name' => '1 Mỳ Ý Jolly Vừa', 'description' => 'Mỳ Ý Jolly cỡ vừa.', 'price' => 35000],
                    ['name' => 'Combo 1 Gà + Khoai + Nước', 'description' => '1 miếng gà, khoai tây chiên vừa, nước ngọt vừa và sốt.', 'price' => 53000],
                ],
            ],
            [
                'brand' => ['name' => 'Lotteria', 'description' => 'Lotteria Việt Nam', 'status' => 'active'],
                'hub' => ['name' => 'Lotteria HTV', 'address' => '11-11Bis Nguyễn Thị Minh Khai, Phường Bến Nghé, Quận 1, Thành phố Hồ Chí Minh', 'phone' => '02839101633', 'capacity' => 20, 'status' => 'active'],
                'items' => [
                    ['name' => 'Gà Rán 1 Miếng', 'description' => '1 miếng gà rán.', 'price' => 37000],
                    ['name' => 'Burger Bulgogi', 'description' => 'Burger bò Bulgogi.', 'price' => 50000],
                    ['name' => 'Burger Tôm', 'description' => 'Burger tôm.', 'price' => 50000],
                    ['name' => 'Cơm Gà Viên', 'description' => 'Cơm gà viên.', 'price' => 48000],
                ],
            ],
        ];

        foreach ($restaurants as $restaurant) {
            $brand = Brand::updateOrCreate(
                ['name' => $restaurant['brand']['name']],
                $restaurant['brand']
            );
            $hub = Hub::updateOrCreate(
                ['name' => $restaurant['hub']['name']],
                $restaurant['hub']
            );
            $hub->brands()->sync([$brand->id]);

            foreach ($restaurant['items'] as $item) {
                MenuItem::updateOrCreate(
                    ['brand_id' => $brand->id, 'name' => $item['name']],
                    [...$item, 'is_available' => true]
                );
            }
        }

        $this->call(DefaultAccountsSeeder::class);
    }
}
