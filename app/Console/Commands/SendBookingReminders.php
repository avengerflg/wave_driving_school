<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Notifications\BookingReminderOneDay;
use App\Notifications\BookingReminderTwoDays;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendBookingReminders extends Command
{
    protected $signature = 'bookings:send-reminders';
    protected $description = 'Send reminders for upcoming bookings';

    public function handle()
    {
        $this->sendTwoDayReminders();
        $this->sendOneDayReminders();
        
        $this->info('Booking reminders sent successfully!');
        return Command::SUCCESS;
    }
    
    private function sendTwoDayReminders()
    {
        // Get bookings scheduled for 2 days from now
        $twoDaysFromNow = Carbon::now()->addDays(2)->format('Y-m-d');
        
        $bookings = Booking::with(['user', 'instructor.user', 'service', 'suburb'])
            ->whereDate('date', $twoDaysFromNow)
            ->whereIn('status', ['confirmed', 'pending'])
            ->where(function($query) {
                $query->where('two_day_reminder_sent', false)
                      ->orWhereNull('two_day_reminder_sent');
            })
            ->get();
            
        $this->info('Sending 2-day reminders for ' . $bookings->count() . ' bookings');
        $remindersSent = 0;
        
        foreach ($bookings as $booking) {
            if ($booking->user) {
                try {
                    $booking->user->notify(new BookingReminderTwoDays($booking));
                    
                    // Mark as sent
                    $booking->two_day_reminder_sent = true;
                    $booking->save();
                    
                    $remindersSent++;
                } catch (\Exception $e) {
                    Log::error('Failed to send 2-day reminder for booking #' . $booking->id . ': ' . $e->getMessage());
                }
            }
        }
        
        $this->info("Successfully sent {$remindersSent} two-day reminders");
    }
    
    private function sendOneDayReminders()
    {
        // Get bookings scheduled for tomorrow
        $tomorrow = Carbon::now()->addDay()->format('Y-m-d');
        
        $bookings = Booking::with(['user', 'instructor.user', 'service', 'suburb'])
            ->whereDate('date', $tomorrow)
            ->whereIn('status', ['confirmed', 'pending'])
            ->where(function($query) {
                $query->where('one_day_reminder_sent', false)
                      ->orWhereNull('one_day_reminder_sent');
            })
            ->get();
            
        $this->info('Sending 1-day reminders for ' . $bookings->count() . ' bookings');
        $remindersSent = 0;
        
        foreach ($bookings as $booking) {
            if ($booking->user) {
                try {
                    $booking->user->notify(new BookingReminderOneDay($booking));
                    
                    // Mark as sent
                    $booking->one_day_reminder_sent = true;
                    $booking->save();
                    
                    $remindersSent++;
                } catch (\Exception $e) {
                    Log::error('Failed to send 1-day reminder for booking #' . $booking->id . ': ' . $e->getMessage());
                }
            }
        }
        
        $this->info("Successfully sent {$remindersSent} one-day reminders");
    }
}