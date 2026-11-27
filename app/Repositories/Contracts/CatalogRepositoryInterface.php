<?php

namespace App\Repositories\Contracts;

use App\Models\Brand;
use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Collection;

interface CatalogRepositoryInterface
{
    public function brands(): Collection;
    public function brandsForHub(int $hubId): Collection;
    public function items(?int $brandId = null, ?int $hubId = null, bool $activeOnly = false, bool $availableAtHubOnly = false): Collection;
    public function getBrandById(int $id): Brand;
    public function createBrand(array $data): Brand;
    public function updateBrandModel(Brand $brand, array $data): Brand;
    public function deactivateBrand(Brand $brand): Brand;
    public function createItem(array $data): MenuItem;
    public function updateItem(MenuItem $item, array $data): MenuItem;
    public function deactivateItem(MenuItem $item): MenuItem;
    public function lockAvailableItem(int $itemId, int $brandId, int $hubId): ?MenuItem;
}
