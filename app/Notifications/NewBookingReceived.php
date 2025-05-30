<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;
use Carbon\Carbon;

class NewBookingReceived extends Notification implements ShouldQueue
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
        $studentName = $this->booking->booking_for === 'other' 
            ? $this->booking->other_name 
            : $this->booking->user->name;

        return (new MailMessage)
            ->subject('New Booking Request - Lesson #' . $this->booking->id)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You have received a new booking request that requires your attention.')
            ->line('**Booking Details:**')
            ->line('Booking ID: #' . $this->booking->id)
            ->line('Student: ' . $studentName)
            ->line('Service: ' . $this->booking->service->name)
            ->line('Date: ' . Carbon::parse($this->booking->date)->format('l, F j, Y'))
            ->line('Time: ' . Carbon::parse($this->booking->start_time)->format('g:i A') . ' - ' . Carbon::parse($this->booking->end_time)->format('g:i A'))
            ->line('Location: ' . $this->booking->suburb->name)
            ->line('Address: ' . $this->booking->address)
            ->when($this->booking->booking_for === 'other', function ($mail) {
                return $mail->line('Booked by: ' . $this->booking->user->name)
                           ->line('Student Email: ' . $this->booking->other_email)
                           ->line('Student Phone: ' . $this->booking->other_phone);
            }, function ($mail) {
                return $mail->line('Student Email: ' . $this->booking->user->email)
                           ->line('Student Phone: ' . $this->booking->user->phone);
            })
            ->when($this->booking->notes, function ($mail) {
                return $mail->line('Notes: ' . $this->booking->notes);
            })
            ->line('**Payment Information:**')
            ->line('Amount: $' . number_format($this->booking->service->price, 2))
            ->line('Payment Status: ' . ucfirst($this->booking->payment->status ?? 'Pending'))
            ->action('Review & Confirm Booking', route('instructor.bookings.show', $this->booking->id))
            ->line('Please review this booking and confirm or decline it as soon as possible.')
            ->line('The student will be notified of your decision automatically.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        $studentName = $this->booking->booking_for === 'other' 
            ? $this->booking->other_name 
            : $this->booking->user->name;

        return [
            'title' => 'New Booking Request',
            'message' => 'New lesson booking from ' . $studentName . ' on ' . Carbon::parse($this->booking->date)->format('M j, Y') . ' at ' . Carbon::parse($this->booking->start_time)->format('g:i A'),
            'booking_id' => $this->booking->id,
            'student_name' => $studentName,
            'icon' => 'bx-calendar-plus',
            'color' => 'info',
            'action_url' => route('instructor.bookings.show', $this->booking->id),
            'action_text' => 'Review Booking'
        ];
    }
}
