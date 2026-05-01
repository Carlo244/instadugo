<?php

namespace Tests\Feature;

use App\Services\BloodRequestStatusTransitionService;
use Tests\TestCase;

class BloodRequestStatusTransitionServiceTest extends TestCase
{
    public function test_it_allows_expected_status_transitions(): void
    {
        $service = new BloodRequestStatusTransitionService();

        $this->assertTrue($service->canTransition('pending', 'accepted'));
        $this->assertTrue($service->canTransition('pending', 'declined'));
        $this->assertTrue($service->canTransition('pending', 'cancelled'));
        $this->assertTrue($service->canTransition('accepted', 'fulfilled'));
        $this->assertTrue($service->canTransition('accepted', 'declined'));
        $this->assertTrue($service->canTransition('accepted', 'cancelled'));
    }

    public function test_it_blocks_invalid_or_terminal_transitions(): void
    {
        $service = new BloodRequestStatusTransitionService();

        $this->assertFalse($service->canTransition('pending', 'fulfilled'));
        $this->assertFalse($service->canTransition('fulfilled', 'accepted'));
        $this->assertFalse($service->canTransition('declined', 'accepted'));
        $this->assertFalse($service->canTransition('cancelled', 'accepted'));
        $this->assertFalse($service->canTransition('unknown', 'accepted'));
    }

    public function test_it_only_allows_priority_changes_for_active_states(): void
    {
        $service = new BloodRequestStatusTransitionService();

        $this->assertTrue($service->canChangePriority('pending'));
        $this->assertTrue($service->canChangePriority('accepted'));
        $this->assertFalse($service->canChangePriority('fulfilled'));
        $this->assertFalse($service->canChangePriority('declined'));
        $this->assertFalse($service->canChangePriority('cancelled'));
    }
}
