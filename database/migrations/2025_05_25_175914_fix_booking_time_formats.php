<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixBookingTimeFormats extends Migration
{
    public function up()
    {
        // Fix any bookings that have full datetime in time columns
        DB::table('bookings')->get()->each(function ($booking) {
            $startTime = $booking->start_time;
            $endTime = $booking->end_time;
            
            // Check if the time contains a date part
            if (strpos($startTime, '-') !== false) {
                // Extract just the time part
                $startTime = \Carbon\Carbon::parse($startTime)->format('H:i:s');
                $endTime = \Carbon\Carbon::parse($endTime)->format('H:i:s');
                
                DB::table('bookings')
                    ->where('id', $booking->id)
                    ->update([
                        'start_time' => $startTime,
                        'end_time' => $endTime
                    ]);
            }
        });

        // Fix any availabilities that have full datetime in time columns
        DB::table('availabilities')->get()->each(function ($slot) {
            $startTime = $slot->start_time;
            $endTime = $slot->end_time;
            
            // Check if the time contains a date part
            if (strpos($startTime, '-') !== false) {
                // Extract just the time part
                $startTime = \Carbon\Carbon::parse($startTime)->format('H:i:s');
                $endTime = \Carbon\Carbon::parse($endTime)->format('H:i:s');
                
                DB::table('availabilities')
                    ->where('id', $slot->id)
                    ->update([
                        'start_time' => $startTime,
                        'end_time' => $endTime
                    ]);
            }
        });
    }
    
    public function down()
    {
        // This migration is not reversible
    }
}