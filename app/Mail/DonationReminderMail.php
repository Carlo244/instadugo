<?php

namespace App\Mail;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DonationReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Donation $donation, public string $reminderLabel = 'Reminder')
    {
        $this->onQueue('emails');
    }

    public function build()
    {
        $subject = $this->reminderLabel . ': Upcoming Donation — ' . $this->donation->donation_date . ' ' . $this->donation->donation_time;

        return $this->subject($subject)
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->view('emails.donation_reminder')
                    ->bcc('instadugo@gmail.com')
                    ->with([
                        'donation' => $this->donation,
                        'reminderLabel' => $this->reminderLabel,
                    ]);
    }
}
