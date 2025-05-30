<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class BookingReminderOneDay extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $date = Carbon::parse($this->booking->date)->format('l, F j, Y');
        $startTime = Carbon::parse($this->booking->start_time)->format('g:i A');
        $endTime = Carbon::parse($this->booking->end_time)->format('g:i A');
        
        return (new MailMessage)
            ->subject('Your Driving Lesson Tomorrow - Wave Driving School')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('This is a reminder that you have a driving lesson scheduled for tomorrow.')
            ->line('**Service:** ' . $this->booking->service->name)
            ->line('**Date:** ' . $date)
            ->line('**Time:** ' . $startTime . ' - ' . $endTime)
            ->line('**Location:** ' . $this->booking->suburb->name)
            ->line('**Instructor:** ' . $this->booking->instructor->user->name)
            ->line('**Instructor\'s Phone:** ' . ($this->booking->instructor->phone ?? 'Contact via app'))
            ->line('Please remember to:')
            ->line('- Be ready 5 minutes before your lesson time')
            ->line('- Bring your learner\'s permit/license')
            ->line('- Wear comfortable clothing and appropriate shoes for driving')
            ->line('- Note your pickup location if different from the address on file')
            ->action('View Booking Details', url('/client/bookings/' . $this->booking->id))
            ->line('We look forward to seeing you tomorrow!')
            ->line('Thank you for choosing Wave Driving School.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'title' => 'Lesson Reminder - Tomorrow',
            'message' => 'Your driving lesson is scheduled for tomorrow.',
            'date_time' => Carbon::parse($this->booking->date)->format('M d, Y') . ' at ' . Carbon::parse($this->booking->start_time)->format('g:i A'),
            'instructor_name' => $this->booking->instructor->user->name,
            'icon' => 'bx-calendar-exclamation',
            'color' => 'warning',
            'link' => '/client/bookings/' . $this->booking->id
        ];
    }
}