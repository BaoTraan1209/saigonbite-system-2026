<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hub;
use App\Repositories\HubRepository;
use App\Services\HubService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class HubController extends Controller
{
    public function __construct(private HubRepository $repo, private HubService $service) {}

    public function index(Request $request)
    {
        $filters = $request->validate([
            'available' => 'sometimes|boolean',
        ]);

        return response()->json($this->repo->all((bool) ($filters['available'] ?? false)));
    }

    public function show(Hub $hub)
    {
        return response()->json($hub->load('brands', 'orders'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:30',
            'capacity' => 'required|integer|min:1',
            'brand_ids' => 'sometimes|array',
            'brand_ids.*' => 'integer|distinct|exists:brands,id',
        ]);

        return response()->json($this->service->create($data), 201);
    }

    public function update(Request $request, Hub $hub)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:150',
            'address' => 'sometimes|required|string',
            'phone' => 'sometimes|nullable|string|max:30',
            'capacity' => 'sometimes|integer|min:1',
        ]);
        $updated = DB::transaction(function () use ($hub, $data) {
            $hub = Hub::whereKey($hub->id)->lockForUpdate()->firstOrFail();
            if (isset($data['capacity']) && $data['capacity'] < $hub->current_load) {
                throw ValidationException::withMessages([
                    'capacity' => 'Capacity cannot be lower than the hub current load.',
                ]);
            }

            return $this->repo->update($hub, $data);
        });

        return response()->json($updated);
    }

    public function changeStatus(Request $request, Hub $hub)
    {
        $data = $request->validate([
            'status' => 'required|in:active,suspending,suspended,archived',
        ]);

        return response()->json($this->service->changeStatus($hub, $data['status']));
    }

    public function syncBrands(Request $request, Hub $hub)
    {
        $data = $request->validate([
            'brand_ids' => 'required|array',
            'brand_ids.*' => 'integer|distinct|exists:brands,id',
        ]);
        DB::transaction(function () use ($hub, $data) {
            $hub = Hub::whereKey($hub->id)->lockForUpdate()->firstOrFail();
            $hub->brands()->sync($data['brand_ids']);
        });

        return response()->json($hub->refresh()->load('brands'));
    }
}
