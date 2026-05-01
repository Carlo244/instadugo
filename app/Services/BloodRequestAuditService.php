<?php

namespace App\Services;

use App\Models\BloodRequest;
use App\Models\BloodRequestActionAudit;

class BloodRequestAuditService
{
    public function logStatusChange(BloodRequest $request, string $action, ?string $fromStatus, ?string $toStatus, array $meta = []): void
    {
        BloodRequestActionAudit::create([
            'blood_request_id' => $request->id,
            'hospital_admin_id' => auth('hospital_admin')->id(),
            'action' => $action,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'meta' => $meta,
        ]);
    }

    public function logPriorityChange(BloodRequest $request, ?string $fromUrgency, ?string $toUrgency, array $meta = []): void
    {
        BloodRequestActionAudit::create([
            'blood_request_id' => $request->id,
            'hospital_admin_id' => auth('hospital_admin')->id(),
            'action' => 'priority_update',
            'from_urgency' => $fromUrgency,
            'to_urgency' => $toUrgency,
            'meta' => $meta,
        ]);
    }
}
