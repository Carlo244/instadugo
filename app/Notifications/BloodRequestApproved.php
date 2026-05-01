<?php

namespace App\Notifications;

use App\Models\BloodRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BloodRequestApproved extends Notification
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
            ->subject('Blood Request Approved')
            ->view('emails.blood_approved', [
                'user' => $notifiable,
                'request' => $this->bloodRequest,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Request Approved!',
            'message' => 'Your blood request for ' . $this->bloodRequest->blood_type . ' has been approved.',
            'link' => route('user.blood-requests'), // This matches the href in the modal.
            'priority' => 'urgent',
        ];
    }
}
