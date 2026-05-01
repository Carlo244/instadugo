<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        $requestsPerMonth = (clone $baseQuery)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $statusCounts = (clone $baseQuery)
            ->selectRaw('status, COUNT(*) as total')
            ->whereIn('status', ['fulfilled', 'cancelled'])
            ->groupBy('status')
            ->get();

        $avgResponseTime = (clone $baseQuery)
            ->whereIn('status', ['accepted', 'fulfilled'])
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) as avg_minutes')
            ->value('avg_minutes');

        $bloodTypeCounts = (clone $baseQuery)
            ->selectRaw('blood_type, COUNT(*) as total')
            ->groupBy('blood_type')
            ->orderByDesc('total')
            ->get();

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
