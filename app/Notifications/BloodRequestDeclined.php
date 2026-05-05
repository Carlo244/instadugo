<?php

namespace App\Notifications;

use App\Models\BloodRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BloodRequestDeclined extends Notification
{
    use Queueable;

    protected BloodRequest $bloodRequest;

    public function __construct(BloodRequest $bloodRequest)
    {
        $this->bloodRequest = $bloodRequest;
    }

    // This sends to both database and email.
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Blood Request Declined')
            ->view('emails.blood_declined', [
                'user' => $notifiable,
                'request' => $this->bloodRequest,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Request Declined',
            'message' => 'Your blood request for ' . $this->bloodRequest->blood_type . ' has been declined by the hospital.',
            'link' => route('user.blood-requests'),
            'priority' => 'high',
        ];
    }
}
