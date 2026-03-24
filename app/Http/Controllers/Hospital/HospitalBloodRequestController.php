<?php

namespace App\Http\Controllers\Hospital;

use App\Events\BloodRequestStatusUpdated;
use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use App\Models\User;
use App\Notifications\BloodReadyForPickup;
use App\Notifications\BloodRequestApproved;
use App\Notifications\BloodRequestNotification;

class HospitalBloodRequestController extends Controller
{
    /**
     * Display blood requests with compatibility matching and urgency queues
     */
    public function index(\Illuminate\Http\Request $request)
    {
        // Added 'receiver' to with() to prevent N+1 issues when displaying target donors
        $requests = BloodRequest::with(['user', 'hospitalAdmin', 'receiver'])
            ->where('hospital_admin_id', auth('hospital_admin')->id())
            ->orderBy('created_at', 'desc')
            ->get();

        // Attach matched donors to each request (only for public requests)
        foreach ($requests as $req) {
            if (!$req->receiver_id) {
                $compatibleBloodTypes = $this->compatibleDonors($req->blood_type);

                $req->matchedDonors = User::whereIn('blood_type', $compatibleBloodTypes)->where('id', '!=', $req->user_id)->get()->filter(fn($donor) => $donor->isEligible());
            } else {
                $req->matchedDonors = collect(); // No matching needed for direct invites
            }
        }

        /**
         * UPDATED QUEUE LOGIC:
         * We now include 'accepted' because those are active appointments
         * that the hospital needs to fulfill.
         */
        $activeStatuses = ['pending', 'accepted'];

        // Get priority configuration
        $priorityConfig = config('priorities.levels');
        $priorityOrder = config('priorities.order');

        // Build queue data with computed metadata
        $queues = [];
        $totalActive = 0;

        foreach ($priorityOrder as $level) {
            $queueRequests = $requests->where('urgency', $level)->whereIn('status', $activeStatuses);
            $count = $queueRequests->count();
            $totalActive += $count;

            $queues[$level] = [
                'requests' => $queueRequests,
                'count' => $count,
                'config' => $priorityConfig[$level],
            ];
        }

        // History includes fulfilled, declined, and cancelled
        $fulfilledRequests = $requests->whereIn('status', ['fulfilled', 'declined', 'cancelled']);

        // If ajax request, return the appropriate partial
        if ($request->boolean('ajax')) {
            $level = $request->get('level', 'Emergency');
            if ($level === 'History') {
                return view('partials.hospital-bloodrequest-table', [
                    'requests' => $fulfilledRequests,
                    'level' => 'History',
                ]);
            }
            return view('partials.hospital-bloodrequest-table', [
                'requests' => $queues[$level]['requests'] ?? collect(),
                'level' => $level,
            ]);
        }

        return view('hospital.requests', compact('queues', 'fulfilledRequests', 'totalActive', 'priorityOrder'));
    }

    /**
     * Approve a blood request and notify the user
     */
    public function approve($id)
    {
        $request = BloodRequest::with('user')->findOrFail($id);
        $this->authorizeRequestOwner($request);
        $previousStatus = (string) $request->status;

        // CHANGE THIS LINE:
        // It was 'pending', which is why it looked like it wasn't updating.
        $request->update(['status' => 'accepted']);
        event(new BloodRequestStatusUpdated($request->fresh(['user']), $previousStatus));

        $request->user->notify(new BloodRequestApproved($request));

        return back()->with('success', 'Blood request has been approved and moved to the active queue.');
    }

    /**
     * Fulfill a blood request
     */
    public function fulfill($id)
    {
        $request = BloodRequest::with('user')->findOrFail($id);
        $this->authorizeRequestOwner($request);
        $previousStatus = (string) $request->status;

        // This is the "Finalize" action
        $request->update(['status' => 'fulfilled']);
        event(new BloodRequestStatusUpdated($request->fresh(['user']), $previousStatus));

        if ($request->user) {
            $request->user->notify(new BloodReadyForPickup($request));
        }

        // Optional: If you have a Donation model, you would create a record here
        // to reward the donor with points/certificates.

        return back()->with('success', "Blood request #{$id} has been successfully fulfilled.");
    }

    /**
     * Cancel a blood request
     */
    public function cancel($id)
    {
        $request = BloodRequest::findOrFail($id);

        $this->authorizeRequestOwner($request);
        $previousStatus = (string) $request->status;

        $request->update(['status' => 'cancelled']);
        event(new BloodRequestStatusUpdated($request->fresh(['user']), $previousStatus));

        return back()->with('success', "Blood request #{$id} has been cancelled.");
    }

    /**
     * Notify a single compatible donor
     */
    public function notify(BloodRequest $request, User $donor)
    {
        // Make sure $request is passed here!
        $donor->notify(new BloodRequestNotification($request));

        return back()->with('success', "Notification sent to {$donor->name}");
    }

    /**
     * Bulk notify all compatible donors
     */
    public function bulkNotify(BloodRequest $request)
    {
        $eligibleDonors = $request->matchedDonors->filter(fn($donor) => $donor->isEligible());

        foreach ($eligibleDonors as $donor) {
            $donor->notify(new BloodRequestNotification($request));
        }

        return back()->with('success', 'Notifications sent to all eligible donors.');
    }

    /**
     * View a single blood request
     */
    public function show($id)
    {
        $request = BloodRequest::with('user', 'hospitalAdmin')->findOrFail($id);

        $this->authorizeRequestOwner($request);

        return view('hospital.requests.show', compact('request'));
    }

    /**
     * Rule-based blood type compatibility
     */
    private function compatibleDonors($bloodType)
    {
        $compatibility = [
            'O-' => ['O-'],
            'O+' => ['O+', 'O-'],
            'A-' => ['A-', 'O-'],
            'A+' => ['A+', 'A-', 'O+', 'O-'],
            'B-' => ['B-', 'O-'],
            'B+' => ['B+', 'B-', 'O+', 'O-'],
            'AB-' => ['AB-', 'A-', 'B-', 'O-'],
            'AB+' => ['AB+', 'AB-', 'A+', 'A-', 'B+', 'B-', 'O+', 'O-'],
        ];

        return $compatibility[$bloodType] ?? [];
    }

    /**
     * Ensure the logged-in hospital admin owns the request
     */
    private function authorizeRequestOwner(BloodRequest $request)
    {
        if ($request->hospital_admin_id !== auth('hospital_admin')->id()) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Update phlebotomist count for the hospital
     */
    public function updatePhlebotomist(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'phlebotomist_count' => 'required|integer|min:1|max:10',
        ]);

        $hospital = auth('hospital_admin')->user();
        $hospital->update(['phlebotomist_count' => $validated['phlebotomist_count']]);

        return back()->with('success', "Phlebotomist count updated to {$validated['phlebotomist_count']}. Time slot capacity has been adjusted.");
    }
}
