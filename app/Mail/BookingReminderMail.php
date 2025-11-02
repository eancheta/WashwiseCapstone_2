<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData; // declared property

    public function __construct($data)
    {
        // If $data is an object (Eloquent model)
        if (is_object($data)) {
            $this->emailData = [
                'customer_name'   => $data->name ?? null,
                'service_name'    => (isset($data->size_of_the_car) ? $data->size_of_the_car . ' Wash' : ($data->service_name ?? null)),
                'date_time'       => ( ($data->date_of_booking ?? null) . ' ' . ($data->time_of_booking ?? '') ),
                'car_wash_name'   => $data->shop_name ?? ($data->car_wash_name ?? 'WashWise'),
                'car_wash_address'=> $data->shop_address ?? ($data->car_wash_address ?? 'Not specified'),
            ];
        }
        // If $data is an array (manual test)
        elseif (is_array($data)) {
            $this->emailData = $data;
        } else {
            // fallback to avoid warnings
            $this->emailData = [
                'customer_name' => null,
                'service_name' => null,
                'date_time' => null,
                'car_wash_name' => 'WashWise',
                'car_wash_address' => 'Not specified',
            ];
        }
    }

    public function build()
    {
        return $this->subject('WashWise Booking Reminder')
                    ->view('emails.booking_reminder')
                    ->with($this->emailData);
    }
}
