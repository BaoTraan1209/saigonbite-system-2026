<?php
namespace App\Repositories;
use App\Models\Brand;
use App\Models\MenuItem;
class CatalogRepository {
    public function brands() { return Brand::with('menuItems')->latest()->get(); }
    public function createBrand(array $data): Brand { return Brand::create($data); }
    public function createItem(array $data): MenuItem { return MenuItem::create($data); }
}
