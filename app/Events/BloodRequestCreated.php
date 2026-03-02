<?php

namespace App\Events;

use App\Models\BloodRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BloodRequestCreated implements ShouldBroadcast
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
        return new Channel('blood-requests');
    }
}
