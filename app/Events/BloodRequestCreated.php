<?php

namespace App\Events;

use App\Models\BloodRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BloodRequestCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bloodRequest;

    public function __construct(BloodRequest $bloodRequest)
    {
        // ensure the model is fresh with user relation for client use
        $this->bloodRequest = $bloodRequest->load('user');
    }

    public function broadcastOn(): Channel
    {
        $hospitalId = $this->bloodRequest->hospital_admin_id ?? $this->bloodRequest->hospitalAdmin?->id;
        return new PrivateChannel("blood-requests." . ($hospitalId ?? 'global'));
    }
}
