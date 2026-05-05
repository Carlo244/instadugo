<?php

namespace App\Notifications;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DonationReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Donation $donation)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $hospital = $this->donation->hospitalAdmin?->hospital_name ?? 'Our Partner Hospital';
        $donationDate = $this->donation->donation_date;
        $donationTime = $this->donation->donation_time;

        return (new MailMessage())
            ->subject('Reminder: Your Blood Donation Appointment Tomorrow at ' . $hospital)
            ->view('emails.donation_reminder', [
                'donorName' => $notifiable->name,
                'donation' => $this->donation,
                'hospital' => $hospital,
                'donationDate' => $donationDate,
                'donationTime' => $donationTime,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $hospital = $this->donation->hospitalAdmin?->hospital_name ?? 'Our Partner Hospital';

        return [
            'title' => 'Donation Appointment Reminder',
            'message' => "Your blood donation is scheduled tomorrow at {$hospital}",
            'donation_id' => $this->donation->id,
            'donation_time' => $this->donation->donation_time,
        ];
    }
}
