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
    // ... inside DonorRequestNotification.php

    public function toMail(object $notifiable): MailMessage
    {
        return new MailMessage()->subject('URGENT: Blood Request from ' . $this->sender->name)->view('emails.send_request', [
            'donor' => $notifiable,
            'sender' => $this->sender,
            'urgency' => $this->requestData['urgency'] ?? 'Normal',
            'personalMessage' => $this->requestData['message'] ?? 'No additional message provided.',
            'hospital' => $this->requestData['hospital'] ?? 'N/A',
            // Passing the ID to the email view for buttons
            'requestId' => $this->requestData['request_id'],
        ]);
    }

public function toArray(object $notifiable): array
{
    return [
        'title' => 'New Blood Request',
        'message' => "{$this->sender->name} requested blood via {$this->requestData['hospital']}.",
        'link' => route('user.requests.show', $this->requestData['request_id']),
        'request_id' => $this->requestData['request_id'],
        'sender_id' => $this->sender->id,
        'urgency' => $this->requestData['urgency'] ?? 'Normal',
    ];
}
}
