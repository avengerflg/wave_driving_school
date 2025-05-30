<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class BookingReminderTwoDays extends Notification implements ShouldQueue
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
            ->subject('Your Driving Lesson in 2 Days - Wave Driving School')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('This is a friendly reminder that you have a driving lesson scheduled in 2 days.')
            ->line('**Service:** ' . $this->booking->service->name)
            ->line('**Date:** ' . $date)
            ->line('**Time:** ' . $startTime . ' - ' . $endTime)
            ->line('**Location:** ' . $this->booking->suburb->name)
            ->line('**Instructor:** ' . $this->booking->instructor->user->name)
            ->line('**Instructor\'s Phone:** ' . ($this->booking->instructor->phone ?? 'Contact via app'))
            ->line('If you need to cancel, please do so at least 24 hours in advance to avoid any cancellation fees.')
            ->action('View Booking Details', url('/client/bookings/' . $this->booking->id))
            ->line('We look forward to seeing you!')
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
            'title' => 'Lesson Reminder - 2 Days',
            'message' => 'Your driving lesson is scheduled in 2 days.',
            'date_time' => Carbon::parse($this->booking->date)->format('M d, Y') . ' at ' . Carbon::parse($this->booking->start_time)->format('g:i A'),
            'instructor_name' => $this->booking->instructor->user->name,
            'icon' => 'bx-calendar',
            'color' => 'info',
            'link' => '/client/bookings/' . $this->booking->id
        ];
    }
}