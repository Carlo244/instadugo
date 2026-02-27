<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;

class DonorRequestNotification extends Notification
{
    use Queueable;

    protected User $sender;
    protected array $requestData;

    /**
     * Create a new notification instance.
     *
     * @param User $sender
     * @param array $requestData
     */
    public function __construct(User $sender, array $requestData)
    {
        $this->sender = $sender;
        $this->requestData = $requestData;
    }

    /**
     * Get the notification's delivery channels.
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
        return (new MailMessage)
            ->subject('URGENT: Blood Request from ' . $this->sender->name)
            ->view('emails.send_request', [
                'donor'           => $notifiable,
                'sender'          => $this->sender,
                'urgency'         => $this->requestData['urgency'] ?? 'Normal',
                'personalMessage' => $this->requestData['message'] ?? 'No additional message provided.',
            ]);
    }

    /**
     * Get the array representation of the notification for database.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Blood Request',
            'message' => "{$this->sender->name} sent a {$this->requestData['urgency']} request.",
            'link' => route('user.dashboard'),
            'sender_id' => $this->sender->id,
            'urgency' => $this->requestData['urgency'] ?? 'Normal',
        ];
    }
}