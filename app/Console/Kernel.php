<?php

namespace App\Console;

use App\Console\Commands\SendDonationReminders;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Send donation reminders daily at 9:00 AM
        $schedule->command(SendDonationReminders::class)
            ->dailyAt('09:00')
            ->description('Send donation appointment reminders');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
