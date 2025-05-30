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
        'services',      // Added to track instructor services
        'commission_rate', // Added for potential commission tracking
        'featured',      // Added to mark featured instructors
    ];

    protected $casts = [
        'suburbs' => 'array',
        'services' => 'array', // Cast services as array
        'active' => 'boolean',
        'featured' => 'boolean',
        'commission_rate' => 'float',
    ];

    /**
     * Mutator to ensure suburbs are stored as integers in JSON
     */
    public function setSuburbsAttribute($value)
    {
        $this->attributes['suburbs'] = json_encode(array_map('intval', (array) $value));
    }

    /**
     * Mutator to ensure services are stored as integers in JSON
     */
    public function setServicesAttribute($value)
    {
        $this->attributes['services'] = json_encode(array_map('intval', (array) $value));
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
     * Get all suburbs for this instructor
     */
    public function suburbs()
    {
        return $this->belongsToMany(Suburb::class, 'instructor_suburb', 'instructor_id', 'suburb_id');
    }

    /**
     * Get all services this instructor provides
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'instructor_service', 'instructor_id', 'service_id')
                    ->withPivot('price', 'active')
                    ->withTimestamps();
    }

    /**
     * Get all students (users) who have booked with this instructor
     */
    public function students()
    {
        return User::whereIn('id', $this->bookings()->pluck('user_id')->unique());
    }

    /**
     * Get all package credits used in bookings with this instructor
     */
    public function packageCreditsUsed()
    {
        return PackageCredit::whereIn('id', $this->bookings()->whereNotNull('package_credit_id')->pluck('package_credit_id')->unique());
    }

    /**
     * Get student package orders related to this instructor
     * (Orders from students who have booked with this instructor)
     */
    public function studentPackageOrders()
    {
        $studentIds = $this->bookings()->pluck('user_id')->unique();
        return PackageOrder::whereIn('user_id', $studentIds);
    }

    /**
     * Get upcoming package-based bookings
     */
    public function upcomingPackageBookings()
    {
        return $this->bookings()
                    ->whereNotNull('package_credit_id')
                    ->where('date', '>=', now()->startOfDay())
                    ->orderBy('date', 'asc')
                    ->orderBy('start_time', 'asc');
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
     * Sync services between JSON column and pivot table
     */
    public function syncServicesWithPivot()
    {
        if (isset($this->attributes['services'])) {
            $serviceIds = json_decode($this->attributes['services'], true);
            $this->services()->sync($serviceIds);
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
     * Check if instructor provides a specific service
     */
    public function providesService($serviceId)
    {
        return in_array($serviceId, (array) $this->services) || 
               $this->services()->where('services.id', $serviceId)->exists();
    }

    /**
     * Get instructor's package-based lesson count
     */
    public function packageLessonCount()
    {
        return $this->bookings()
                    ->whereNotNull('package_credit_id')
                    ->count();
    }

    /**
     * Get total revenue from non-package bookings
     */
    public function directBookingRevenue()
    {
        return $this->bookings()
                    ->whereNull('package_credit_id')
                    ->where('status', 'completed')
                    ->sum('price');
    }

    /**
     * Calculate average rating from reviews
     */
    public function averageRating()
    {
        $reviews = $this->reviews;
        if ($reviews->isEmpty()) {
            return 0;
        }
        return $reviews->avg('rating');
    }

    /**
     * Override the save method to sync suburbs and services
     */
    public function save(array $options = [])
    {
        $result = parent::save($options);
        $this->syncSuburbsWithPivot();
        $this->syncServicesWithPivot();
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
     * Scope query to find instructors who provide a specific service
     */
    public function scopeWithService($query, $serviceId)
    {
        return $query->where(function($q) use ($serviceId) {
            $q->whereJsonContains('services', $serviceId)
              ->orWhereHas('services', function($subQ) use ($serviceId) {
                  $subQ->where('services.id', $serviceId);
              });
        });
    }

    /**
     * Scope query to find featured instructors
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
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

    /**
     * Get the package statistics for this instructor
     */
    public function getPackageStats()
    {
        $totalPackageBookings = $this->bookings()->whereNotNull('package_credit_id')->count();
        $completedPackageBookings = $this->bookings()->whereNotNull('package_credit_id')->where('status', 'completed')->count();
        $uniqueStudentsWithPackages = $this->bookings()->whereNotNull('package_credit_id')->distinct('user_id')->count('user_id');
        
        return [
            'total_package_bookings' => $totalPackageBookings,
            'completed_package_bookings' => $completedPackageBookings,
            'conversion_rate' => $totalPackageBookings > 0 ? ($completedPackageBookings / $totalPackageBookings) * 100 : 0,
            'unique_students_with_packages' => $uniqueStudentsWithPackages,
        ];
    }
}