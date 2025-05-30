<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class BookingRescheduled extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;
    protected $oldDate;
    protected $oldStartTime;
    protected $notes;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking, $oldDate, $oldStartTime, $notes = null)
    {
        $this->booking = $booking;
        $this->oldDate = $oldDate;
        $this->oldStartTime = $oldStartTime;
        $this->notes = $notes;
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
        $oldDateTime = Carbon::parse($this->oldDate . ' ' . $this->oldStartTime);
        $newDateTime = Carbon::parse($this->booking->date . ' ' . $this->booking->start_time);
        
        return (new MailMessage)
            ->subject('Your Booking with Wave Driving School has been Rescheduled')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your booking with Wave Driving School has been rescheduled.')
            ->line('**Service:** ' . $this->booking->service->name)
            ->line('**Original Date & Time:** ' . $oldDateTime->format('l, F j, Y \a\t g:i A'))
            ->line('**New Date & Time:** ' . $newDateTime->format('l, F j, Y \a\t g:i A'))
            ->line('**Location:** ' . $this->booking->suburb->name)
            ->line('**Instructor:** ' . $this->booking->instructor->user->name)
            ->when($this->notes, function ($message) {
                return $message->line('**Note from instructor:** ' . $this->notes);
            })
            ->line('Please contact us if you have any questions or if this new time does not work for you.')
            ->action('View Booking Details', url('/client/bookings/' . $this->booking->id))
            ->line('Thank you for choosing Wave Driving School!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $oldDateTime = Carbon::parse($this->oldDate . ' ' . $this->oldStartTime);
        $newDateTime = Carbon::parse($this->booking->date . ' ' . $this->booking->start_time);
        
        return [
            'booking_id' => $this->booking->id,
            'title' => 'Booking Rescheduled',
            'message' => 'Your booking for ' . $this->booking->service->name . ' has been rescheduled.',
            'old_date_time' => $oldDateTime->format('M d, Y g:i A'),
            'new_date_time' => $newDateTime->format('M d, Y g:i A'),
            'instructor_name' => $this->booking->instructor->user->name,
            'notes' => $this->notes,
            'icon' => 'bx-calendar-edit',
            'color' => 'warning',
            'link' => '/client/bookings/' . $this->booking->id
        ];
    }
}