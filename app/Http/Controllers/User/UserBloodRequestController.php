<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use App\Models\HospitalAdmin;
use App\Notifications\HospitalAdminDashboardNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserBloodRequestController extends Controller
{
    /**
     * Show the user blood requests page
     */
    public function userIndex()
    {
        $hospitals = HospitalAdmin::all();

        // Split into active (pending/accepted) and history (fulfilled/declined/cancelled)
        $currentRequests = BloodRequest::with('hospitalAdmin')
            ->where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'accepted'])
            ->latest()
            ->get();

        $historyRequests = BloodRequest::with('hospitalAdmin')
            ->where('user_id', Auth::id())
            ->whereIn('status', ['fulfilled', 'declined', 'cancelled'])
            ->latest()
            ->get();

        return view('user.blood-requests', compact('hospitals', 'currentRequests', 'historyRequests'));
    }

    /**
     * Store a new blood request (user)
     */
    public function store(Request $request)
    {
        $request->validate([
            'blood_type' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'quantity' => 'required|integer|min:1',
            'hospital_admin_id' => 'required|exists:hospital_admins,id',
            'urgency' => 'required|in:Emergency,High,Normal',
            'date_needed' => 'required|date|after_or_equal:today',
            'reason' => 'required|string|max:1000',
        ]);

        $bloodRequest = BloodRequest::create([
            'user_id' => auth()->id(),
            'blood_type' => $request->blood_type,
            'quantity' => $request->quantity,
            'hospital_admin_id' => $request->hospital_admin_id,
            'urgency' => $request->urgency,
            'date_needed' => $request->date_needed,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        // broadcast so hospital dashboards get updated immediately
        try {
            event(new \App\Events\BloodRequestCreated($bloodRequest));
        } catch (\Throwable $e) {
            report($e);
        }

        $hospital = HospitalAdmin::find($bloodRequest->hospital_admin_id);
        if ($hospital) {
            $hospital->notify(new HospitalAdminDashboardNotification(
                'blood_request_created',
                'New blood request for ' . $bloodRequest->blood_type . ' has been submitted.',
                route('hospital.requests'),
                [
                    'blood_request_id' => $bloodRequest->id,
                    'blood_type' => $bloodRequest->blood_type,
                    'quantity' => $bloodRequest->quantity,
                    'urgency' => $bloodRequest->urgency,
                ]
            ));
        }

        return back()->with('success', 'Your blood request has been submitted.');
    }
}
