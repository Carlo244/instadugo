<?php

namespace App\Events;

use App\Models\BloodRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BloodRequestStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bloodRequest;
    public $fromStatus;
    public $toStatus;

    public function __construct(BloodRequest $bloodRequest, string $fromStatus)
    {
        $this->bloodRequest = $bloodRequest->loadMissing('user');
        $this->fromStatus = $fromStatus;
        $this->toStatus = (string) $bloodRequest->status;
    }

    public function broadcastOn(): Channel
    {
        $hospitalId = $this->bloodRequest->hospital_admin_id ?? $this->bloodRequest->hospitalAdmin?->id;
        return new PrivateChannel("blood-requests." . ($hospitalId ?? 'global'));
    }
}
