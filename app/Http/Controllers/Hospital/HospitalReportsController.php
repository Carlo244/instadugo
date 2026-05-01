<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class HospitalReportsController extends Controller
{
    public function index(Request $request)
    {
        $hospitalId = auth('hospital_admin')->id();

        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'preset' => ['nullable', 'in:last7,last30,this_month'],
        ]);

        $from = $validated['from'] ?? null;
        $to = $validated['to'] ?? null;
        $preset = $validated['preset'] ?? null;

        if ($preset === 'last7') {
            $from = Carbon::now()->subDays(6)->toDateString();
            $to = Carbon::now()->toDateString();
        } elseif ($preset === 'last30') {
            $from = Carbon::now()->subDays(29)->toDateString();
            $to = Carbon::now()->toDateString();
        } elseif ($preset === 'this_month') {
            $from = Carbon::now()->startOfMonth()->toDateString();
            $to = Carbon::now()->toDateString();
        }

        $baseQuery = BloodRequest::where('hospital_admin_id', $hospitalId)
            ->when($from, fn($query) => $query->whereDate('created_at', '>=', $from))
            ->when($to, fn($query) => $query->whereDate('created_at', '<=', $to));

        $totalRequests = (clone $baseQuery)->count();
        $waitingRequests = (clone $baseQuery)
            ->whereIn('status', ['pending', 'accepted'])
            ->count();
        $completedRequests = (clone $baseQuery)
            ->where('status', 'fulfilled')
            ->count();
        $cancelledRequests = (clone $baseQuery)
            ->where('status', 'cancelled')
            ->count();

        // Duty-focused metrics should reflect current operational needs.
        $actionWaitingNow = BloodRequest::where('hospital_admin_id', $hospitalId)
            ->whereIn('status', ['pending', 'accepted'])
            ->count();

        $actionUrgentToday = BloodRequest::where('hospital_admin_id', $hospitalId)
            ->whereIn('status', ['pending', 'accepted'])
            ->where('urgency', 'Emergency')
            ->whereDate('date_needed', '<=', Carbon::today()->toDateString())
            ->count();

        $actionOverduePending = BloodRequest::where('hospital_admin_id', $hospitalId)
            ->where('status', 'pending')
            ->where('created_at', '<=', Carbon::now()->subHours(2))
            ->count();

        $reportRows = (clone $baseQuery)
            ->get(['created_at', 'status', 'blood_type', 'updated_at']);

        $requestsPerMonth = $reportRows
            ->groupBy(fn (BloodRequest $request) => $request->created_at?->format('Y-m'))
            ->filter()
            ->map(fn (Collection $group, string $month) => (object) [
                'month' => $month,
                'total' => $group->count(),
            ])
            ->sortKeys()
            ->values();

        $statusCounts = $reportRows
            ->whereIn('status', ['fulfilled', 'cancelled'])
            ->groupBy('status')
            ->map(fn (Collection $group, string $status) => (object) [
                'status' => $status,
                'total' => $group->count(),
            ])
            ->values();

        $avgResponseTime = $reportRows
            ->filter(fn (BloodRequest $request) => in_array($request->status, ['accepted', 'fulfilled'], true))
            ->map(fn (BloodRequest $request) => $request->created_at && $request->updated_at
                ? $request->created_at->diffInMinutes($request->updated_at)
                : null)
            ->filter(fn ($minutes) => $minutes !== null)
            ->avg();

        $bloodTypeCounts = $reportRows
            ->groupBy('blood_type')
            ->map(fn (Collection $group, string $bloodType) => (object) [
                'blood_type' => $bloodType,
                'total' => $group->count(),
            ])
            ->sortByDesc('total')
            ->values();

        return view('hospital.reports', compact(
            'requestsPerMonth',
            'statusCounts',
            'avgResponseTime',
            'bloodTypeCounts',
            'from',
            'to',
            'preset',
            'totalRequests',
            'waitingRequests',
            'completedRequests',
            'cancelledRequests',
            'actionWaitingNow',
            'actionUrgentToday',
            'actionOverduePending'
        ));
    }
}
