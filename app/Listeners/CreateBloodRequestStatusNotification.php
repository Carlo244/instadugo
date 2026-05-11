<?php

namespace App\Listeners;

use App\Events\BloodRequestStatusUpdated;
use App\Models\Notification;

class CreateBloodRequestStatusNotification
{
    public function handle(BloodRequestStatusUpdated $event)
    {
        try {
            $hospitalAdmin = $event->bloodRequest->hospitalAdmin;
            
            if (!$hospitalAdmin) {
                return;
            }

            Notification::create([
                'type' => 'blood_request_status_updated',
                'notifiable_id' => $hospitalAdmin->id,
                'notifiable_type' => get_class($hospitalAdmin),
                'data' => [
                    'blood_type' => $event->bloodRequest->blood_type,
                    'from_status' => $event->fromStatus,
                    'to_status' => $event->toStatus,
                    'request_id' => $event->bloodRequest->id,
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
