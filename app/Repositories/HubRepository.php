<?php

namespace App\Repositories;

use App\Models\Hub;
use App\Models\MenuItem;
use App\Repositories\Contracts\HubRepositoryInterface;
use Illuminate\Support\Facades\DB;

class HubRepository implements HubRepositoryInterface
{
    public function all(bool $availableOnly = false)
    {
        return Hub::with('brands')->withCount([
            'orders as current_load' => fn ($query) => $query->whereIn(
                'status',
                ['submitted', 'accepted', 'preparing', 'ready', 'delivering']
            ),
        ])
            ->when($availableOnly, fn ($query) => $query
                ->where('status', 'active')
                ->whereRaw('(SELECT COUNT(*) FROM orders WHERE orders.hub_id = hubs.id AND orders.status IN (?, ?, ?, ?, ?)) < hubs.capacity', [
                    'submitted', 'accepted', 'preparing', 'ready', 'delivering',
                ]))
            ->latest()
            ->get()
            ->each(function (Hub $hub) {
                $hub->setAttribute(
                    'load_percent',
                    $hub->capacity > 0 ? (int) floor($hub->current_load * 100 / $hub->capacity) : 0
                );
            });
    }

    public function create(array $data): Hub
    {
        return Hub::create($data);
    }

    public function update(Hub $hub, array $data): Hub
    {
        $hub->update($data);

        return $hub->refresh();
    }

    public function lockForOrder(int $hubId): Hub
    {
        return Hub::whereKey($hubId)->lockForUpdate()->firstOrFail();
    }

    public function currentLoadForOrder(int $hubId): int
    {
        // A locking read sees the latest committed rows after waiting for the Hub lock.
        return DB::table('orders')
            ->where('hub_id', $hubId)
            ->whereIn('status', ['submitted', 'accepted', 'preparing', 'ready', 'delivering'])
            ->lockForUpdate()
            ->get(['id'])
            ->count();
    }

    public function initializeMenuAvailability(Hub $hub, array $brandIds): void
    {
        $itemIds = MenuItem::whereIn('brand_id', $brandIds)->pluck('id');
        $pivot = $itemIds->mapWithKeys(fn ($itemId) => [$itemId => ['is_available' => false]])->all();
        if ($pivot !== []) {
            $hub->menuItems()->syncWithoutDetaching($pivot);
        }
    }
}
