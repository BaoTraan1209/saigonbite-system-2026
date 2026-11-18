<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ActivityLog extends Model {
    public $timestamps = false;
    protected $fillable = ['actor','action','subject_type','subject_id','details','created_at'];
    protected $casts = ['details'=>'array','created_at'=>'datetime'];
}
