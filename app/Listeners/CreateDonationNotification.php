<?php

namespace App\Listeners;

use App\Events\DonationCreated;
use App\Models\Notification;

class CreateDonationNotification
{
    public function handle(DonationCreated $event)
    {
        try {
            $hospitalAdmin = $event->donation->hospitalAdmin;
            
            if (!$hospitalAdmin) {
                return;
            }

            Notification::create([
                'type' => 'donation_created',
                'notifiable_id' => $hospitalAdmin->id,
                'notifiable_type' => get_class($hospitalAdmin),
                'data' => [
                    'donor_name' => $event->donation->user?->name,
                    'donation_time' => $event->donation->donation_time,
                    'donation_id' => $event->donation->id,
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
