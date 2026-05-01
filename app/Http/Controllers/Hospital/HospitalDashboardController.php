<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\User;
use Carbon\Carbon;
use App\Services\BloodRequestPriorityService;

class HospitalDashboardController extends Controller
{
    public function __construct(private readonly BloodRequestPriorityService $priorityService)
    {
    }

    public function index()
    {
        $hospitalId = auth('hospital_admin')->id();
        $priorityOrder = $this->priorityService->order();

        // Get dashboard configuration
        $statsConfig = config('dashboard.stats');

        // Build stats with metadata - filtered by current hospital
        $stats = [
            'users' => [
                'value' => User::count(),
                'config' => $statsConfig['users'],
            ],
            'pending' => [
                'value' => BloodRequest::where('hospital_admin_id', $hospitalId)
                    ->where('status', 'pending')
                    ->count(),
                'config' => $statsConfig['pending'],
            ],
            'fulfilled' => [
                'value' => BloodRequest::where('hospital_admin_id', $hospitalId)
                    ->where('status', 'fulfilled')
                    ->count(),
                'config' => $statsConfig['fulfilled'],
            ],
            'appointments' => [
                'value' => Donation::where('hospital_admin_id', $hospitalId)
                    ->where('status', 'scheduled')
                    ->whereDate('donation_date', Carbon::today())
                    ->count(),
                'config' => $statsConfig['appointments'],
            ],
        ];

        // User directory
        $users = User::latest()->paginate(10);

        // Priority queue - with blood type filter - FILTERED BY HOSPITAL
        $queueRequestsQuery = BloodRequest::with(['user', 'hospitalAdmin'])
            ->where('hospital_admin_id', $hospitalId)
            ->where('status', 'pending')
            ->when(request('blood_type'), function ($query, $bloodType) {
                return $query->where('blood_type', $bloodType);
            });

        $queueRequests = $this->priorityService->applyPriorityOrder($queueRequestsQuery)
            ->orderBy('created_at', 'asc')
            ->paginate(10, ['*'], 'requests_page');

        // Today's donations schedule
        $donations = Donation::with('user')
            ->where('hospital_admin_id', $hospitalId)
            ->whereDate('donation_date', Carbon::today())
            ->whereIn('status', ['scheduled', 'completed'])
            ->orderBy('donation_time', 'asc')
            ->get();

        // Recent activity - FILTERED BY HOSPITAL
        $fulfilledRequests = BloodRequest::with(['user', 'hospitalAdmin'])
            ->where('hospital_admin_id', $hospitalId)
            ->where('status', 'fulfilled')
            ->latest()
            ->take(10)
            ->get();

        // Notifications - FILTERED BY HOSPITAL
        $notifications = BloodRequest::where('hospital_admin_id', $hospitalId)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($req) {
                return (object) [
                    'message' => "Blood request #{$req->id} ({$req->blood_type}) - {$req->status}",
                    'time' => $req->created_at->diffForHumans(),
                ];
            });

        return view('hospital.dashboard', compact(
            'stats',
            'users',
            'queueRequests',
            'donations',
            'notifications',
            'fulfilledRequests',
            'priorityOrder'
        ));
    }
}
