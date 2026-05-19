<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

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

    /**
     * Build multi-level priority queues from a collection (or paginator/array) of requests.
     * Normalizes incoming data to a Collection so callers may pass a Paginator or array.
     *
     * @param  Collection|LengthAwarePaginator|array  $requests
     */
    public function buildQueues($requests, ?array $activeStatuses = null): array
    {
        // Normalize to Collection if a paginator or array was provided
        if ($requests instanceof LengthAwarePaginator) {
            $requests = collect($requests->items());
        } elseif (is_array($requests)) {
            $requests = collect($requests);
        }

        if (! $requests instanceof Collection) {
            $requests = collect($requests);
        }

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