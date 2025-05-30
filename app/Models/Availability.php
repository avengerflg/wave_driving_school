<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class Availability extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'date',
        'start_time',
        'end_time',
        'is_available',
        'notes',
        'visibility',
        'private_note',
        'public_note',
        'suburbs',
        'duration_minutes',
    ];

    protected $table = 'availabilities';

    protected $casts = [
        'date' => 'date',
        'is_available' => 'boolean',
        'suburbs' => 'array',
    ];

    /**
     * Get the instructor that owns this availability slot.
     */
    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    /**
     * Get any bookings that overlap with this availability slot.
     */
    public function bookings()
    {
        return Booking::where('instructor_id', $this->instructor_id)
            ->where('date', $this->date)
            ->where(function ($query) {
                $query->where(function ($q) {
                    // Booking starts during availability
                    $q->where('start_time', '>=', $this->start_time)
                        ->where('start_time', '<', $this->end_time);
                })->orWhere(function ($q) {
                    // Booking ends during availability
                    $q->where('end_time', '>', $this->start_time)
                        ->where('end_time', '<=', $this->end_time);
                })->orWhere(function ($q) {
                    // Booking completely overlaps availability
                    $q->where('start_time', '<=', $this->start_time)
                        ->where('end_time', '>=', $this->end_time);
                });
            });
    }

    /**
     * Calculate the duration in minutes.
     */
    public function getDurationAttribute()
    {
        if ($this->duration_minutes) {
            return $this->duration_minutes;
        }
        return Carbon::parse($this->start_time)->diffInMinutes(Carbon::parse($this->end_time));
    }

    /**
     * Get a Carbon instance for the start date and time.
     */
    public function getStartDateTimeAttribute()
    {
        return Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->start_time);
    }

    /**
     * Get a Carbon instance for the end date and time.
     */
    public function getEndDateTimeAttribute()
    {
        return Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->end_time);
    }

    /**
     * Format the start time for display.
     */
    public function getFormattedStartTimeAttribute()
    {
        return Carbon::parse($this->start_time)->format('h:i A');
    }

    /**
     * Format the end time for display.
     */
    public function getFormattedEndTimeAttribute()
    {
        return Carbon::parse($this->end_time)->format('h:i A');
    }

    /**
     * Get the visibility label for display.
     */
    public function getVisibilityLabelAttribute()
    {
        $labels = [
            'public' => 'Publicly Available to book',
            'private' => 'Privately Available, shown as booked',
            'hidden' => 'Hidden note or booking. Hidden from clients',
            'note' => 'Public note only'
        ];

        return $labels[$this->visibility] ?? 'Unknown';
    }

    /**
     * Get formatted duration for display.
     */
    public function getFormattedDurationAttribute()
    {
        $minutes = $this->duration;
        
        if ($minutes < 60) {
            return $minutes . ' mins';
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        if ($remainingMinutes === 0) {
            return $hours . ' hour' . ($hours > 1 ? 's' : '');
        }
        
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ' . $remainingMinutes . ' mins';
    }

    /**
     * Get the suburbs list as a formatted string.
     */
    public function getFormattedSuburbsAttribute()
    {
        if (!$this->suburbs || empty($this->suburbs)) {
            return 'All Suburbs';
        }

        if (in_array('all', $this->suburbs)) {
            return 'All Suburbs';
        }

        return implode(', ', $this->suburbs);
    }

    /**
     * Check if this availability slot is booked.
     */
    public function isBooked()
    {
        return $this->bookings()->exists();
    }

    /**
     * Check if this availability slot is in the past.
     */
    public function isPast()
    {
        return $this->start_date_time->isPast();
    }

    /**
     * Check if this availability is available for booking.
     */
    public function isAvailableForBooking()
    {
        return $this->is_available && 
               $this->visibility === 'public' && 
               !$this->isBooked() && 
               !$this->isPast();
    }

    /**
     * Check if this availability should be visible to clients.
     */
    public function isVisibleToClients()
    {
        return in_array($this->visibility, ['public', 'note']);
    }

    /**
     * Check if this availability should be shown as booked to clients.
     */
    public function shouldShowAsBooked()
    {
        return $this->visibility === 'private';
    }

    /**
     * Check if this availability is hidden from clients.
     */
    public function isHiddenFromClients()
    {
        return $this->visibility === 'hidden';
    }

    /**
     * Scope a query to only include available slots.
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope a query to only include future availability slots.
     */
    public function scopeFuture(Builder $query): Builder
    {
        return $query->whereDate('date', '>=', Carbon::today());
    }

    /**
     * Scope a query to find availability for a specific instructor in a date range.
     */
    public function scopeAvailableForInstructor($query, $instructorId, $startDate, $endDate)
    {
        return $query->where('instructor_id', $instructorId)
            ->where('date', '>=', $startDate)
            ->where('date', '<=', $endDate)
            ->where('is_available', true)
            ->orderBy('date')
            ->orderBy('start_time');
    }

    /**
     * Scope a query to find availability for a specific date.
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope a query to find availability slots that are not booked.
     */
    public function scopeNotBooked($query)
    {
        $query->whereDoesntHave('bookings', function($q) {
            $q->where('status', '!=', 'cancelled');
        });
    }

    /**
     * Scope a query to only include publicly visible availability.
     */
    public function scopePubliclyVisible($query)
    {
        return $query->where('visibility', 'public');
    }

    /**
     * Scope a query to only include availability visible to clients.
     */
    public function scopeVisibleToClients($query)
    {
        return $query->whereIn('visibility', ['public', 'note']);
    }

    /**
     * Scope a query to find availability for specific suburbs.
     */
    public function scopeForSuburbs($query, $suburbs)
    {
        if (empty($suburbs)) {
            return $query;
        }

        return $query->where(function($q) use ($suburbs) {
            $q->whereJsonContains('suburbs', 'all')
              ->orWhere(function($subQuery) use ($suburbs) {
                  foreach ($suburbs as $suburb) {
                      $subQuery->orWhereJsonContains('suburbs', $suburb);
                  }
              });
        });
    }

    /**
     * Get available time slots for an instructor on a specific date.
     */
    public static function getAvailableTimeSlotsForDate($instructorId, $date, $suburbs = null)
    {
        $query = static::where('instructor_id', $instructorId)
            ->whereDate('date', $date)
            ->where('is_available', true)
            ->where('visibility', 'public')
            ->orderBy('start_time');

        if ($suburbs) {
            $query->forSuburbs($suburbs);
        }

        return $query->get()
            ->filter(function ($availability) {
                return !$availability->isBooked();
            });
    }

    /**
     * Get client-visible availability slots.
     */
    public static function getClientVisibleSlots($instructorId, $startDate, $endDate, $suburbs = null)
    {
        $query = static::where('instructor_id', $instructorId)
            ->whereBetween('date', [$startDate, $endDate])
            ->visibleToClients()
            ->future()
            ->orderBy('date')
            ->orderBy('start_time');

        if ($suburbs) {
            $query->forSuburbs($suburbs);
        }

        return $query->get();
    }

    /**
     * Create availability with automatic end time calculation.
     */
    public static function createWithDuration($data)
    {
        if (isset($data['duration']) && isset($data['start_time'])) {
            $startTime = Carbon::createFromFormat('H:i', $data['start_time']);
            $endTime = $startTime->copy()->addMinutes($data['duration']);
            $data['end_time'] = $endTime->format('H:i:s');
            $data['duration_minutes'] = $data['duration'];
            unset($data['duration']);
        }

        return static::create($data);
    }
}
