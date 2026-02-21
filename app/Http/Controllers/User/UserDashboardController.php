<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\HospitalAdmin;
use App\Models\Notification;
use App\Models\User;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Hospitals for dropdowns
        $hospitals = HospitalAdmin::all();

        // Latest 5 blood requests for this user
        $userRequests = BloodRequest::where('user_id', $user->id)->orderBy('created_at', 'desc')->take(5)->get();

        // Latest 5 donations for this user
        $donations = Donation::where('user_id', $user->id)->orderBy('donation_date', 'desc')->orderBy('donation_time', 'desc')->take(5)->get();

        // Compatible donors based on blood type and eligibility
        $compatibility = [
            'A+' => ['A+', 'A-', 'O+', 'O-'],
            'A-' => ['A-', 'O-'],
            'B+' => ['B+', 'B-', 'O+', 'O-'],
            'B-' => ['B-', 'O-'],
            'AB+' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'],
            'AB-' => ['A-', 'B-', 'AB-', 'O-'],
            'O+' => ['O+', 'O-'],
            'O-' => ['O-'],
        ];

        $compatibleBloodTypes = $compatibility[$user->blood_type] ?? [];

        $compatibleDonors = User::whereIn('blood_type', $compatibleBloodTypes)->where('id', '!=', $user->id)->get()->filter(fn($donor) => $donor->isEligible());
        $notifications = Notification::where('user_id', $user->id)
                                     ->orderBy('created_at', 'desc')
                                     ->take(5)
                                     ->get();
        return view('user.dashboard', compact('hospitals', 'userRequests', 'donations', 'compatibleDonors', 'notifications'));
    }
}
