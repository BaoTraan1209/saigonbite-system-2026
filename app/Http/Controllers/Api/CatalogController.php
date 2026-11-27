<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\StoreMenuItemRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Requests\UpdateMenuItemRequest;
use App\Models\Brand;
use App\Models\MenuItem;
use App\Repositories\Contracts\CatalogRepositoryInterface;
use App\Services\CatalogService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CatalogController extends Controller
{
    public function __construct(
        private CatalogRepositoryInterface $repo,
        private CatalogService $service
    ) {}

    public function index(Request $request)
    {
        $filters = $request->validate(['hub_id' => 'sometimes|integer|exists:hubs,id']);
        if ($request->user()->role === 'customer' && ! isset($filters['hub_id'])) {
            throw ValidationException::withMessages(['hub_id' => 'Select a Hub to browse its catalog.']);
        }

        $brands = isset($filters['hub_id'])
            ? $this->repo->brandsForHub((int) $filters['hub_id'])
            : $this->repo->brands();

        return response()->json($brands);
    }

    public function show(Request $request, Brand $brand)
    {
        if ($request->user()->role === 'customer') {
            $hubId = (int) $request->validate(['hub_id' => 'required|integer|exists:hubs,id'])['hub_id'];
            if ($brand->status !== 'active' || ! $brand->hubs()->whereKey($hubId)->exists()) {
                abort(404);
            }
            $brand->setRelation('menuItems', $this->repo->items($brand->id, $hubId, true));
        } else {
            $brand->load('menuItems');
        }

        return response()->json($brand);
    }

    public function store(StoreBrandRequest $request)
    {
        return response()->json($this->service->createBrand($request->validated()), 201);
    }

    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        return response()->json($this->service->updateBrand($brand, $request->validated()));
    }

    public function destroy(Brand $brand)
    {
        return response()->json($this->service->deactivateBrand($brand));
    }

    public function items(Request $request, Brand $brand)
    {
        if ($request->user()->role === 'customer') {
            $hubId = (int) $request->validate(['hub_id' => 'required|integer|exists:hubs,id'])['hub_id'];
            if ($brand->status !== 'active' || ! $brand->hubs()->whereKey($hubId)->exists()) {
                abort(404);
            }

            return response()->json($this->repo->items($brand->id, $hubId, true));
        }

        return response()->json($brand->menuItems()->latest()->get());
    }

    public function menuItems(Request $request)
    {
        $filters = $request->validate([
            'brand_id' => 'sometimes|integer|exists:brands,id',
            'hub_id' => 'sometimes|integer|exists:hubs,id',
        ]);
        $customer = $request->user()->role === 'customer';
        if ($customer && ! isset($filters['hub_id'])) {
            throw ValidationException::withMessages(['hub_id' => 'Select a Hub to browse menu availability.']);
        }

        return response()->json($this->repo->items(
            isset($filters['brand_id']) ? (int) $filters['brand_id'] : null,
            isset($filters['hub_id']) ? (int) $filters['hub_id'] : null,
            $customer,
            false
        ));
    }

    public function storeItem(StoreMenuItemRequest $request)
    {
        return response()->json($this->service->createMenuItem($request->validated()), 201);
    }

    public function updateItem(UpdateMenuItemRequest $request, MenuItem $menuItem)
    {
        return response()->json($this->service->updateMenuItem($menuItem, $request->validated()));
    }

    public function deleteItem(MenuItem $menuItem)
    {
        return response()->json($this->service->deactivateMenuItem($menuItem));
    }
}
