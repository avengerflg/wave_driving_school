<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageOrder extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
        'booking_for',
        'other_name',
        'other_email',
        'other_phone',
        'address'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function items()
    {
        return $this->hasMany(PackageOrderItem::class, 'order_id');
    }
    
    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id');
    }
}