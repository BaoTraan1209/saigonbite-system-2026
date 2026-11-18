<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class ReportController extends Controller {
    public function summary() {
        return response()->json([
            'sales'=>DB::table('orders')->where('status','completed')->sum('total_amount'),
            'orders_by_status'=>DB::table('orders')->select('status',DB::raw('COUNT(*) as total'))->groupBy('status')->get(),
            'hub_workload'=>DB::table('hubs')->leftJoin('orders','hubs.id','=','orders.hub_id')
                ->select('hubs.id','hubs.name','hubs.capacity',DB::raw("SUM(CASE WHEN orders.status IN ('submitted','accepted','preparing','ready','delivering') THEN 1 ELSE 0 END) as current_load"))
                ->groupBy('hubs.id','hubs.name','hubs.capacity')->get(),
            'delivery_performance'=>DB::table('deliveries')->select('status',DB::raw('COUNT(*) as total'))->groupBy('status')->get()
        ]);
    }
}
