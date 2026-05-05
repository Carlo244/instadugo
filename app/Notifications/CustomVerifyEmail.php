<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class CustomVerifyEmail extends VerifyEmail
{
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return new \Illuminate\Notifications\Messages\MailMessage()->subject('Confirm your InstaDugo email address')->view('emails.verify_email', [
            'user' => $notifiable,
            'url' => $verificationUrl,
        ]);
    }
}
