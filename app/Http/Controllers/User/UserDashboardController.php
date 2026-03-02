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
    public function index(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();

        // Latest 5 donations scheduled BY this user
        $donations = Donation::where('user_id', $user->id)->orderBy('donation_date', 'desc')->orderBy('donation_time', 'desc')->take(5)->get();

        if ($request->boolean('ajax')) {
            return view('partials.user-donations-table', compact('donations'));
        }

        // Hospitals for dropdowns
        $hospitals = HospitalAdmin::all();

        // Latest 5 blood requests made BY this user
        $userRequests = BloodRequest::where('user_id', $user->id)->latest()->take(5)->get();


        // Invitations sent specifically TO this user
        // We filter out 'declined' so they don't clutter the dashboard
        $invitations = BloodRequest::where('receiver_id', $user->id)
            ->where('status', '!=', 'declined')
            ->with(['user', 'hospital'])
            ->latest()
            ->get();

        // Compatible donors who can give to the current user
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
            // Eligibility check: No completed donations in the last 56 days
            ->whereDoesntHave('donations', function ($query) {
                $query->where('status', 'completed')->where('donation_date', '>', now()->subDays(56));
            })
            ->get();

        return view('user.dashboard', compact('hospitals', 'userRequests', 'donations', 'compatibleDonors', 'invitations', 'user'));
    }


    public function sendDonorRequest(Request $request)
    {
        // 1. Validation
        $validated = $request->validate([
            'donor_id' => 'required|exists:users,id',
            'hospital_admin_id' => 'required|exists:hospital_admins,id',
            'urgency' => 'required|in:Normal,High,Emergency',
            'message' => 'required|string|max:500',
        ]);

        // 2. Define the variables
        $sender = auth()->user();
        $donor = User::findOrFail($validated['donor_id']);
        $hospital = HospitalAdmin::findOrFail($validated['hospital_admin_id']);

        // 3. Create Blood Request Record (Now including receiver_id)
        $bloodRequest = BloodRequest::create([
            'user_id' => $sender->id,
            'receiver_id' => $donor->id, // CRITICAL: This links the request to the donor!
            'hospital_admin_id' => $validated['hospital_admin_id'],
            'blood_type' => $sender->blood_type,
            'urgency' => $validated['urgency'],
            'reason' => $validated['message'],
            'status' => 'pending',
            'date_needed' => now()->addDays(1), // Usually requests need a lead time
            'quantity' => 1,
        ]);

        // 4. Notify Donor
        // We pass the $bloodRequest object directly so the notification can access its ID
        $donor->notify(
            new DonorRequestNotification($sender, [
                'request_id' => $bloodRequest->id,
                'urgency' => $validated['urgency'],
                'message' => $validated['message'],
                'hospital' => $hospital->hospital_name,
            ]),
        );

        return back()->with('success', 'Request sent directly to ' . $donor->name . '!');
    }

    public function showRequest($id)
    {
        // Fetch the request with hospital and the person who needs blood (user)
        $bloodRequest = BloodRequest::with(['hospital', 'user'])->findOrFail($id);

        return view('user.show_request', compact('bloodRequest'));
    }


}
