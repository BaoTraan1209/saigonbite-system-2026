<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Repositories\{OrderRepository,DeliveryRepository};
class DeliveryController extends Controller {
 public function queue(array $p=[]): never { $this->requireRole(['Shipper','Admin']);$this->ok((new OrderRepository())->listByStatus('READY')); }
 public function update(array $p=[]): never { $this->requireRole(['Shipper','Admin']);$d=$this->input();$status=$d['status']??'';if(!in_array($status,['PICKED_UP','DELIVERED','FAILED'],true))$this->fail('Invalid delivery status.');$orders=new OrderRepository();$order=$orders->find((int)$p['id']);if(!$order)$this->fail('Order not found.',404);(new DeliveryRepository())->update((int)$p['id'],$d);if($status==='DELIVERED')$orders->updateStatus((int)$p['id'],'DELIVERED');if($status==='FAILED')$orders->updateStatus((int)$p['id'],'DELIVERY_FAILED');$this->ok(['order_id'=>(int)$p['id'],'delivery_status'=>$status]); }
}
