<?php

namespace App\Events;

use App\Models\BloodRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BloodRequestPriorityUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bloodRequest;
    public $fromUrgency;
    public $toUrgency;

    public function __construct(BloodRequest $bloodRequest, string $fromUrgency)
    {
        $this->bloodRequest = $bloodRequest->loadMissing('user');
        $this->fromUrgency = $fromUrgency;
        $this->toUrgency = (string) $bloodRequest->urgency;
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('blood-requests');
    }
}
