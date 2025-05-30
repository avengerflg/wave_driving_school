<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'invoice_number',
        'amount',
        'status',
        'due_date',
        'paid_date',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function getStatusBadgeClassAttribute()
    {
        return [
            'paid' => 'bg-success',
            'pending' => 'bg-warning',
            'overdue' => 'bg-danger',
            'cancelled' => 'bg-secondary',
            'refunded' => 'bg-info',
        ][$this->status] ?? 'bg-primary';
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
            ->whereDate('due_date', '<', now());
    }
}