<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $url; // Verification URL

    /**
     * Create a new message instance.
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Verify Your Email - InstaDugo')
                    ->markdown('emails.verify')
                    ->with([
                        'actionUrl' => $this->url,
                        'actionText' => 'Verify My Email',
                    ]);
    }
}
