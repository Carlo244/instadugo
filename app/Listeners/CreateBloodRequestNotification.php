<?php

namespace App\Listeners;

use App\Events\BloodRequestCreated;
use App\Models\Notification;

class CreateBloodRequestNotification
{
    public function handle(BloodRequestCreated $event)
    {
        try {
            $hospitalAdmin = $event->bloodRequest->hospitalAdmin;
            
            if (!$hospitalAdmin) {
                return;
            }

            Notification::create([
                'type' => 'blood_request_created',
                'notifiable_id' => $hospitalAdmin->id,
                'notifiable_type' => get_class($hospitalAdmin),
                'data' => [
                    'blood_type' => $event->bloodRequest->blood_type,
                    'quantity' => $event->bloodRequest->quantity,
                    'urgency' => $event->bloodRequest->urgency,
                    'user_name' => $event->bloodRequest->user?->name,
                    'request_id' => $event->bloodRequest->id,
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
