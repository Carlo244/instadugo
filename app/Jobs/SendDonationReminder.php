<?php

namespace App\Jobs;

use App\Models\Donation;
use App\Mail\DonationReminderMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendDonationReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $donationId, public string $reminderLabel = 'Reminder')
    {
        $this->onQueue('emails');
    }

    public function handle()
    {
        Log::info("[SendDonationReminder] Processing job for donation {$this->donationId}, label: {$this->reminderLabel}");

        $donation = Donation::with('user', 'hospitalAdmin')->find($this->donationId);
        if (! $donation) {
            Log::warning("[SendDonationReminder] Donation {$this->donationId} not found");
            return;
        }

        // Only send if still scheduled
        if ($donation->status !== 'scheduled') {
            Log::info("[SendDonationReminder] Donation {$this->donationId} status is {$donation->status}, skipping reminder");
            return;
        }

        $user = $donation->user;
        if (! $user || ! $user->email) {
            Log::warning("[SendDonationReminder] Donation {$this->donationId} has no user or email");
            return;
        }

        try {
            Log::info("[SendDonationReminder] Sending {$this->reminderLabel} to {$user->email} for donation {$this->donationId}");
            Mail::to($user->email)->send(new DonationReminderMail($donation, $this->reminderLabel));
            Log::info("[SendDonationReminder] Successfully sent {$this->reminderLabel} to {$user->email}");
        } catch (\Throwable $e) {
            Log::error("[SendDonationReminder] Failed to send reminder for donation {$this->donationId}", [
                'error' => $e->getMessage(),
                'email' => $user->email,
                'label' => $this->reminderLabel,
            ]);
            throw $e;
        }
    }
}
