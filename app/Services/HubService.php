<?php

namespace App\Services;

use App\Models\Hub;
use App\Repositories\HubRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class HubService
{
    public function __construct(private HubRepository $hubs) {}

    public function create(array $data): Hub
    {
        $brandIds = $data['brand_ids'] ?? null;
        unset($data['brand_ids']);

        return DB::transaction(function () use ($data, $brandIds) {
            $data['status'] = 'draft';
            $hub = $this->hubs->create($data);
            if ($brandIds !== null) {
                $hub->brands()->sync($brandIds);
            }

            return $hub->load('brands');
        });
    }

    public function changeStatus(Hub $hub, string $status): Hub
    {
        return DB::transaction(function () use ($hub, $status) {
            $hub = Hub::whereKey($hub->id)->lockForUpdate()->firstOrFail();

            if ($hub->status === 'archived') {
                throw ValidationException::withMessages(['status' => 'Archived hubs cannot be changed.']);
            }

            $transitions = [
                'draft' => ['active', 'archived'],
                'active' => ['suspending', 'suspended', 'archived'],
                'suspending' => ['active', 'suspended', 'archived'],
                'suspended' => ['active', 'archived'],
            ];
            if (! in_array($status, $transitions[$hub->status] ?? [], true)) {
                throw ValidationException::withMessages(['status' => 'Invalid hub status transition.']);
            }

            $unfinished = $hub->orders()
                ->whereIn('status', ['submitted', 'accepted', 'preparing', 'ready', 'delivering'])
                ->exists();
            if ($status === 'archived' && $unfinished) {
                throw ValidationException::withMessages([
                    'status' => 'A hub with unfinished orders cannot be archived.',
                ]);
            }
            if ($status === 'suspended' && $unfinished) {
                $status = 'suspending';
            }
            if ($status === 'active' && ! $hub->brands()->where('brands.status', 'active')->exists()) {
                throw ValidationException::withMessages([
                    'status' => 'Assign an active brand to the hub before activating it.',
                ]);
            }

            $hub->update(['status' => $status]);

            return $hub->refresh()->load('brands');
        });
    }
}
