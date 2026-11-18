<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{Hub,Brand,MenuItem};
class DatabaseSeeder extends Seeder {
 public function run(): void {
  $brand=Brand::create(['name'=>'Saigon Bowl','description'=>'Vietnamese rice and noodle bowls','status'=>'active']);
  $hub=Hub::create(['name'=>'District 1 Hub','address'=>'District 1, Ho Chi Minh City','phone'=>'0900000000','capacity'=>20,'status'=>'active']);
  $hub->brands()->attach($brand);
  MenuItem::create(['brand_id'=>$brand->id,'name'=>'Chicken Rice Bowl','description'=>'Grilled chicken with rice','price'=>65000,'is_available'=>true]);
  MenuItem::create(['brand_id'=>$brand->id,'name'=>'Beef Noodle Bowl','description'=>'Beef noodle bowl','price'=>75000,'is_available'=>true]);
 }
}
