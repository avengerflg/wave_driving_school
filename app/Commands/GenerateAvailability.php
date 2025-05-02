<?php

namespace App\Commands;

use Illuminate\Console\Command;
use App\Models\Instructor;
use App\Models\Availability;
use Carbon\Carbon;

class GenerateAvailability extends Command
{
    protected $signature = 'generate:availability {instructor_id} {days=14}';
    protected $description = 'Generate availability slots for an instructor';

    public function handle()
    {
        try {
            $instructorId = $this->argument('instructor_id');
            $days = (int) $this->argument('days'); // Cast to integer here
            
            // Check if instructor exists
            $instructor = Instructor::query()
                ->where('id', $instructorId)
                ->first();

            if (!$instructor) {
                $this->error("No instructor found with ID: {$instructorId}");
                return 1;
            }

            $startDate = Carbon::today();
            $endDate = Carbon::today()->addDays($days);
            $current = $startDate->copy();
            
            $this->info("Generating availability slots for instructor: {$instructor->user->name}");
            $this->info("From: {$startDate->format('Y-m-d')} To: {$endDate->format('Y-m-d')}");
            
            $bar = $this->output->createProgressBar($days);
            $bar->start();
            
            while ($current <= $endDate) {
                if (!$current->isWeekend()) {
                    // Generate slots from 9 AM to 5 PM with 1-hour slots
                    for ($hour = 9; $hour < 17; $hour++) {
                        $this->createSlot(
                            $instructorId,
                            $current,
                            sprintf('%02d:00:00', $hour),
                            sprintf('%02d:00:00', $hour + 1)
                        );
                    }
                }
                
                $current->addDay();
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine();
            $this->info('Availability slots generated successfully!');
            
            return 0;
        } catch (\Exception $e) {
            $this->error("An error occurred: {$e->getMessage()}");
            return 1;
        }
    }
    
    private function createSlot($instructorId, $date, $startTime, $endTime)
    {
        return Availability::updateOrCreate(
            [
                'instructor_id' => $instructorId,
                'date' => $date->format('Y-m-d'),
                'start_time' => $startTime,
            ],
            [
                'end_time' => $endTime,
                'is_available' => true,
            ]
        );
    }
}
