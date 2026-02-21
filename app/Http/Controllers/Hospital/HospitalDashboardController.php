<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Http\Request;

class HospitalDashboardController extends Controller
{
    public function index()
    {
        // =========================
        // SYSTEM SUMMARY (TOP CARDS)
        // =========================
        $totalUsers = User::count();

        $activeRequests = BloodRequest::where('status', 'pending')->count();

        $matchesCompleted = BloodRequest::where('status', 'fulfilled')->count();

        $totalDonations = Donation::where('status', 'scheduled')->count();

        $users = User::latest()->take(10)->get();

        $queueRequests = BloodRequest::where('status', 'pending')
            ->orderByRaw(
                "
                CASE
                    WHEN urgency = 'Emergency' THEN 1
                    WHEN urgency = 'High' THEN 2
                    WHEN urgency = 'Normal' THEN 3
                END
                ",
            )
            ->orderBy('created_at', 'asc')
            ->get();

        $fulfilledRequests = BloodRequest::where('status', 'fulfilled')->latest()->take(10)->get();
        $matches = BloodRequest::whereIn('status', ['fulfilled', 'matched'])
            ->latest()
            ->take(10)
            ->get();

        // =========================
        // DONATION SCHEDULING
        // =========================
        $schedules = Donation::whereIn('status', ['confirmed', 'completed'])
            ->orderBy('donation_date', 'asc')
            ->take(10)
            ->get();

        // =========================
        // NOTIFICATIONS (simple version)
        // =========================
        $notifications = BloodRequest::latest()
            ->take(5)
            ->get()
            ->map(function ($req) {
                return (object) [
                    'message' => "Blood request #{$req->id} ({$req->blood_type}) - {$req->status}",
                ];
            });

        return view('hospital.dashboard', compact('totalUsers', 'activeRequests', 'totalDonations', 'matchesCompleted', 'users', 'queueRequests', 'matches', 'schedules', 'notifications', 'fulfilledRequests'));
    }
}
