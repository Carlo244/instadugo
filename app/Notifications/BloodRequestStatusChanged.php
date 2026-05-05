<?php

namespace App\Notifications;

use App\Models\BloodRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BloodRequestStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        private readonly BloodRequest $bloodRequest,
        private readonly string $title,
        private readonly string $message
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject($this->title)
            ->view('emails.blood_request_status_changed', [
                'notifiable' => $notifiable,
                'bloodRequest' => $this->bloodRequest,
                'message' => $this->message,
                'title' => $this->title,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'link' => route('user.blood-requests'),
            'priority' => 'normal',
        ];
    }
}
