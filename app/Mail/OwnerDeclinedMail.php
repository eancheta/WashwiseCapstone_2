<?php

namespace App\Mail;

use App\Models\CarWashOwner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OwnerDeclinedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $owner;

    /**
     * Create a new message instance.
     */
    public function __construct(CarWashOwner $owner)
    {
        $this->owner = $owner;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Account Declined - WashWise')
                    ->markdown('emails.declined')
                    ->with([
                        'name' => $this->owner->name,
                    ]);
    }
}
