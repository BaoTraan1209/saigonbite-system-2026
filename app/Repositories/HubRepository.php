<?php

namespace App\Repositories;

use App\Models\Hub;

class HubRepository
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
}
