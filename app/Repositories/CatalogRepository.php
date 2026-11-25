<?php

namespace App\Repositories;

use App\Models\Brand;
use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Collection;

class CatalogRepository
{
    public function brands()
    {
        return Brand::with('menuItems')->latest()->get();
    }

    public function brandsForHub(int $hubId): Collection
    {
        return Brand::with('menuItems')
            ->where('status', 'active')
            ->whereHas('hubs', fn ($query) => $query->where('hubs.id', $hubId))
            ->latest()
            ->get();
    }

    public function items(?int $brandId = null, ?int $hubId = null): Collection
    {
        return MenuItem::with('brand')
            ->when($brandId, fn ($query) => $query->where('brand_id', $brandId))
            ->when($hubId, fn ($query) => $query->whereHas('brand.hubs', function ($hubs) use ($hubId) {
                $hubs->where('hubs.id', $hubId);
            }))
            ->latest()
            ->get();
    }

    public function getBrandById($id)
    {
        return Brand::with('menuItems')->findOrFail($id);
    }

    public function createBrand(array $data)
    {
        return Brand::create($data);
    }

    public function updateBrand($id, array $data)
    {
        $brand = Brand::findOrFail($id);
        $brand->update($data);

        return $brand->refresh();
    }

    public function deleteBrand($id)
    {
        $brand = Brand::findOrFail($id);

        return $brand->delete();
    }

    public function createItem(array $data)
    {
        return MenuItem::create($data);
    }

    public function updateBrandModel(Brand $brand, array $data): Brand
    {
        $brand->update($data);

        return $brand->refresh();
    }

    public function updateItem(MenuItem $item, array $data): MenuItem
    {
        $item->update($data);

        return $item->refresh();
    }

    public function deleteItem(MenuItem $item): bool
    {
        return (bool) $item->delete();
    }
}
