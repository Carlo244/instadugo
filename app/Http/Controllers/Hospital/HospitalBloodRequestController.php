<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use Illuminate\Http\Request;

class HospitalBloodRequestController extends Controller
{
    /**
     * Display all blood requests (pending, approved, fulfilled, cancelled)
     */
    public function index()
    {
        $requests = BloodRequest::with('user', 'hospitalAdmin')
            ->where('hospital_admin_id', auth()->id()) // only requests for this hospital
            ->orderBy('created_at', 'desc')
            ->get();

        // Optionally separate by status for easier display
        $pendingRequests = $requests->where('status', 'pending');
        $approvedRequests = $requests->where('status', 'approved');
        $fulfilledRequests = $requests->where('status', 'fulfilled');

        return view('hospital.requests', compact(
            'requests',
            'pendingRequests',
            'approvedRequests',
            'fulfilledRequests'
        ));
    }

    /**
     * Approve a blood request
     */
    public function approve($id)
    {
        $request = BloodRequest::findOrFail($id);

        if ($request->hospital_admin_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->update(['status' => 'approved']);

        return back()->with('success', "Blood request #{$id} has been approved.");
    }

    /**
     * Fulfill a blood request
     */
    public function fulfill($id)
    {
        $request = BloodRequest::findOrFail($id);

        if ($request->hospital_admin_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->update(['status' => 'fulfilled']);

        return back()->with('success', "Blood request #{$id} has been fulfilled.");
    }

    /**
     * Cancel a blood request
     */
    public function cancel($id)
    {
        $request = BloodRequest::findOrFail($id);

        if ($request->hospital_admin_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->update(['status' => 'cancelled']);

        return back()->with('success', "Blood request #{$id} has been cancelled.");
    }

    /**
     * View details of a single request
     */
    public function show($id)
    {
        $request = BloodRequest::with('user', 'hospitalAdmin')->findOrFail($id);

        if ($request->hospital_admin_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('hospital.requests.show', compact('request'));
    }
}
