<?php
namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AvailabilityController extends Controller
{
    /**
     * Display the instructor's availability calendar
     */
    public function index(Request $request)
    {
        $instructor = Auth::user()->instructor;

        try {
            // Set up calendar dates
            $today = Carbon::today();
            $viewMonth = $this->getViewMonth($request, $today);
            $calendarDates = $this->getCalendarDates($viewMonth);

            // Get availabilities for the date range
            $availabilities = $this->getAvailabilities(
                $instructor->id,
                $calendarDates['startDate'],
                $calendarDates['endDate']
            );

            $services = \App\Models\Service::where('active', true)->get();
            return view('instructor.availability.index', [
                'groupedAvailabilities' => $availabilities->groupBy(function($item) {
                    return $item->date->format('Y-m-d');
                }),
                'startDate' => $calendarDates['startDate'],
                'endDate' => $calendarDates['endDate'],
                'prevMonth' => $viewMonth->copy()->subMonth()->format('Y-m'),
                'nextMonth' => $viewMonth->copy()->addMonth()->format('Y-m'),
                'currentMonth' => $viewMonth->format('Y-m'),
                'viewMonth' => $viewMonth,
                'weeks' => $calendarDates['weeks'],
                'services' => $services,
            ]);

        } catch (\Exception $e) {
            Log::error('Error displaying calendar: ' . $e->getMessage());
            return back()->with('error', 'Error displaying calendar');
        }
    }

    /**
     * Store a new availability slot
     */
    public function store(Request $request)
    {
        $instructor = Auth::user()->instructor;

        // Check if it's a routine schedule request
        if ($request->has('routine_schedule')) {
            return $this->storeRoutineSchedule($request, $instructor);
        }

        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_recurring' => 'nullable|boolean',
            'recur_until' => 'nullable|required_if:is_recurring,1|date|after:date',
            'days_of_week' => 'nullable|required_if:is_recurring,1|array',
            'days_of_week.*' => 'nullable|integer|between:0,6',
        ]);

        DB::beginTransaction();

        try {
            $dates = $this->calculateDateRange($validated);
            $daysOfWeek = $this->getDaysOfWeek($request, $validated, $dates['startDate']);

            $createdCount = 0;
            $currentDate = $dates['startDate']->copy();

            while ($currentDate->lte($dates['endDate'])) {
                if (in_array((int)$currentDate->dayOfWeek, $daysOfWeek)) {
                    $createdCount += $this->createSlotsForDate(
                        $instructor,
                        $currentDate,
                        $validated['start_time'],
                        $validated['end_time']
                    );
                }
                $currentDate->addDay();
            }

            DB::commit();

            return redirect()->route('instructor.availability.index')
                ->with('success', "{$createdCount} availability slots created successfully");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating availability: ' . $e->getMessage());
            return back()->with('error', 'Error creating availability slots: ' . $e->getMessage());
        }
    }

    /**
     * Store routine schedule (Monday-Friday 7am-4pm, Saturday 7am-5pm)
     */
    public function storeRoutineSchedule(Request $request, $instructor)
{
    $validated = $request->validate([
        'routine_start_date' => 'required|date|after_or_equal:today',
        'routine_end_date' => 'required|date|after:routine_start_date',
        'routine_schedule_type' => 'required|in:standard,custom', // Changed from 'routine_schedule'
        'monday_start' => 'nullable|date_format:H:i',
        'monday_end' => 'nullable|date_format:H:i|after:monday_start',
        'tuesday_start' => 'nullable|date_format:H:i',
        'tuesday_end' => 'nullable|date_format:H:i|after:tuesday_start',
        'wednesday_start' => 'nullable|date_format:H:i',
        'wednesday_end' => 'nullable|date_format:H:i|after:wednesday_start',
        'thursday_start' => 'nullable|date_format:H:i',
        'thursday_end' => 'nullable|date_format:H:i|after:thursday_start',
        'friday_start' => 'nullable|date_format:H:i',
        'friday_end' => 'nullable|date_format:H:i|after:friday_start',
        'saturday_start' => 'nullable|date_format:H:i',
        'saturday_end' => 'nullable|date_format:H:i|after:saturday_start',
        'sunday_start' => 'nullable|date_format:H:i',
        'sunday_end' => 'nullable|date_format:H:i|after:sunday_start',
    ]);

    DB::beginTransaction();

    try {
        $startDate = Carbon::parse($validated['routine_start_date']);
        $endDate = Carbon::parse($validated['routine_end_date']);
        
        // Define standard schedule or use custom
        $schedule = $this->getRoutineSchedule($validated);
        
        $createdCount = 0;
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dayOfWeek = $currentDate->dayOfWeek; // 0=Sunday, 1=Monday, etc.
            
            if (isset($schedule[$dayOfWeek]) && $schedule[$dayOfWeek]['enabled']) {
                $createdCount += $this->createSlotsForDate(
                    $instructor,
                    $currentDate,
                    $schedule[$dayOfWeek]['start'],
                    $schedule[$dayOfWeek]['end']
                );
            }
            $currentDate->addDay();
        }

        DB::commit();

        return redirect()->route('instructor.availability.index')
            ->with('success', "{$createdCount} routine availability slots created successfully");

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error creating routine schedule: ' . $e->getMessage());
        return back()->with('error', 'Error creating routine schedule: ' . $e->getMessage());
    }
}

/**
 * Get routine schedule based on type
 */
private function getRoutineSchedule($validated)
{
    if ($validated['routine_schedule_type'] === 'standard') { // Changed from 'routine_schedule'
        // Standard: Monday-Friday 7am-4pm, Saturday 7am-5pm, Sunday off
        return [
            0 => ['enabled' => false], // Sunday
            1 => ['enabled' => true, 'start' => '07:00', 'end' => '16:00'], // Monday
            2 => ['enabled' => true, 'start' => '07:00', 'end' => '16:00'], // Tuesday
            3 => ['enabled' => true, 'start' => '07:00', 'end' => '16:00'], // Wednesday
            4 => ['enabled' => true, 'start' => '07:00', 'end' => '16:00'], // Thursday
            5 => ['enabled' => true, 'start' => '07:00', 'end' => '16:00'], // Friday
            6 => ['enabled' => true, 'start' => '07:00', 'end' => '17:00'], // Saturday
        ];
    } else {
        // Custom schedule from form inputs
        return [
            0 => [
                'enabled' => !empty($validated['sunday_start']) && !empty($validated['sunday_end']),
                'start' => $validated['sunday_start'] ?? '07:00',
                'end' => $validated['sunday_end'] ?? '16:00'
            ],
            1 => [
                'enabled' => !empty($validated['monday_start']) && !empty($validated['monday_end']),
                'start' => $validated['monday_start'] ?? '07:00',
                'end' => $validated['monday_end'] ?? '16:00'
            ],
            2 => [
                'enabled' => !empty($validated['tuesday_start']) && !empty($validated['tuesday_end']),
                'start' => $validated['tuesday_start'] ?? '07:00',
                'end' => $validated['tuesday_end'] ?? '16:00'
            ],
            3 => [
                'enabled' => !empty($validated['wednesday_start']) && !empty($validated['wednesday_end']),
                'start' => $validated['wednesday_start'] ?? '07:00',
                'end' => $validated['wednesday_end'] ?? '16:00'
            ],
            4 => [
                'enabled' => !empty($validated['thursday_start']) && !empty($validated['thursday_end']),
                'start' => $validated['thursday_start'] ?? '07:00',
                'end' => $validated['thursday_end'] ?? '16:00'
            ],
            5 => [
                'enabled' => !empty($validated['friday_start']) && !empty($validated['friday_end']),
                'start' => $validated['friday_start'] ?? '07:00',
                'end' => $validated['friday_end'] ?? '16:00'
            ],
            6 => [
                'enabled' => !empty($validated['saturday_start']) && !empty($validated['saturday_end']),
                'start' => $validated['saturday_start'] ?? '07:00',
                'end' => $validated['saturday_end'] ?? '17:00'
            ],
        ];
    }
}

    /**
     * Create 15-minute slots for a specific date and time range
     */
    private function createSlotsForDate($instructor, Carbon $date, $startTime, $endTime)
    {
        $createdCount = 0;
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        
        while ($start->lt($end)) {
            $slotEnd = $start->copy()->addMinutes(15);
            
            // Don't create a slot that would exceed the end time
            if ($slotEnd->gt($end)) {
                break;
            }
            
            // Check if this exact slot already exists
            $existingSlot = Availability::where('instructor_id', $instructor->id)
                ->where('date', $date->format('Y-m-d'))
                ->where('start_time', $start->format('H:i:s'))
                ->where('end_time', $slotEnd->format('H:i:s'))
                ->first();
            
            if (!$existingSlot) {
                Availability::create([
                    'instructor_id' => $instructor->id,
                    'date' => $date->format('Y-m-d'),
                    'start_time' => $start->format('H:i:s'),
                    'end_time' => $slotEnd->format('H:i:s'),
                    'is_available' => true
                ]);
                $createdCount++;
            }
            
            $start = $slotEnd;
        }
        
        return $createdCount;
    }

    /**
     * Delete an availability slot
     */
    public function destroy(Availability $availability)
    {
        if (!$this->canModifyAvailability($availability)) {
            abort(403);
        }

        if ($availability->isBooked()) {
            return back()->with('error', 'Cannot delete availability with existing bookings');
        }

        $availability->delete();
        return back()->with('success', 'Availability slot deleted successfully');
    }

    /**
     * Bulk delete availability slots
     */
    public function bulkDelete(Request $request)
    {
        $instructor = Auth::user()->instructor;

        $validated = $request->validate([
            'date' => 'required|date',
            'delete_all_future' => 'nullable|boolean',
        ]);

        try {
            $query = $this->buildBulkDeleteQuery($instructor->id, $validated);

            if ($this->hasBookings($query)) {
                return back()->with('error', 'Cannot delete slots with existing bookings');
            }

            $count = $query->count();
            $query->delete();

            return back()->with('success', "{$count} availability slots deleted successfully");

        } catch (\Exception $e) {
            Log::error('Error deleting availability: ' . $e->getMessage());
            return back()->with('error', 'Error deleting availability slots');
        }
    }

    // --- Private helper methods ---

    private function getViewMonth(Request $request, Carbon $today)
    {
        if (!$request->has('month')) {
            return $today->copy();
        }

        try {
            return Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();
        } catch (\Exception $e) {
            Log::warning('Invalid month format provided: ' . $request->month);
            return $today->copy();
        }
    }

    private function getCalendarDates(Carbon $viewMonth)
    {
        $startDate = $viewMonth->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $endDate = $viewMonth->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        $weeks = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $week = [];
            for ($i = 0; $i < 7 && $currentDate->lte($endDate); $i++) {
                $week[] = $currentDate->copy();
                $currentDate->addDay();
            }
            $weeks[] = $week;
        }

        return compact('startDate', 'endDate', 'weeks');
    }

    private function getAvailabilities($instructorId, Carbon $startDate, Carbon $endDate)
    {
        return Availability::where('instructor_id', $instructorId)
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
    }

    private function calculateDateRange(array $validated)
    {
        $startDate = Carbon::parse($validated['date']);
        $endDate = isset($validated['recur_until']) ? Carbon::parse($validated['recur_until']) : $startDate;

        return compact('startDate', 'endDate');
    }

    private function getDaysOfWeek(Request $request, array $validated, Carbon $startDate)
    {
        return $request->has('is_recurring') && isset($validated['days_of_week'])
            ? array_map('intval', $validated['days_of_week'])
            : [(int)$startDate->dayOfWeek];
    }

    private function canModifyAvailability(Availability $availability)
    {
        return $availability->instructor_id === Auth::user()->instructor->id;
    }

    private function buildBulkDeleteQuery($instructorId, array $validated)
    {
        $query = Availability::where('instructor_id', $instructorId);

        if (!empty($validated['delete_all_future'])) {
            $query->whereDate('date', '>=', $validated['date']);
        } else {
            $query->whereDate('date', $validated['date']);
        }

        return $query;
    }

    private function hasBookings($query)
    {
        return $query->get()->some(function($availability) {
            return $availability->isBooked();
        });
    }
}