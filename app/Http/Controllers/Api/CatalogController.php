<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\{Brand,MenuItem};
use App\Repositories\CatalogRepository;
use Illuminate\Http\Request;
class CatalogController extends Controller {
    public function __construct(private CatalogRepository $repo) {}
    public function index() { return response()->json($this->repo->brands()); }
    public function show(Brand $brand) { return response()->json($brand->load('menuItems')); }
    public function store(Request $request) {
        $data=$request->validate(['name'=>'required|string|max:150','description'=>'nullable|string','status'=>'in:active,inactive,archived']);
        return response()->json($this->repo->createBrand($data),201);
    }
    public function update(Request $request, Brand $brand) {
        $data=$request->validate(['name'=>'sometimes|string|max:150','description'=>'nullable|string','status'=>'sometimes|in:active,inactive,archived']);
        $brand->update($data); return response()->json($brand);
    }
    public function destroy(Brand $brand) {
        if ($brand->menuItems()->exists()) return response()->json(['message'=>'Archive this brand while menu items are linked.'],422);
        $brand->delete(); return response()->json(['message'=>'Brand deleted.']);
    }
    public function items(Brand $brand) { return response()->json($brand->menuItems); }
    public function storeItem(Request $request) {
        $data=$request->validate(['brand_id'=>'required|exists:brands,id','name'=>'required|string|max:150','description'=>'nullable|string','price'=>'required|numeric|min:0','is_available'=>'boolean']);
        return response()->json($this->repo->createItem($data),201);
    }
    public function updateItem(Request $request, MenuItem $menuItem) {
        $menuItem->update($request->validate(['name'=>'sometimes|string|max:150','description'=>'nullable|string','price'=>'sometimes|numeric|min:0','is_available'=>'sometimes|boolean']));
        return response()->json($menuItem);
    }
    public function deleteItem(MenuItem $menuItem) { $menuItem->delete(); return response()->json(['message'=>'Menu item deleted.']); }
}
