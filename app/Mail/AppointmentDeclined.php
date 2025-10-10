<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentDeclined extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;

    /**
     * Create a new message instance.
     */
    public function __construct($appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        \Log::info('Building AppointmentDeclined email', ['appointment' => $this->appointment]);

        return $this->subject('Your Appointment Has Been Declined')
            ->view('emails.appointment_declined')
            ->with([
                'appointment' => $this->appointment,
            ]);
    }
}
