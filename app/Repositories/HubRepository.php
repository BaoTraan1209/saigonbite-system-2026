<?php
namespace App\Repositories;
use App\Models\Hub;
class HubRepository {
    public function all() { return Hub::withCount('orders')->latest()->get(); }
    public function create(array $data): Hub { return Hub::create($data); }
    public function update(Hub $hub, array $data): Hub { $hub->update($data); return $hub->refresh(); }
}
