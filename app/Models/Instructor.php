<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'license_number',
        'bio',
        'profile_image',
        'active',
        'suburbs',
    ];

    protected $casts = [
        'suburbs' => 'array',
        'active' => 'boolean',
    ];

    /**
     * Mutator to ensure suburbs are stored as integers in JSON
     */
    public function setSuburbsAttribute($value)
    {
        $this->attributes['suburbs'] = json_encode(array_map('intval', (array) $value));
    }

    /**
     * Get the user that owns the instructor profile
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all bookings for the instructor
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get all availability slots for the instructor
     */
    public function availability()
    {
        return $this->hasMany(Availability::class);
    }

    /**
     * Get all suburbs through the pivot table
     */
    public function suburbs()
    {
        return $this->belongsToMany(Suburb::class, 'instructor_suburb');
    }

    /**
     * Sync suburbs between JSON column and pivot table
     */
    public function syncSuburbsWithPivot()
    {
        if (isset($this->attributes['suburbs'])) {
            $suburbIds = json_decode($this->attributes['suburbs'], true);
            $this->suburbs()->sync($suburbIds);
        }
    }

    /**
     * Check if instructor services a specific suburb
     */
    public function servicesSuburb($suburbId)
    {
        // Check both JSON column and pivot table
        return in_array($suburbId, (array) $this->suburbs) || 
               $this->suburbs()->where('suburbs.id', $suburbId)->exists();
    }

    /**
     * Override the save method to sync suburbs
     */
    public function save(array $options = [])
    {
        $result = parent::save($options);
        $this->syncSuburbsWithPivot();
        return $result;
    }

    /**
     * Scope query to find instructors in a specific suburb
     */
    public function scopeInSuburb($query, $suburbId)
    {
        return $query->where(function($q) use ($suburbId) {
            $q->whereJsonContains('suburbs', $suburbId)
              ->orWhereHas('suburbs', function($subQ) use ($suburbId) {
                  $subQ->where('suburbs.id', $suburbId);
              });
        });
    }

    /**
     * Get all active instructors in a suburb
     */
    public static function getActiveInSuburb($suburbId)
    {
        return static::with(['user', 'suburbs'])
            ->where('active', true)
            ->inSuburb($suburbId)
            ->get();
    }
}
