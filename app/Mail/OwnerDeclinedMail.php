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
    public $reason;

    /**
     * Create a new message instance.
     */
    public function __construct(CarWashOwner $owner, $reason = null)
    {
        $this->owner = $owner;
        $this->reason = $reason;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Account Declined - WashWise')
                    ->view('emails.declined') // Use normal Blade since your template is HTML
                    ->with([
                        'owner' => $this->owner,
                        'reason' => $this->reason, // ✅ Pass the reason to the view
                    ]);
    }
}
