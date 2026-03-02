<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use App\Models\HospitalAdmin;
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

        $userRequests = BloodRequest::with('hospitalAdmin')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('user.blood-requests', compact('hospitals', 'userRequests'));
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
        event(new \App\Events\BloodRequestCreated($bloodRequest));

        return back()->with('success', 'Your blood request has been submitted.');
    }
}
