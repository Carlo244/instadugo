<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\User;
use Carbon\Carbon;

class HospitalDashboardController extends Controller
{
    public function index()
    {
        $hospitalId = auth()->id();

        // Get dashboard configuration
        $statsConfig = config('dashboard.stats');

        // Build stats with metadata
        $stats = [
            'users' => [
                'value' => User::count(),
                'config' => $statsConfig['users'],
            ],
            'pending' => [
                'value' => BloodRequest::where('status', 'pending')->count(),
                'config' => $statsConfig['pending'],
            ],
            'fulfilled' => [
                'value' => BloodRequest::where('status', 'fulfilled')->count(),
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

        // Priority queue - with blood type filter
        $priorityOrder = config('priorities.order');
        $queueRequests = BloodRequest::with(['user', 'hospitalAdmin'])
            ->where('status', 'pending')
            ->when(request('blood_type'), function ($query, $bloodType) {
                return $query->where('blood_type', $bloodType);
            })
            ->orderByRaw(
                "CASE
                WHEN urgency = 'Emergency' THEN 1
                WHEN urgency = 'High' THEN 2
                WHEN urgency = 'Normal' THEN 3
                ELSE 4 END"
            )
            ->orderBy('created_at', 'asc')
            ->paginate(10, ['*'], 'requests_page');

        // Today's donations schedule
        $donations = Donation::with('user')
            ->where('hospital_admin_id', $hospitalId)
            ->whereDate('donation_date', Carbon::today())
            ->whereIn('status', ['scheduled', 'confirmed', 'completed'])
            ->orderBy('donation_time', 'asc')
            ->get();

        // Recent activity
        $fulfilledRequests = BloodRequest::with(['user', 'hospitalAdmin'])
            ->where('status', 'fulfilled')
            ->latest()
            ->take(10)
            ->get();

        // Notifications
        $notifications = BloodRequest::latest()
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
