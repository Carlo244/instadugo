<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class BloodRequestPriorityService
{
    public function levels(): array
    {
        return config('priorities.levels', []);
    }

    public function order(): array
    {
        return config('priorities.order', array_keys($this->levels()));
    }

    public function activeStatuses(): array
    {
        return ['pending', 'accepted'];
    }

    public function historyStatuses(): array
    {
        return ['fulfilled', 'declined', 'cancelled'];
    }

    public function queueRequests(Collection $requests, string $level, ?array $activeStatuses = null): Collection
    {
        return $requests
            ->where('urgency', $level)
            ->whereIn('status', $activeStatuses ?? $this->activeStatuses());
    }

    public function buildQueues(Collection $requests, ?array $activeStatuses = null): array
    {
        $queues = [];
        $totalActive = 0;

        foreach ($this->order() as $level) {
            $queueRequests = $this->queueRequests($requests, $level, $activeStatuses);

            $queues[$level] = [
                'requests' => $queueRequests,
                'count' => $queueRequests->count(),
                'config' => $this->levels()[$level] ?? [],
            ];

            $totalActive += $queues[$level]['count'];
        }

        return compact('queues', 'totalActive');
    }

    public function applyPriorityOrder(Builder $query, string $column = 'urgency'): Builder
    {
        $cases = [];

        foreach ($this->order() as $index => $level) {
            $position = $index + 1;
            $escapedLevel = str_replace("'", "''", $level);
            $cases[] = "WHEN {$column} = '{$escapedLevel}' THEN {$position}";
        }

        if (empty($cases)) {
            return $query;
        }

        $caseSql = 'CASE ' . implode(' ', $cases) . ' ELSE ' . (count($cases) + 1) . ' END';

        return $query->orderByRaw($caseSql);
    }
}