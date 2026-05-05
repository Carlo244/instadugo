<?php

namespace App\Notifications;

use App\Models\BloodRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BloodRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $bloodRequest;

    public function __construct(BloodRequest $bloodRequest)
    {
        $this->bloodRequest = $bloodRequest;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database']; // Added database so it shows in their user dashboard too
    }

    public function toMail(object $notifiable): MailMessage
    {
        $hospitalName = $this->bloodRequest->hospitalAdmin?->hospital_name ?? 'Our Partner Hospital';

        return (new MailMessage)
            ->subject('Urgent: Blood Donation Request - ' . $this->bloodRequest->blood_type)
            // This points to the custom blade file we are about to create
            ->view('emails.blood_notification', [
                'donorName' => $notifiable->name,
                'request' => $this->bloodRequest,
                'hospital' => $hospitalName,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $hospitalName = $this->bloodRequest->hospitalAdmin?->hospital_name ?? 'Our Partner Hospital';

        return [
            'message' => "Urgent request for {$this->bloodRequest->blood_type} at {$hospitalName}",
            'request_id' => $this->bloodRequest->id,
        ];
    }
}