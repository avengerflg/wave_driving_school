<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Availability;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AvailabilityController extends Controller
{
    public function index()
    {
        $instructor = Auth::user()->instructor;
        
        // Get future availabilities
        $availabilities = Availability::where('instructor_id', $instructor->id)
            ->whereDate('date', '>=', Carbon::today())
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
        
        // Group availabilities by date
        $availabilitiesByDate = $availabilities->groupBy(function($availability) {
            return Carbon::parse($availability->date)->format('Y-m-d');
        });
        
        return view('instructor.availability.index', compact('availabilitiesByDate'));
    }
    
    public function create()
    {
        return view('instructor.availability.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'repeat' => 'nullable|in:none,daily,weekly',
            'repeat_until' => 'nullable|required_if:repeat,daily,weekly|date|after:date',
        ]);
        
        $instructor = Auth::user()->instructor;
        
        // Check if repeat is enabled
        if ($request->repeat && $request->repeat !== 'none') {
            $dates = [];
            $startDate = Carbon::parse($request->date);
            $endDate = Carbon::parse($request->repeat_until);
            
            if ($request->repeat === 'daily') {
                $period = CarbonPeriod::create($startDate, '1 day', $endDate);
                foreach ($period as $date) {
                    $dates[] = $date->format('Y-m-d');
                }
            } elseif ($request->repeat === 'weekly') {
                $period = CarbonPeriod::create($startDate, '1 week', $endDate);
                foreach ($period as $date) {
                    $dates[] = $date->format('Y-m-d');
                }
            }
            
            // Create availability for each date
            foreach ($dates as $date) {
                $this->createAvailability($instructor->id, $date, $request->start_time, $request->end_time);
            }
            
            return redirect()->route('instructor.availability.index')
                ->with('success', 'Recurring availability has been added successfully.');
        } else {
            // Create single availability
            $this->createAvailability($instructor->id, $request->date, $request->start_time, $request->end_time);
            
            return redirect()->route('instructor.availability.index')
                ->with('success', 'Availability has been added successfully.');
        }
    }
    
    private function createAvailability($instructorId, $date, $startTime, $endTime)
    {
        // Check for overlapping availabilities
        $overlapping = Availability::where('instructor_id', $instructorId)
            ->where('date', $date)
            ->where(function($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })
            ->exists();
        
        if (!$overlapping) {
            Availability::create([
                'instructor_id' => $instructorId,
                'date' => $date,
                'start_time' => $startTime,
                'end_time' => $endTime,
            ]);
        }
    }
    
    public function destroy(Availability $availability)
    {
        // Check if the availability belongs to the authenticated instructor
        if ($availability->instructor_id !== Auth::user()->instructor->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if the availability has a booking
        $hasBooking = Booking::where('availability_id', $availability->id)->exists();
        
        if ($hasBooking) {
            return back()->with('error', 'Cannot delete availability with an existing booking.');
        }
        
        $availability->delete();
        
        return redirect()->route('instructor.availability.index')
            ->with('success', 'Availability has been deleted successfully.');
    }
    
    public function bulkCreate()
    {
        return view('instructor.availability.bulk-create');
    }
    
    public function bulkStore(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'days' => 'required|array',
            'days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);
        
        $instructor = Auth::user()->instructor;
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $days = $request->days;
        
        $period = CarbonPeriod::create($startDate, '1 day', $endDate);
        $count = 0;
        
        foreach ($period as $date) {
            $dayName = strtolower($date->format('l'));
            
            if (in_array($dayName, $days)) {
                $this->createAvailability(
                    $instructor->id,
                    $date->format('Y-m-d'),
                    $request->start_time,
                    $request->end_time
                );
                $count++;
            }
        }
        
        return redirect()->route('instructor.availability.index')
            ->with('success', $count . ' availabilities have been added successfully.');
    }
}
