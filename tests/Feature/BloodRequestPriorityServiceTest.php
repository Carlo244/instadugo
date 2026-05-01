<?php

namespace Tests\Feature;

use App\Services\BloodRequestPriorityService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class BloodRequestPriorityServiceTest extends TestCase
{
    public function test_it_builds_multilevel_queues_in_config_order_with_correct_counts(): void
    {
        $service = new BloodRequestPriorityService();

        $requests = new Collection([
            ['id' => 1, 'urgency' => 'High', 'status' => 'pending'],
            ['id' => 2, 'urgency' => 'Emergency', 'status' => 'accepted'],
            ['id' => 3, 'urgency' => 'Normal', 'status' => 'pending'],
            ['id' => 4, 'urgency' => 'Emergency', 'status' => 'fulfilled'],
            ['id' => 5, 'urgency' => 'High', 'status' => 'cancelled'],
            ['id' => 6, 'urgency' => 'Emergency', 'status' => 'pending'],
        ]);

        $result = $service->buildQueues($requests);

        $this->assertSame(['Emergency', 'High', 'Normal'], array_keys($result['queues']));
        $this->assertSame(2, $result['queues']['Emergency']['count']);
        $this->assertSame(1, $result['queues']['High']['count']);
        $this->assertSame(1, $result['queues']['Normal']['count']);
        $this->assertSame(4, $result['totalActive']);
    }

    public function test_it_only_includes_active_statuses_in_queues(): void
    {
        $service = new BloodRequestPriorityService();

        $requests = new Collection([
            ['id' => 11, 'urgency' => 'Emergency', 'status' => 'pending'],
            ['id' => 12, 'urgency' => 'Emergency', 'status' => 'accepted'],
            ['id' => 13, 'urgency' => 'Emergency', 'status' => 'fulfilled'],
            ['id' => 14, 'urgency' => 'Emergency', 'status' => 'declined'],
            ['id' => 15, 'urgency' => 'Emergency', 'status' => 'cancelled'],
        ]);

        $queue = $service->queueRequests($requests, 'Emergency');

        $this->assertCount(2, $queue);
        $this->assertSame([11, 12], $queue->pluck('id')->values()->all());
    }

    public function test_it_exposes_expected_history_statuses(): void
    {
        $service = new BloodRequestPriorityService();

        $this->assertSame(['fulfilled', 'declined', 'cancelled'], $service->historyStatuses());
    }

    public function test_it_builds_case_order_expression_for_priority_sorting(): void
    {
        $service = new BloodRequestPriorityService();

        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('orderByRaw')
            ->once()
            ->withArgs(function (string $sql): bool {
                return str_contains($sql, "WHEN urgency = 'Emergency' THEN 1")
                    && str_contains($sql, "WHEN urgency = 'High' THEN 2")
                    && str_contains($sql, "WHEN urgency = 'Normal' THEN 3")
                    && str_contains($sql, 'ELSE 4 END');
            })
            ->andReturnSelf();

        $result = $service->applyPriorityOrder($builder);

        $this->assertSame($builder, $result);
    }
}
