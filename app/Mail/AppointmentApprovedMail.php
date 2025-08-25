<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;

    /**
     * Create a new message instance.
     *
     * @param  object|array  $appointment
     * @return void
     */
    public function __construct($appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Your Car Wash Appointment is Confirmed – WashWise';

        return $this->subject($subject)
                    ->view('emails.appointment_approved')
                    ->with([
                        'appointment' => $this->appointment,
                    ]);
    }
}
