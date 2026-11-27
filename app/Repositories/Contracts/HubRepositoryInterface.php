<?php

namespace App\Repositories\Contracts;

use App\Models\Hub;

interface HubRepositoryInterface
{
    public function all(bool $availableOnly = false);
    public function create(array $data): Hub;
    public function update(Hub $hub, array $data): Hub;
    public function lockForOrder(int $hubId): Hub;
    public function currentLoadForOrder(int $hubId): int;
    public function initializeMenuAvailability(Hub $hub, array $brandIds): void;
}
