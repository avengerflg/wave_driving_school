<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;
use Carbon\Carbon;

class NewBookingAdmin extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Booking Created - #' . $this->booking->id)
            ->greeting('Hello Admin!')
            ->line('A new booking has been created in the system.')
            ->line('**Booking Details:**')
            ->line('Booking ID: #' . $this->booking->id)
            ->line('Student: ' . $this->booking->user->name)
            ->line('Instructor: ' . $this->booking->instructor->user->name)
            ->line('Service: ' . $this->booking->service->name)
            ->line('Date: ' . Carbon::parse($this->booking->date)->format('l, F j, Y'))
            ->line('Time: ' . Carbon::parse($this->booking->start_time)->format('g:i A') . ' - ' . Carbon::parse($this->booking->end_time)->format('g:i A'))
            ->line('Amount: $' . number_format($this->booking->service->price, 2))
            ->action('View in Admin Panel', route('admin.bookings.show', $this->booking->id))
            ->line('This booking is currently pending instructor confirmation.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'title' => 'New Booking Created',
            'message' => 'Booking #' . $this->booking->id . ' created by ' . $this->booking->user->name,
            'booking_id' => $this->booking->id,
            'icon' => 'bx-calendar-event',
            'color' => 'primary',
            'action_url' => route('admin.bookings.show', $this->booking->id),
            'action_text' => 'View Details'
        ];
    }
}
