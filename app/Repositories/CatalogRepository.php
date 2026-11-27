<?php

namespace App\Repositories;

use App\Models\Brand;
use App\Models\MenuItem;
use App\Repositories\Contracts\CatalogRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CatalogRepository implements CatalogRepositoryInterface
{
    public function brands(): Collection
    {
        return Brand::with('menuItems')->latest()->get();
    }

    public function brandsForHub(int $hubId): Collection
    {
        return Brand::with(['menuItems' => fn ($items) => $items
            ->where('menu_items.is_active', true)
            ->whereHas('hubs', fn ($hubs) => $hubs->where('hubs.id', $hubId))])
            ->where('status', 'active')
            ->whereHas('hubs', fn ($query) => $query->where('hubs.id', $hubId))
            ->whereHas('menuItems', fn ($items) => $items
                ->where('menu_items.is_active', true)
                ->whereHas('hubs', fn ($hubs) => $hubs->where('hubs.id', $hubId)))
            ->latest()
            ->get();
    }

    public function items(?int $brandId = null, ?int $hubId = null, bool $activeOnly = false, bool $availableAtHubOnly = false): Collection
    {
        $query = MenuItem::with('brand')
            ->when($brandId, fn ($items) => $items->where('brand_id', $brandId))
            ->when($activeOnly, fn ($items) => $items->where('is_active', true))
            ->when($hubId, fn ($items) => $items->whereHas('brand.hubs', fn ($hubs) => $hubs->where('hubs.id', $hubId)))
            ->when($hubId && $activeOnly, fn ($items) => $items->whereHas('hubs', fn ($hubs) => $hubs->where('hubs.id', $hubId)))
            ->when($hubId && $availableAtHubOnly, fn ($items) => $items->whereHas('hubs', fn ($hubs) => $hubs
                ->where('hubs.id', $hubId)->where('hub_menu_items.is_available', true)))
            ->when($hubId, fn ($items) => $items->with(['hubs' => fn ($hubs) => $hubs->where('hubs.id', $hubId)]))
            ->latest();

        $items = $query->get();
        if ($hubId) {
            $items->each(function (MenuItem $item) use ($hubId): void {
                $hub = $item->hubs->first();
                $item->setAttribute('is_available', (bool) ($hub?->pivot?->is_available ?? false));
                $item->setAttribute('availability_hub_id', $hubId);
            });
        }

        return $items;
    }

    public function getBrandById(int $id): Brand
    {
        return Brand::with('menuItems')->findOrFail($id);
    }

    public function createBrand(array $data): Brand
    {
        return Brand::create($data);
    }

    public function updateBrandModel(Brand $brand, array $data): Brand
    {
        $brand->update($data);
        return $brand->refresh();
    }

    public function deactivateBrand(Brand $brand): Brand
    {
        $brand->update(['status' => 'inactive']);
        return $brand->refresh();
    }

    public function createItem(array $data): MenuItem
    {
        return DB::transaction(function () use ($data): MenuItem {
            $item = MenuItem::create($data);
            $hubIds = DB::table('hub_brands')->where('brand_id', $item->brand_id)->pluck('hub_id');
            foreach ($hubIds as $hubId) {
                $item->hubs()->syncWithoutDetaching([$hubId => ['is_available' => (bool) $item->is_available]]);
            }

            return $item->load('brand');
        });
    }

    public function updateItem(MenuItem $item, array $data): MenuItem
    {
        return DB::transaction(function () use ($item, $data): MenuItem {
            $item->update($data);
            if (array_key_exists('is_available', $data)) {
                DB::table('hub_menu_items')
                    ->where('menu_item_id', $item->id)
                    ->update(['is_available' => (bool) $data['is_available']]);
            }

            return $item->refresh()->load('brand');
        });
    }

    public function deactivateItem(MenuItem $item): MenuItem
    {
        $item->update(['is_active' => false]);
        return $item->refresh()->load('brand');
    }

    public function lockAvailableItem(int $itemId, int $brandId, int $hubId): ?MenuItem
    {
        return MenuItem::query()
            ->whereKey($itemId)
            ->where('brand_id', $brandId)
            ->where('is_active', true)
            ->whereHas('hubs', fn ($query) => $query->where('hubs.id', $hubId)->where('hub_menu_items.is_available', true))
            ->lockForUpdate()
            ->first();
    }
}
