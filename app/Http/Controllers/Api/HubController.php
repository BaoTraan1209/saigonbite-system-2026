<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Hub;
use App\Repositories\HubRepository;
use App\Services\HubService;
use Illuminate\Http\Request;
class HubController extends Controller {
    public function __construct(private HubRepository $repo, private HubService $service) {}
    public function index() { return response()->json($this->repo->all()); }
    public function show(Hub $hub) { return response()->json($hub->load('orders')); }
    public function store(Request $request) {
        $data=$request->validate(['name'=>'required|string|max:150','address'=>'required|string','phone'=>'nullable|string|max:30','capacity'=>'required|integer|min:1']);
        return response()->json($this->service->create($data),201);
    }
    public function update(Request $request, Hub $hub) {
        $data=$request->validate(['name'=>'sometimes|string|max:150','address'=>'sometimes|string','phone'=>'nullable|string|max:30','capacity'=>'sometimes|integer|min:1']);
        return response()->json($this->repo->update($hub,$data));
    }
    public function changeStatus(Request $request, Hub $hub) {
        $data=$request->validate(['status'=>'required|string']);
        return response()->json($this->service->changeStatus($hub,$data['status']));
    }
}
