<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Repositories\ReportRepository;
class ReportController extends Controller {
 public function index(array $p=[]): never { $this->requireRole(['Manager','Admin']);$from=$_GET['from']??date('Y-m-01');$to=$_GET['to']??date('Y-m-d');$r=new ReportRepository();$this->ok(['period'=>['from'=>$from,'to'=>$to],'sales'=>$r->sales($from,$to),'hub_workload'=>$r->hubWorkload(),'delivery_performance'=>$r->deliveryPerformance($from,$to)]); }
}
