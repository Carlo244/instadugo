<?php

namespace App\Notifications;

use App\Models\BloodRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BloodReadyForPickup extends Notification
{
    use Queueable;

    protected BloodRequest $bloodRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(BloodRequest $bloodRequest)
    {
        $this->bloodRequest = $bloodRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Blood Request Fulfilled - Ready for Pickup')
            ->view('emails.blood_ready', [
                'user' => $notifiable,
                'request' => $this->bloodRequest,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Blood is ready for pickup',
            'message' => 'Your request for ' . $this->bloodRequest->blood_type . ' has been fulfilled.',
            'link' => route('user.blood-requests'),
            'priority' => 'urgent',
        ];
    }
}
