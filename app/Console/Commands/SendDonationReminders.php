<?php

namespace App\Console\Commands;

use App\Models\Donation;
use App\Notifications\DonationReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDonationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'donations:send-tomorrow-reminders {--date= : Send reminders for a specific date (YYYY-MM-DD), defaults to tomorrow}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Send reminder notifications for donations scheduled tomorrow';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetDate = $this->option('date')
            ? Carbon::createFromFormat('Y-m-d', $this->option('date'))
            : Carbon::tomorrow();

        $this->info("Sending donation reminders for {$targetDate->format('Y-m-d')}...");

        $donations = Donation::with('user')
            ->where('donation_date', $targetDate->toDateString())
            ->where('status', 'scheduled')
            ->get();

        if ($donations->isEmpty()) {
            $this->info('No scheduled donations found for ' . $targetDate->format('Y-m-d'));
            return Command::SUCCESS;
        }

        $sentCount = 0;
        foreach ($donations as $donation) {
            try {
                $donation->user->notify(new DonationReminderNotification($donation));
                $sentCount++;
                $this->info("Reminder sent to {$donation->user->email} for donation on {$donation->donation_date} at {$donation->donation_time}");
            } catch (\Throwable $e) {
                $this->error("Failed to send reminder for donation {$donation->id}: {$e->getMessage()}");
                report($e);
            }
        }

        $this->info("Successfully sent {$sentCount} donation reminders.");
        return Command::SUCCESS;
    }
}
