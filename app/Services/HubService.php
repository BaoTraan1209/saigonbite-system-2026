<?php
namespace App\Services;
use App\Models\Hub;
use App\Repositories\HubRepository;
use Illuminate\Validation\ValidationException;
class HubService {
    public function __construct(private HubRepository $hubs) {}
    public function create(array $data): Hub {
        $data['status'] = 'draft';
        return $this->hubs->create($data);
    }
    public function changeStatus(Hub $hub, string $status): Hub {
        if ($hub->status === 'archived') throw ValidationException::withMessages(['status'=>'Archived hubs cannot be changed.']);
        if (!in_array($status, ['active','suspending','suspended','archived'], true)) {
            throw ValidationException::withMessages(['status'=>'Invalid hub status.']);
        }
        if ($status === 'active' && \App\Models\Brand::where('status','active')->doesntExist()) {
            throw ValidationException::withMessages(['status'=>'Create an active brand before activating a hub.']);
        }
        $unfinished = $hub->orders()->whereIn('status',['submitted','accepted','preparing','ready'])->exists();
        if ($status === 'suspended' && $unfinished) $status = 'suspending';
        $hub->update(['status'=>$status]);
        return $hub->refresh();
    }
}
