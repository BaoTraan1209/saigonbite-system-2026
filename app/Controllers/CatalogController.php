<?php
namespace App\Controllers;
use App\Core\{Controller,Request,Database};
use App\Repositories\CatalogRepository;
class CatalogController extends Controller {
 private CatalogRepository $repo; public function __construct(){ $this->repo=new CatalogRepository(); }
 public function brands(array $p=[]): never { $this->ok($this->repo->brands(isset($_GET['hub_id'])?(int)$_GET['hub_id']:null)); }
 public function menu(array $p=[]): never { $this->ok($this->repo->menuItems(isset($_GET['brand_id'])?(int)$_GET['brand_id']:null,isset($_GET['hub_id'])?(int)$_GET['hub_id']:null)); }
 public function saveBrand(array $p=[]): never { $this->requireRole(['Admin']);$d=$this->input();if(empty($d['name']))$this->fail('Brand name is required.');$id=$this->repo->saveBrand($d,isset($p['id'])?(int)$p['id']:null);$this->ok(['id'=>$id],201); }
 public function saveMenu(array $p=[]): never { $this->requireRole(['Admin']);$d=$this->input();if(empty($d['name'])||empty($d['brand_id'])||!isset($d['price']))$this->fail('name, brand_id and price are required.');$id=$this->repo->saveMenu($d,isset($p['id'])?(int)$p['id']:null);$this->ok(['id'=>$id],201); }
 public function deleteBrand(array $p=[]): never { $this->requireRole(['Admin']);try{$this->repo->delete('brands',(int)$p['id']);$this->ok(['deleted'=>true]);}catch(\Throwable $e){$this->fail('Cannot delete brand because it may be used by existing data.',409);} }
 public function deleteMenu(array $p=[]): never { $this->requireRole(['Admin']);try{$this->repo->delete('menu_items',(int)$p['id']);$this->ok(['deleted'=>true]);}catch(\Throwable $e){$this->fail('Cannot delete menu item because it may be used by existing data.',409);} }
}
