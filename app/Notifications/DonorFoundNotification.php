<?php

namespace App\Notifications;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DonorFoundNotification extends Notification
{
    use Queueable;

    public $donation;

    // We pass the Donation model here so we can access date/time/donor name
    public function __construct(Donation $donation)
    {
        $this->donation = $donation;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $hospitalName = $this->donation->hospitalAdmin?->hospital_name ?? 'the hospital';
        $hospitalAddress = $this->donation->hospitalAdmin?->address;

        return (new MailMessage)
            ->subject('UPDATE: Donor found for your blood request')
            ->view('emails.respond_request', [
                'donorName'    => $this->donation->user->name,
                'donationDate' => $this->donation->donation_date,
                'donationTime' => $this->donation->donation_time,
                'hospital'     => $hospitalName,
                'hospitalAddress' => $hospitalAddress,
            ]);
    }
}
