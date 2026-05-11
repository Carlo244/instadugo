<?php

namespace App\Listeners;

use App\Events\DonationStatusUpdated;
use App\Models\Notification;

class CreateDonationStatusNotification
{
    public function handle(DonationStatusUpdated $event)
    {
        try {
            $hospitalAdmin = $event->donation->hospitalAdmin;
            
            if (!$hospitalAdmin) {
                return;
            }

            Notification::create([
                'type' => 'donation_status_updated',
                'notifiable_id' => $hospitalAdmin->id,
                'notifiable_type' => get_class($hospitalAdmin),
                'data' => [
                    'donor_name' => $event->donation->user?->name,
                    'from_status' => $event->fromStatus,
                    'to_status' => $event->toStatus,
                    'donation_id' => $event->donation->id,
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
