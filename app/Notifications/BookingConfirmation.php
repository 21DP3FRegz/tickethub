<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;
    protected $ticket;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking, Ticket $ticket)
    {
        $this->booking = $booking;
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $show = $this->ticket->show;
        $concert = $show->concert;
        $seat = $this->ticket->seat;
        $row = $seat->row;

        return (new MailMessage)
            ->subject('Your Concert Booking Confirmation')
            ->greeting('Hello ' . $this->booking->name . '!')
            ->line('Thank you for your booking. Here are your concert details:')
            ->line('Concert: ' . $concert->artist)
            ->line('Venue: ' . $concert->location->name)
            ->line('Date & Time: ' . $show->start->format('F j, Y, g:i a'))
            ->line('Seat: Row ' . $row->name . ', Seat ' . $seat->seat_number)
            ->line('Ticket Code: ' . $this->ticket->code)
            ->line('Please present this code or the QR code attached at the venue entrance.')
            ->action('View Booking Details', url(route('bookings.show', $this->booking->id)))
            ->line('We hope you enjoy the concert!')
            ->salutation('The Concert Ticketing Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'ticket_id' => $this->ticket->id,
            'concert' => $this->ticket->show->concert->artist,
            'show_date' => $this->ticket->show->start,
        ];
    }
}
