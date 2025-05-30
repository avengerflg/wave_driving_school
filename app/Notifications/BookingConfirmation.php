<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;
use Carbon\Carbon;

class BookingConfirmation extends Notification implements ShouldQueue
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
        $subject = $this->booking->status === 'confirmed' 
            ? 'Booking Confirmed - Driving Lesson #' . $this->booking->id
            : 'Booking Received - Driving Lesson #' . $this->booking->id;

        $greeting = $this->booking->status === 'confirmed' 
            ? 'Great news! Your driving lesson has been confirmed.'
            : 'Thank you for booking your driving lesson with us.';

        $statusMessage = $this->booking->status === 'confirmed'
            ? 'Your lesson is confirmed and ready to go!'
            : 'Your booking is currently pending confirmation. We\'ll notify you once it\'s confirmed by your instructor.';

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($greeting)
            ->line($statusMessage)
            ->line('**Booking Details:**')
            ->line('Booking ID: #' . $this->booking->id)
            ->line('Service: ' . $this->booking->service->name)
            ->line('Date: ' . Carbon::parse($this->booking->date)->format('l, F j, Y'))
            ->line('Time: ' . Carbon::parse($this->booking->start_time)->format('g:i A') . ' - ' . Carbon::parse($this->booking->end_time)->format('g:i A'))
            ->line('Location: ' . $this->booking->suburb->name)
            ->line('Address: ' . $this->booking->address)
            ->when($this->booking->instructor, function ($mail) {
                return $mail->line('Instructor: ' . $this->booking->instructor->user->name)
                           ->line('Instructor Phone: ' . $this->booking->instructor->phone);
            })
            ->when($this->booking->booking_for === 'other', function ($mail) {
                return $mail->line('Student: ' . $this->booking->other_name);
            })
            ->when($this->booking->notes, function ($mail) {
                return $mail->line('Notes: ' . $this->booking->notes);
            })
            ->action('View Booking Details', route('student.bookings.show', $this->booking->id))
            ->line('If you need to reschedule or have any questions, please contact us or use the link above.')
            ->line('Thank you for choosing our driving school!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'title' => $this->booking->status === 'confirmed' ? 'Booking Confirmed' : 'Booking Received',
            'message' => $this->booking->status === 'confirmed' 
                ? 'Your driving lesson on ' . Carbon::parse($this->booking->date)->format('M j, Y') . ' has been confirmed.'
                : 'Your booking request for ' . Carbon::parse($this->booking->date)->format('M j, Y') . ' is pending confirmation.',
            'booking_id' => $this->booking->id,
            'icon' => $this->booking->status === 'confirmed' ? 'bx-check-circle' : 'bx-clock',
            'color' => $this->booking->status === 'confirmed' ? 'success' : 'warning',
            'action_url' => route('student.bookings.show', $this->booking->id),
            'action_text' => 'View Booking'
        ];
    }
}
