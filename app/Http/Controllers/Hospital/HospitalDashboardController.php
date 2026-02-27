<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HospitalDashboardController extends Controller
{
    public function index()
    {
        $hospitalId = auth()->id();

        // 1. SYSTEM SUMMARY (Filtered by this Hospital where applicable)
        $totalUsers = User::count();
        $activeRequests = BloodRequest::where('status', 'pending')->count();
        $matchesCompleted = BloodRequest::where('status', 'fulfilled')->count();

        // Count ONLY this hospital's scheduled donations
        $totalDonations = Donation::where('hospital_admin_id', $hospitalId)->where('status', 'scheduled')->count();

        // 2. USER DATA
        $users = User::latest()->take(10)->get();

        // 3. QUEUE LOGIC (Triage ordering)
        $queueRequests = BloodRequest::where('status', 'pending')
            ->orderByRaw(
                "CASE
                WHEN urgency = 'Emergency' THEN 1
                WHEN urgency = 'High' THEN 2
                WHEN urgency = 'Normal' THEN 3
                ELSE 4 END",
            )
            ->orderBy('created_at', 'asc')
            ->get();

        // 4. DONATION SCHEDULING (Optimized for your new Table)
        $donations = Donation::with('user')
            ->where('hospital_admin_id', $hospitalId)
            ->whereDate('donation_date', Carbon::today())
            ->whereIn('status', ['scheduled', 'confirmed', 'completed'])
            ->orderBy('donation_time', 'asc')
            ->get();
            
        // 5. MATCHES & FULFILLED
        $fulfilledRequests = BloodRequest::where('status', 'fulfilled')->latest()->take(10)->get();

        $matches = BloodRequest::whereIn('status', ['fulfilled', 'matched'])
            ->latest()
            ->take(10)
            ->get();

        // 6. NOTIFICATIONS
        $notifications = BloodRequest::latest()
            ->take(5)
            ->get()
            ->map(function ($req) {
                return (object) [
                    'message' => "Blood request #{$req->id} ({$req->blood_type}) - {$req->status}",
                ];
            });

        // 7. COMPACT (Pass $donations instead of $schedules to match your Blade)
        return view('hospital.dashboard', compact('totalUsers', 'activeRequests', 'totalDonations', 'matchesCompleted', 'users', 'queueRequests', 'matches', 'donations', 'notifications', 'fulfilledRequests'));
    }
}
