<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'instructor_id',
        'service_id',
        'suburb_id',
        'date',
        'start_time',
        'end_time',
        'status',
        'notes',
        'booking_for',
        'other_name',
        'other_email',
        'other_phone',
        'address',
        'price',
        'package_credit_id',  // Added for package credit support
        'payment_status',     // Added to track payment status
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Get the user (student) who made the booking.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias for the user relationship to maintain compatibility with student terminology.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the instructor for this booking.
     */
    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    /**
     * Get the service for this booking.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the suburb for this booking.
     */
    public function suburb()
    {
        return $this->belongsTo(Suburb::class);
    }

    /**
     * Get the payment for this booking.
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Get the package credit used for this booking.
     */
    public function packageCredit()
    {
        return $this->belongsTo(PackageCredit::class);
    }

    /**
     * Determine if this booking was paid using a package credit.
     */
    public function usedPackageCredit()
    {
        return !is_null($this->package_credit_id);
    }

    /**
     * Scope a query to only include bookings with a specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include upcoming bookings.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->startOfDay())
                     ->orderBy('date', 'asc')
                     ->orderBy('start_time', 'asc');
    }

    /**
     * Scope a query to only include past bookings.
     */
    public function scopePast($query)
    {
        return $query->where('date', '<', now()->startOfDay())
                     ->orderBy('date', 'desc')
                     ->orderBy('start_time', 'desc');
    }
}