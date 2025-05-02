<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Availability extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'date',
        'start_time',
        'end_time',
        'is_available',
    ];

    protected $table = 'availabilities'; // Updated to plural form

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function scopeAvailableForInstructor($query, $instructorId, $startDate, $endDate)
    {
        return $query->where('instructor_id', $instructorId)
            ->where('date', '>=', $startDate)
            ->where('date', '<=', $endDate)
            ->where('is_available', true)
            ->orderBy('date')
            ->orderBy('start_time');
    }

    public function getStartDateTimeAttribute()
    {
        return Carbon::parse($this->date . ' ' . $this->start_time);
    }

    public function getEndDateTimeAttribute()
    {
        return Carbon::parse($this->date . ' ' . $this->end_time);
    }

    public function isBooked()
    {
        return Booking::where('instructor_id', $this->instructor_id)
            ->where('date', $this->date)
            ->where(function ($query) {
                $query->whereBetween('start_time', [$this->start_time, $this->end_time])
                    ->orWhereBetween('end_time', [$this->start_time, $this->end_time])
                    ->orWhere(function ($q) {
                        $q->where('start_time', '<=', $this->start_time)
                            ->where('end_time', '>=', $this->end_time);
                    });
            })
            ->exists();
    }
}
