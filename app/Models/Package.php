<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'price',
        'active',
        'lessons',
        'duration'
    ];
    
    public function orders()
    {
        return $this->hasMany(PackageOrderItem::class);
    }
}