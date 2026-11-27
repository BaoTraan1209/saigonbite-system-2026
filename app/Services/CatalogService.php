<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\MenuItem;
use App\Repositories\Contracts\CatalogRepositoryInterface;

class CatalogService
{
    public function __construct(
        private CatalogRepositoryInterface $repository
    ) {}

    public function createBrand(array $data): Brand
    {
        return $this->repository->createBrand($data);
    }

    public function updateBrand(Brand $brand, array $data): Brand
    {
        return $this->repository->updateBrandModel($brand, $data);
    }

    public function deactivateBrand(Brand $brand): Brand
    {
        return $this->repository->deactivateBrand($brand);
    }

    public function createMenuItem(array $data): MenuItem
    {
        return $this->repository->createItem($data);
    }

    public function updateMenuItem(MenuItem $menuItem, array $data): MenuItem
    {
        return $this->repository->updateItem($menuItem, $data);
    }

    public function deactivateMenuItem(MenuItem $menuItem): MenuItem
    {
        return $this->repository->deactivateItem($menuItem);
    }
}
