<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Repositories\{OrderRepository,DeliveryRepository};
class KitchenController extends Controller {
 public function queue(array $p=[]): never { $this->requireRole(['Kitchen','Admin']);$status = $_GET['status'] ?? null; $rows = (new OrderRepository())->listByStatus($status); $this->ok(array_values(array_filter($rows, fn($o) => in_array($o['status'], ['SUBMITTED','ACCEPTED','PREPARING'], true)))); }
 public function update(array $p=[]): never { $this->requireRole(['Kitchen','Admin']);$d=$this->input();$status=$d['status']??'';if(!in_array($status,['ACCEPTED','PREPARING','READY','REJECTED'],true))$this->fail('Invalid kitchen status.');$repo=new OrderRepository();if(!$repo->find((int)$p['id']))$this->fail('Order not found.',404);$repo->updateStatus((int)$p['id'],$status);if($status==='READY')(new DeliveryRepository())->createForOrder((int)$p['id']);$this->ok(['order_id'=>(int)$p['id'],'status'=>$status]); }
}
