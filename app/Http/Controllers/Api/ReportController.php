<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function summary(Request $request)
    {
        $filters = $request->validate([
            'from' => 'sometimes|nullable|date',
            'to' => 'sometimes|nullable|date|after_or_equal:from',
        ]);

        $orders = DB::table('orders');
        if (! empty($filters['from'])) {
            $orders->whereDate('created_at', '>=', $filters['from']);
        }
        if (! empty($filters['to'])) {
            $orders->whereDate('created_at', '<=', $filters['to']);
        }

        $deliveries = DB::table('deliveries');
        if (! empty($filters['from'])) {
            $deliveries->whereDate('created_at', '>=', $filters['from']);
        }
        if (! empty($filters['to'])) {
            $deliveries->whereDate('created_at', '<=', $filters['to']);
        }

        return response()->json([
            'sales' => (clone $orders)->where('status', 'completed')->sum('total_amount'),
            'orders_by_status' => (clone $orders)
                ->select('status', DB::raw('COUNT(*) as total'))
                ->groupBy('status')
                ->get(),
            'hub_workload' => DB::table('hubs')->leftJoin('orders', function ($join) {
                $join->on('hubs.id', '=', 'orders.hub_id')
                    ->whereIn('orders.status', [
                        'submitted', 'accepted', 'preparing', 'ready', 'delivering',
                    ]);
            })
                ->select(
                    'hubs.id',
                    'hubs.name',
                    'hubs.capacity',
                    DB::raw('COUNT(orders.id) as current_load')
                )
                ->groupBy('hubs.id', 'hubs.name', 'hubs.capacity')
                ->get(),
            'delivery_performance' => (clone $deliveries)
                ->select('status', DB::raw('COUNT(*) as total'))
                ->groupBy('status')
                ->get(),
        ]);
    }
}
