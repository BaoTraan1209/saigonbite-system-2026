<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
class DeliveryController extends Controller {
    public function index() { return response()->json(Delivery::with('order')->latest()->get()); }
    public function updateStatus(Request $request, Delivery $delivery) {
        $data=$request->validate(['status'=>'required|in:assigned,picked_up,delivered,failed','shipper_name'=>'nullable|string|max:150','shipper_phone'=>'nullable|string|max:30','cod_amount'=>'nullable|numeric|min:0','failure_reason'=>'nullable|string']);
        $allowed=['waiting'=>['assigned'],'assigned'=>['picked_up'],'picked_up'=>['delivered','failed']];
        if (!in_array($data['status'],$allowed[$delivery->status] ?? [],true)) throw ValidationException::withMessages(['status'=>'Invalid delivery status transition.']);
        $delivery->update($data);
        if ($data['status']==='picked_up') $delivery->order()->update(['status'=>'delivering']);
        if ($data['status']==='delivered') { $delivery->update(['delivered_at'=>now()]); $delivery->order()->update(['status'=>'completed']); }
        if ($data['status']==='failed') $delivery->order()->update(['status'=>'delivery_failed']);
        return response()->json($delivery->refresh()->load('order'));
    }
}
