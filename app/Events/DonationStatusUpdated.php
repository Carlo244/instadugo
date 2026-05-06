<?php

namespace App\Events;

use App\Models\Donation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DonationStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $donation;
    public $fromStatus;
    public $toStatus;

    public function __construct(Donation $donation, string $fromStatus, string $toStatus)
    {
        $this->donation = $donation;
        $this->fromStatus = $fromStatus;
        $this->toStatus = $toStatus;
    }

    public function broadcastOn(): Channel
    {
        $hospitalId = $this->donation->hospital_admin_id ?? $this->donation->hospitalAdmin?->id;
        return new PrivateChannel("donations." . ($hospitalId ?? 'global'));
    }
}