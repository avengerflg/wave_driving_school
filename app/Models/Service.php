<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'duration',
        'active',
        'featured',
    ];

    protected $casts = [
        'active' => 'boolean',
        'featured' => 'boolean',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    
    /**
     * Format price for display
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }
    
    /**
     * Format duration for display
     */
    public function getFormattedDurationAttribute()
    {
        if ($this->duration < 60) {
            return $this->duration . ' minutes';
        }
        
        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;
        
        if ($minutes === 0) {
            return $hours . ' ' . ($hours === 1 ? 'hour' : 'hours');
        }
        
        return $hours . ' ' . ($hours === 1 ? 'hour' : 'hours') . ' ' . $minutes . ' minutes';
    }
}