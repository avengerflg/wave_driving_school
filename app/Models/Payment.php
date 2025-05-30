<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'order_id',  // Add this line
        'user_id',
        'invoice_id',
        'amount',
        'payment_method',
        'transaction_id',
        'status',
        'payment_date',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function order()
    {
        return $this->belongsTo(PackageOrder::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function getStatusBadgeClassAttribute()
    {
        return [
            'completed' => 'bg-success',
            'pending' => 'bg-warning',
            'failed' => 'bg-danger',
            'refunded' => 'bg-info',
        ][$this->status] ?? 'bg-primary';
    }
}