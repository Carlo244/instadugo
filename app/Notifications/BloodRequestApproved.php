<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\BloodRequest;

class BloodRequestApproved extends Notification
{
    use Queueable;

    protected $bloodRequest;

    public function __construct(BloodRequest $bloodRequest)
    {
        $this->bloodRequest = $bloodRequest;
    }

    // This sends to both Database and Email
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    // Email Setup
public function toMail($notifiable)
{
    return (new \Illuminate\Notifications\Messages\MailMessage)
        ->subject('Blood Request Approved')
        ->view('emails.blood_approved', [
            'user' => $notifiable,
            'request' => $this->bloodRequest
        ]);
}

    // Database Dashboard Setup
public function toArray($notifiable)
{
    return [
        'title' => 'Request Approved!',
        'message' => 'Your blood request for ' . $this->bloodRequest->blood_type . ' has been approved.',
        'link' => route('user.blood-requests'), // This matches the href in the modal
        'priority' => 'urgent' 
    ];
}
}
