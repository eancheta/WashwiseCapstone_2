<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OwnerVerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $name;

    public function __construct(string $code, string $name = '')
    {
        $this->code = $code;
        $this->name = $name;
    }

    public function build()
    {
        return $this->subject('Your Owner Verification Code')
                    ->view('emails.owner-verification-code')
                    ->with([
                        'code' => $this->code,
                        'name' => $this->name,
                    ]);
    }
}
