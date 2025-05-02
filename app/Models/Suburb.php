<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suburb extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'state',
        'postcode',
        'active',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function instructors()
    {
        return $this->belongsToMany(Instructor::class);
    }
}
