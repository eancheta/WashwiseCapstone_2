<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OwnerApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $owner;

    /**
     * Create a new message instance.
     */
    public function __construct($owner)
    {
        $this->owner = $owner;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Car Wash Owner Account Has Been Approved')
            ->view('emails.owners.approved')
            ->with([
                'owner' => $this->owner,
            ]);
    }
}
