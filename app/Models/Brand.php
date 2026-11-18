<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Brand extends Model {
    protected $fillable = ['name','description','status'];
    public function menuItems(): HasMany { return $this->hasMany(MenuItem::class); }
    public function hubs() { return $this->belongsToMany(Hub::class, 'hub_brands'); }
}
