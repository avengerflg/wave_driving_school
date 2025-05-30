<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PackageCredit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'package_id',
        'order_id',
        'total',
        'remaining',
        'status',
        'expires_at',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'total' => 'integer',
        'remaining' => 'integer',
    ];

    /**
     * Get the user that owns the package credit.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the package associated with this credit.
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Get the order that created this credit.
     */
    public function order()
    {
        return $this->belongsTo(PackageOrder::class, 'order_id');
    }

    /**
     * Get the bookings that used this credit.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Check if the credit is active.
     */
    public function isActive()
    {
        return $this->status === 'active' && 
               $this->remaining > 0 && 
               ($this->expires_at === null || $this->expires_at->isFuture());
    }

    /**
     * Check if the credit is expired.
     */
    public function isExpired()
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    /**
     * Check if this credit has been fully used.
     */
    public function isFullyUsed()
    {
        return $this->remaining <= 0;
    }

    /**
     * Use one credit.
     * 
     * @return bool Whether the credit was successfully used
     */
    public function useCredit()
    {
        if (!$this->isActive()) {
            return false;
        }

        $this->remaining -= 1;
        
        if ($this->remaining <= 0) {
            $this->status = 'used';
        }
        
        return $this->save();
    }

    /**
     * Return one credit back to the balance.
     * 
     * @return bool Whether the credit was successfully returned
     */
    public function returnCredit()
    {
        if ($this->remaining >= $this->total) {
            return false;
        }
        
        $this->remaining += 1;
        
        if ($this->status === 'used' && $this->remaining > 0) {
            $this->status = 'active';
        }
        
        return $this->save();
    }

    /**
     * Calculate usage percentage.
     */
    public function usagePercentage()
    {
        if ($this->total <= 0) {
            return 0;
        }
        
        return (($this->total - $this->remaining) / $this->total) * 100;
    }

    /**
     * Extend expiry date by given number of days.
     * 
     * @param int $days Number of days to extend
     * @return bool Whether the expiry was successfully extended
     */
    public function extendExpiry($days)
    {
        if ($this->expires_at === null) {
            $this->expires_at = now()->addDays($days);
        } else {
            $this->expires_at = $this->expires_at->addDays($days);
        }
        
        return $this->save();
    }

    /**
     * Mark the credit as expired.
     */
    public function markAsExpired()
    {
        $this->status = 'expired';
        return $this->save();
    }

    /**
     * Activate the credit.
     */
    public function activate()
    {
        if ($this->remaining > 0) {
            $this->status = 'active';
            return $this->save();
        }
        
        return false;
    }

    /**
     * Deactivate the credit.
     */
    public function deactivate()
    {
        $this->status = 'inactive';
        return $this->save();
    }

    /**
     * Scope a query to only include active credits.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('remaining', '>', 0)
                     ->where(function ($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                     });
    }

    /**
     * Scope a query to only include expired credits.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    /**
     * Scope a query to only include used credits.
     */
    public function scopeUsed($query)
    {
        return $query->where('remaining', 0);
    }

    /**
     * Scope a query to only include unused credits.
     */
    public function scopeUnused($query)
    {
        return $query->where('remaining', '>', 0);
    }

    /**
     * Scope a query to only include credits for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include credits for a specific package.
     */
    public function scopeForPackage($query, $packageId)
    {
        return $query->where('package_id', $packageId);
    }

    /**
     * Scope a query to only include credits from a specific order.
     */
    public function scopeFromOrder($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    /**
     * Scope a query to only include credits expiring soon.
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expires_at')
                     ->whereBetween('expires_at', [now(), now()->addDays($days)]);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-check expiry on retrieval
        static::retrieved(function ($credit) {
            if ($credit->status === 'active' && 
                $credit->expires_at !== null && 
                $credit->expires_at->isPast()) {
                $credit->markAsExpired();
            }
        });
    }
}