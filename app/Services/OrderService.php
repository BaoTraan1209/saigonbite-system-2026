<?php
namespace App\Services;
use App\Models\{Hub,MenuItem,Order,ActivityLog};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class OrderService {
    public function create(array $data): Order {
        return DB::transaction(function () use ($data) {
            $hub = Hub::whereKey($data['hub_id'])->lockForUpdate()->firstOrFail();
            if ($hub->status !== 'active') throw ValidationException::withMessages(['hub_id'=>'This hub is not accepting orders.']);
            $load = $hub->orders()->whereIn('status',['submitted','accepted','preparing','ready'])->count();
            if ($load >= $hub->capacity) throw ValidationException::withMessages(['hub_id'=>'This hub has reached its capacity.']);
            $subtotal = 0;
            $lines = [];
            foreach ($data['items'] as $item) {
                $menu = MenuItem::whereKey($item['menu_item_id'])->lockForUpdate()->firstOrFail();
                if (!$menu->is_available) throw ValidationException::withMessages(['items'=>'A selected menu item is unavailable.']);
                $qty = (int)$item['quantity'];
                $line = (float)$menu->price * $qty;
                $subtotal += $line;
                $lines[] = ['menu_item_id'=>$menu->id,'item_name'=>$menu->name,'quantity'=>$qty,'unit_price'=>$menu->price,'line_total'=>$line];
            }
            $order = Order::create([
                'hub_id'=>$hub->id,'customer_name'=>$data['customer_name'],'customer_phone'=>$data['customer_phone'],
                'delivery_address'=>$data['delivery_address'],'payment_method'=>$data['payment_method'],
                'subtotal'=>$subtotal,'discount_amount'=>0,'total_amount'=>$subtotal,'status'=>'submitted','notes'=>$data['notes'] ?? null
            ]);
            $order->items()->createMany($lines);
            ActivityLog::create(['actor'=>'Customer','action'=>'order_submitted','subject_type'=>'Order','subject_id'=>$order->id,'details'=>['hub_id'=>$hub->id],'created_at'=>now()]);
            return $order->load(['hub','items']);
        });
    }
}
