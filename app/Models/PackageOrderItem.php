<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageOrderItem extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_id',
        'package_id',
        'quantity',
        'unit_price',
        'gst',
        'total'
    ];
    
    public function order()
    {
        return $this->belongsTo(PackageOrder::class);
    }
    
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}