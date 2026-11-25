<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\MenuItem;
use App\Repositories\CatalogRepository;
use App\Services\CatalogService;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function __construct(
        private CatalogRepository $repo,
        private CatalogService $service
    ) {}

    public function index(Request $request)
    {
        $filters = $request->validate([
            'hub_id' => 'sometimes|integer|exists:hubs,id',
        ]);

        $brands = isset($filters['hub_id'])
            ? $this->repo->brandsForHub((int) $filters['hub_id'])
            : $this->repo->brands();

        return response()->json($brands);
    }

    public function show(Brand $brand)
    {
        return response()->json($brand->load('menuItems'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:active,inactive,archived',
        ]);

        return response()->json($this->service->createBrand($data), 201);
    }

    public function update(Request $request, Brand $brand)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:150',
            'description' => 'sometimes|nullable|string',
            'status' => 'sometimes|in:active,inactive,archived',
        ]);

        return response()->json($this->service->updateBrand($brand, $data));
    }

    public function destroy(Brand $brand)
    {
        $this->service->deleteBrand($brand);

        return response()->json(['message' => 'Brand deleted.']);
    }

    public function items(Brand $brand)
    {
        return response()->json($brand->menuItems);
    }

    public function menuItems(Request $request)
    {
        $filters = $request->validate([
            'brand_id' => 'sometimes|integer|exists:brands,id',
            'hub_id' => 'sometimes|integer|exists:hubs,id',
        ]);

        return response()->json($this->repo->items(
            isset($filters['brand_id']) ? (int) $filters['brand_id'] : null,
            isset($filters['hub_id']) ? (int) $filters['hub_id'] : null
        ));
    }

    public function storeItem(Request $request)
    {
        $data = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_available' => 'sometimes|boolean',
        ]);

        return response()->json($this->service->createMenuItem($data), 201);
    }

    public function updateItem(Request $request, MenuItem $menuItem)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:150',
            'description' => 'sometimes|nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'is_available' => 'sometimes|boolean',
        ]);

        return response()->json($this->service->updateMenuItem($menuItem, $data));
    }

    public function deleteItem(MenuItem $menuItem)
    {
        $this->service->deleteMenuItem($menuItem);

        return response()->json(['message' => 'Menu item deleted.']);
    }
}
