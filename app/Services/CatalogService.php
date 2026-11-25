<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\MenuItem;
use App\Repositories\CatalogRepository;
use Illuminate\Validation\ValidationException;

class CatalogService
{
    public function __construct(
        private CatalogRepository $repository
    ) {}

    public function createBrand(array $data): Brand
    {
        return $this->repository->createBrand($data);
    }

    public function updateBrand(Brand $brand, array $data): Brand
    {
        return $this->repository->updateBrandModel($brand, $data);
    }

    public function deleteBrand(Brand $brand): bool
    {
        if ($brand->menuItems()->exists()) {
            throw ValidationException::withMessages([
                'brand' => 'Cannot delete a brand that still has menu items.',
            ]);
        }

        return (bool) $this->repository->deleteBrand($brand->id);
    }

    public function createMenuItem(array $data): MenuItem
    {
        return $this->repository->createItem($data);
    }

    public function updateMenuItem(MenuItem $menuItem, array $data): MenuItem
    {
        return $this->repository->updateItem($menuItem, $data);
    }

    public function deleteMenuItem(MenuItem $menuItem): bool
    {
        if ($menuItem->orderItems()->exists()) {
            throw ValidationException::withMessages([
                'menu_item' => 'Cannot delete a menu item that appears in an order. Mark it unavailable instead.',
            ]);
        }

        return $this->repository->deleteItem($menuItem);
    }
}
