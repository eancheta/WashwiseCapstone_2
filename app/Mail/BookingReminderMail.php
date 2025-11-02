<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

public function build()
{
    return $this->subject('Booking Reminder')
        ->view('emails.booking_reminder')
        ->with([
            'customer_name' => $this->booking->name,
            'service_name' => $this->booking->size_of_the_car . ' Wash',
            'date_time' => $this->booking->date_of_booking . ' ' . $this->booking->time_of_booking,
            'car_wash_name' => $this->booking->car_wash_name ?? 'WashWise',
            'car_wash_address' => $this->booking->car_wash_address ?? 'Not specified',
        ]);
}
}
