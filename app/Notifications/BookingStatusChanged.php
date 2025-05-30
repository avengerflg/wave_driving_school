<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;
    protected $oldStatus;
    protected $notes;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking, $oldStatus, $notes = null)
    {
        $this->booking = $booking;
        $this->oldStatus = $oldStatus;
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
        $statusVerb = match($this->booking->status) {
            'confirmed' => 'confirmed',
            'completed' => 'marked as completed',
            'cancelled' => 'cancelled',
            default => 'updated'
        };
        
        $statusColor = match($this->booking->status) {
            'confirmed' => 'success',
            'completed' => 'info',
            'cancelled' => 'danger',
            default => 'primary'
        };
        
        return (new MailMessage)
            ->subject('Your Booking with Wave Driving School has been ' . ucfirst($statusVerb))
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your booking with Wave Driving School has been ' . $statusVerb . '.')
            ->line('**Service:** ' . $this->booking->service->name)
            ->line('**Date & Time:** ' . $this->booking->date->format('l, F j, Y') . ' at ' . $this->booking->start_time->format('g:i A'))
            ->line('**Location:** ' . $this->booking->suburb->name)
            ->line('**Instructor:** ' . $this->booking->instructor->user->name)
            ->when($this->notes, function ($message) {
                return $message->line('**Note from instructor:** ' . $this->notes);
            })
            ->when($this->booking->status === 'cancelled', function ($message) {
                return $message->line('If you have any questions about this cancellation, please contact us.');
            })
            ->when($this->booking->status === 'confirmed', function ($message) {
                return $message->line('We look forward to seeing you for your lesson!');
            })
            ->when($this->booking->status === 'completed', function ($message) {
                return $message->line('Thank you for completing your lesson with us. We hope it was valuable!');
            })
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
        $statusVerb = match($this->booking->status) {
            'confirmed' => 'confirmed',
            'completed' => 'marked as completed',
            'cancelled' => 'cancelled',
            default => 'updated'
        };
        
        $statusIcon = match($this->booking->status) {
            'confirmed' => 'bx-check',
            'completed' => 'bx-check-double',
            'cancelled' => 'bx-x',
            default => 'bx-info-circle'
        };
        
        $statusColor = match($this->booking->status) {
            'confirmed' => 'success',
            'completed' => 'info',
            'cancelled' => 'danger',
            default => 'primary'
        };
        
        return [
            'booking_id' => $this->booking->id,
            'title' => 'Booking ' . ucfirst($statusVerb),
            'message' => 'Your booking for ' . $this->booking->service->name . ' has been ' . $statusVerb . '.',
            'date_time' => $this->booking->date->format('M d, Y') . ' ' . $this->booking->start_time->format('g:i A'),
            'instructor_name' => $this->booking->instructor->user->name,
            'old_status' => $this->oldStatus,
            'new_status' => $this->booking->status,
            'notes' => $this->notes,
            'icon' => $statusIcon,
            'color' => $statusColor,
            'link' => '/client/bookings/' . $this->booking->id
        ];
    }
}