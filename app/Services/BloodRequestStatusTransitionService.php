<?php

namespace App\Services;

class BloodRequestStatusTransitionService
{
    /**
     * Allowed status transitions for hospital actions.
     */
    private array $allowedTransitions = [
        'pending' => ['accepted', 'declined', 'cancelled'],
        'accepted' => ['fulfilled', 'declined', 'cancelled'],
        'declined' => [],
        'fulfilled' => [],
        'cancelled' => [],
    ];

    public function canTransition(string $fromStatus, string $toStatus): bool
    {
        return in_array($toStatus, $this->allowedTransitions[$fromStatus] ?? [], true);
    }

    public function canChangePriority(string $status): bool
    {
        return in_array($status, ['pending', 'accepted'], true);
    }
}
