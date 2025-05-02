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
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function suburb()
    {
        return $this->belongsTo(Suburb::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
