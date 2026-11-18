<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Repositories\HubRepository;
use App\Services\HubService;
class HubController extends Controller {
 private HubRepository $repo; private HubService $service; public function __construct(){ $this->repo=new HubRepository();$this->service=new HubService(); }
 public function index(array $p=[]): never { $this->ok($this->repo->all(isset($_GET['available']) && $_GET['available']==='1')); }
 public function create(array $p=[]): never { $this->requireRole(['Admin']);$d=$this->input();if(empty($d['name'])||empty($d['address'])||!isset($d['capacity']))$this->fail('name, address and capacity are required.');$d['status']='DRAFT';$this->ok(['id'=>$this->repo->save($d)],201); }
 public function update(array $p=[]): never { $this->requireRole(['Admin']);$d=$this->input();$id=(int)$p['id'];if(!$this->repo->find($id))$this->fail('Hub not found.',404);$this->ok(['id'=>$this->repo->save($d,$id)]); }
 public function status(array $p=[]): never { $this->requireRole(['Admin']);$d=$this->input();try{$this->service->changeStatus((int)$p['id'],$d['status']??'');$this->ok(['updated'=>true]);}catch(\Throwable $e){$this->fail($e->getMessage(),409);} }
}
