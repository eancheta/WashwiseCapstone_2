<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentDeclined extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function build()
    {
        return $this->subject('Your Car Wash Appointment Has Been Declined')
                    ->view('emails.appointment_declined')
                    ->with([
                        'customer_name' => $this->details['customer_name'],
                        'service_name' => $this->details['service_name'],
                        'date_time' => $this->details['date_time'],
                        'car_wash_name' => $this->details['car_wash_name'],
                        'car_wash_address' => $this->details['car_wash_address'],
                        'decline_reason' => $this->details['reason'],
                    ]);
    }
}

