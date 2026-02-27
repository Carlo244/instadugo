<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\HospitalAdmin;
use App\Models\User;
use App\Notifications\DonorRequestNotification;
use Illuminate\Http\Request;

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

        // Compatible donors based on blood type
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

        $compatibleDonors = User::whereIn('blood_type', $compatibleBloodTypes)
            ->where('id', '!=', $user->id)
            ->whereDoesntHave('donations', function ($query) {
                $query->where('status', 'completed')->where('donation_date', '>', now()->subDays(56));
            })
            ->get();

        return view('user.dashboard', compact('hospitals', 'userRequests', 'donations', 'compatibleDonors'));
    }

    public function sendDonorRequest(Request $request)
    {
        $validated = $request->validate([
            'donor_id' => 'required|exists:users,id',
            'hospital_admin_id' => 'required|exists:hospital_admins,id', // NEW
            'urgency' => 'required|in:Normal,High,Emergency',
            'message' => 'required|string|max:500',
        ]);

        $donor = User::findOrFail($validated['donor_id']);
        $sender = auth()->user();

        // Find hospital name to include in the notification message
        $hospital = HospitalAdmin::find($validated['hospital_admin_id']);

        // 1. Create Blood Request Record
        BloodRequest::create([
            'user_id' => $sender->id,
            'hospital_admin_id' => $validated['hospital_admin_id'], // Now linked to the hospital
            'blood_type' => $sender->blood_type,
            'urgency' => $validated['urgency'],
            'reason' => $validated['message'],
            'status' => 'pending',
            'date_needed' => now(),
            'quantity' => 1,
        ]);

        // 2. Notify Donor (Include hospital name in notification)
        $donor->notify(
            new DonorRequestNotification($sender, [
                'urgency' => $validated['urgency'],
                'message' => $validated['message'],
                'hospital' => $hospital->hospital_name, // Pass this to your notification class
            ]),
        );

        return back()->with('success', 'Request sent to donor!');
    }
}
