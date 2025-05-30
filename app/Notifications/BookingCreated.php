<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCreated extends Notification implements ShouldQueue
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
        $isInstructor = $notifiable->id === $this->booking->instructor->user_id;
        
        $message = (new MailMessage)
            ->subject($isInstructor ? 'New Booking Request' : 'Your Booking Confirmation - Wave Driving School');
        
        if ($isInstructor) {
            // Email to instructor
            $message->greeting('Hello ' . $notifiable->name . ',')
                ->line('You have received a new booking request.')
                ->line('**Student:** ' . $this->booking->user->name)
                ->line('**Service:** ' . $this->booking->service->name)
                ->line('**Date & Time:** ' . $this->booking->date->format('l, F j, Y') . ' at ' . $this->booking->start_time->format('g:i A'))
                ->line('**Location:** ' . $this->booking->suburb->name)
                ->action('View Booking Details', url('/instructor/bookings/' . $this->booking->id))
                ->line('Please confirm this booking at your earliest convenience.');
        } else {
            // Email to student
            $message->greeting('Hello ' . $notifiable->name . ',')
                ->line('Thank you for booking with Wave Driving School!')
                ->line('Your booking details are:')
                ->line('**Service:** ' . $this->booking->service->name)
                ->line('**Date & Time:** ' . $this->booking->date->format('l, F j, Y') . ' at ' . $this->booking->start_time->format('g:i A'))
                ->line('**Location:** ' . $this->booking->suburb->name)
                ->line('**Instructor:** ' . $this->booking->instructor->user->name)
                ->line('**Status:** ' . ucfirst($this->booking->status))
                ->line('Please be ready 5 minutes before your scheduled time.')
                ->action('View Booking Details', url('/client/bookings/' . $this->booking->id))
                ->line('If you need to make any changes to your booking, please do so at least 24 hours in advance.');
        }
        
        return $message->line('Thank you for choosing Wave Driving School!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $isInstructor = $notifiable->id === $this->booking->instructor->user_id;
        
        if ($isInstructor) {
            return [
                'booking_id' => $this->booking->id,
                'title' => 'New Booking Request',
                'message' => 'You have received a new booking from ' . $this->booking->user->name,
                'date_time' => $this->booking->date->format('M d, Y') . ' ' . $this->booking->start_time->format('g:i A'),
                'student_name' => $this->booking->user->name,
                'service_name' => $this->booking->service->name,
                'icon' => 'bx-calendar-plus',
                'color' => 'info',
                'link' => '/instructor/bookings/' . $this->booking->id
            ];
        } else {
            return [
                'booking_id' => $this->booking->id,
                'title' => 'Booking Confirmed',
                'message' => 'Your booking for ' . $this->booking->service->name . ' has been created.',
                'date_time' => $this->booking->date->format('M d, Y') . ' ' . $this->booking->start_time->format('g:i A'),
                'instructor_name' => $this->booking->instructor->user->name,
                'service_name' => $this->booking->service->name,
                'icon' => 'bx-calendar-check',
                'color' => 'primary',
                'link' => '/client/bookings/' . $this->booking->id
            ];
        }
    }
}