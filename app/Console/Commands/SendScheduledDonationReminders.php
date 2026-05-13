<?php

namespace App\Console\Commands;

use App\Jobs\SendDonationReminder;
use App\Models\Donation;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendScheduledDonationReminders extends Command
{
    protected $signature = 'donations:send-reminders';
    protected $description = 'Scan scheduled donations and send 24h and 2h email reminders in time windows.';

    public function handle()
    {
        $now = Carbon::now();

        // Windows +/- 15 minutes around target times
        $window = 15; // minutes

        // 24-hour reminder window
        $lower24 = $now->copy()->addMinutes(1440 - $window);
        $upper24 = $now->copy()->addMinutes(1440 + $window);

        // 2-hour reminder window
        $lower2 = $now->copy()->addMinutes(120 - $window);
        $upper2 = $now->copy()->addMinutes(120 + $window);

        $donations = Donation::where('status', 'scheduled')->get();

        foreach ($donations as $donation) {
            try {
                // Extract just the date part (handle both date and datetime formats)
                $dateOnly = Carbon::parse($donation->donation_date)->toDateString();
                $scheduled = Carbon::parse($dateOnly . ' ' . $donation->donation_time, config('app.timezone'));

                // 24h
                if (is_null($donation->reminder_24_sent_at) && $scheduled->between($lower24, $upper24)) {
                    SendDonationReminder::dispatch($donation->id, '24-hour reminder');
                    $donation->reminder_24_sent_at = $now;
                    $donation->save();
                    $this->info("Dispatched 24h reminder for donation {$donation->id}");
                    continue; // avoid double-processing in same loop
                }

                // 2h
                if (is_null($donation->reminder_2h_sent_at) && $scheduled->between($lower2, $upper2)) {
                    SendDonationReminder::dispatch($donation->id, '2-hour reminder');
                    $donation->reminder_2h_sent_at = $now;
                    $donation->save();
                    $this->info("Dispatched 2h reminder for donation {$donation->id}");
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return 0;
    }
}
