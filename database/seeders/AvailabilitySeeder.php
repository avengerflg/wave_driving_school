<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Availability;
use App\Models\Instructor;
use Carbon\Carbon;

class AvailabilitySeeder extends Seeder
{
    public function run()
    {
        $instructors = Instructor::where('active', true)->get();
        
        foreach ($instructors as $instructor) {
            // Create availability for the next 2 weeks
            for ($i = 0; $i < 14; $i++) {
                $date = Carbon::today()->addDays($i);
                
                // Skip weekends
                if ($date->isWeekend()) {
                    continue;
                }
                
                // Create slots from 9 AM to 5 PM
                $startTime = Carbon::parse($date->format('Y-m-d') . ' 09:00:00');
                while ($startTime->format('H') < 17) {
                    Availability::create([
                        'instructor_id' => $instructor->id,
                        'date' => $date,
                        'start_time' => $startTime->format('H:i:s'),
                        'end_time' => $startTime->addHour()->format('H:i:s'),
                        'is_available' => true
                    ]);
                }
            }
        }
    }
}
