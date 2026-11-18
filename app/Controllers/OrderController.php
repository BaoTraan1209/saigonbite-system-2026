<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Repositories\OrderRepository;
use App\Services\OrderService;
class OrderController extends Controller {
 public function create(array $p=[]): never { try{$id=(new OrderService())->place($this->input());$this->ok((new OrderRepository())->find($id),201);}catch(\Throwable $e){$this->fail($e->getMessage(),422);} }
 public function show(array $p=[]): never { $o=(new OrderRepository())->find((int)$p['id']);if(!$o)$this->fail('Order not found.',404);$this->ok($o); }
 public function list(array $p=[]): never { $this->ok((new OrderRepository())->listByStatus(isset($_GET['status'])?$_GET['status']:null)); }
}
