<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
class DashboardController extends Controller {
    public function index(): View {
        $stats = [
            'orders'=>DB::table('orders')->count(),
            'hubs'=>DB::table('hubs')->count(),
            'brands'=>DB::table('brands')->count(),
            'deliveries'=>DB::table('deliveries')->count(),
        ];
        return view('dashboard', compact('stats'));
    }
}
