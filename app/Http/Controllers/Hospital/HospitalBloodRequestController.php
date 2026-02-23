<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use App\Models\User;
use App\Notifications\BloodRequestApproved;
use Illuminate\Http\Request;

class HospitalBloodRequestController extends Controller
{
    /**
     * Display blood requests with compatibility matching and urgency queues
     */
    public function index()
    {
        $requests = BloodRequest::with('user', 'hospitalAdmin')
            ->where('hospital_admin_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        // Attach matched donors to each request
        foreach ($requests as $request) {
            $request->matchedDonors = User::whereIn(
                    'blood_type',
                    $this->compatibleDonors($request->blood_type)
                )
                ->where('id', '!=', $request->user_id)
                ->get();
        }

        // Group requests by urgency and active status
        $queues = [
            'Emergency' => $requests->where('urgency', 'Emergency')
                                    ->whereIn('status', ['pending', 'approved']),
            'High'      => $requests->where('urgency', 'High')
                                    ->whereIn('status', ['pending', 'approved']),
            'Normal'    => $requests->where('urgency', 'Normal')
                                    ->whereIn('status', ['pending', 'approved']),
        ];

        $fulfilledRequests = $requests->where('status', 'fulfilled');

        return view('hospital.requests', compact(
            'requests',
            'queues',
            'fulfilledRequests'
        ));
    }

    /**
     * Approve a blood request and notify the user
     */
    public function approve($id)
    {
        $request = BloodRequest::with('user')->findOrFail($id);

        if ($request->hospital_admin_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->update(['status' => 'approved']);

        // Notify user via email/notification
        $request->user->notify(new BloodRequestApproved($request));

        return back()->with('success', 'Blood request approved and user notified.');
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
     * View a single blood request
     */
    public function show($id)
    {
        $request = BloodRequest::with('user', 'hospitalAdmin')->findOrFail($id);

        if ($request->hospital_admin_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('hospital.requests.show', compact('request'));
    }

    /**
     * Rule-based compatibility function
     */
    private function compatibleDonors($bloodType)
    {
        $compatibility = [
            'O-'  => ['O-'],
            'O+'  => ['O+', 'O-'],
            'A-'  => ['A-', 'O-'],
            'A+'  => ['A+', 'A-', 'O+', 'O-'],
            'B-'  => ['B-', 'O-'],
            'B+'  => ['B+', 'B-', 'O+', 'O-'],
            'AB-' => ['AB-', 'A-', 'B-', 'O-'],
            'AB+' => ['AB+', 'AB-', 'A+', 'A-', 'B+', 'B-', 'O+', 'O-'],
        ];

        return $compatibility[$bloodType] ?? [];
    }
}