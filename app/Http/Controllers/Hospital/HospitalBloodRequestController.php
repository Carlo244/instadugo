<?php

namespace App\Http\Controllers\Hospital;

use App\Events\BloodRequestPriorityUpdated;
use App\Events\BloodRequestStatusUpdated;
use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use App\Models\User;
use App\Notifications\BloodReadyForPickup;
use App\Notifications\BloodRequestApproved;
use App\Notifications\BloodRequestNotification;
use App\Notifications\BloodRequestStatusChanged;
use App\Services\BloodRequestAuditService;
use App\Services\BloodRequestMatchingService;
use App\Services\BloodRequestPriorityService;
use App\Services\BloodRequestStatusTransitionService;

class HospitalBloodRequestController extends Controller
{
    public function __construct(
        private readonly BloodRequestPriorityService $priorityService,
        private readonly BloodRequestMatchingService $matchingService,
        private readonly BloodRequestStatusTransitionService $statusTransitionService,
        private readonly BloodRequestAuditService $auditService
    )
    {
    }

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
            $req->matchedDonors = $this->matchingService->getEligibleMatchedDonors($req);
        }

        /**
         * UPDATED QUEUE LOGIC:
         * We now include 'accepted' because those are active appointments
         * that the hospital needs to fulfill.
         */
        $activeStatuses = ['pending', 'accepted'];

        $priorityOrder = $this->priorityService->order();
        $queueData = $this->priorityService->buildQueues($requests, $activeStatuses);
        $queues = $queueData['queues'];
        $totalActive = $queueData['totalActive'];

        // History includes fulfilled, declined, and cancelled
        $fulfilledRequests = $requests->whereIn('status', $this->priorityService->historyStatuses());

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

        if (!$this->statusTransitionService->canTransition($previousStatus, 'accepted')) {
            return back()->withErrors([
                'status' => "Cannot approve a request with status '{$previousStatus}'.",
            ]);
        }

        // CHANGE THIS LINE:
        // It was 'pending', which is why it looked like it wasn't updating.
        $request->update(['status' => 'accepted']);
        $this->broadcastStatusUpdated($request, $previousStatus);
        $this->auditService->logStatusChange($request, 'approve', $previousStatus, 'accepted');

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

        if (!$this->statusTransitionService->canTransition($previousStatus, 'fulfilled')) {
            return back()->withErrors([
                'status' => "Cannot finalize a request with status '{$previousStatus}'.",
            ]);
        }

        // This is the "Finalize" action
        $request->update(['status' => 'fulfilled']);
        $this->broadcastStatusUpdated($request, $previousStatus);
        $this->auditService->logStatusChange($request, 'fulfill', $previousStatus, 'fulfilled');

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

        if (!$this->statusTransitionService->canTransition($previousStatus, 'cancelled')) {
            return back()->withErrors([
                'status' => "Cannot cancel a request with status '{$previousStatus}'.",
            ]);
        }

        $request->update(['status' => 'cancelled']);
        $this->broadcastStatusUpdated($request, $previousStatus);
        $this->auditService->logStatusChange($request, 'cancel', $previousStatus, 'cancelled');

        if ($request->user) {
            $request->user->notify(new BloodRequestStatusChanged(
                $request,
                'Blood Request Cancelled',
                'Your blood request has been cancelled by the hospital.'
            ));
        }

        return back()->with('success', "Blood request #{$id} has been cancelled.");
    }

    /**
     * Decline a blood request.
     */
    public function decline($id)
    {
        $request = BloodRequest::findOrFail($id);

        $this->authorizeRequestOwner($request);

        if (!$this->statusTransitionService->canTransition((string) $request->status, 'declined')) {
            return back()->withErrors([
                'decline' => "Cannot decline a request with status '{$request->status}'.",
            ]);
        }

        $previousStatus = (string) $request->status;
        $request->update(['status' => 'declined']);
        $this->broadcastStatusUpdated($request, $previousStatus);

        try {
            $this->auditService->logStatusChange($request, 'decline', $previousStatus, 'declined');
        } catch (\Throwable $exception) {
            report($exception);
        }

        if ($request->user) {
            try {
                $request->user->notify(new BloodRequestStatusChanged(
                    $request,
                    'Blood Request Declined',
                    'Your blood request has been declined by the hospital.'
                ));
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        return back()->with('success', "Blood request #{$id} has been declined.");
    }

    /**
     * Update urgency level (priority) of an active blood request.
     */
    public function updatePriority(\Illuminate\Http\Request $request, $id)
    {
        $bloodRequest = BloodRequest::findOrFail($id);
        $this->authorizeRequestOwner($bloodRequest);

        if (!$this->statusTransitionService->canChangePriority((string) $bloodRequest->status)) {
            return back()->withErrors([
                'priority' => 'Priority can only be changed for pending or accepted requests.',
            ]);
        }

        $validated = $request->validate([
            'urgency' => 'required|in:Emergency,High,Normal',
        ]);

        $fromUrgency = (string) $bloodRequest->urgency;
        $toUrgency = (string) $validated['urgency'];

        if ($fromUrgency === $toUrgency) {
            return back()->with('info', 'Priority is already set to that level.');
        }

        $bloodRequest->update(['urgency' => $toUrgency]);
        event(new BloodRequestPriorityUpdated($bloodRequest->fresh(['user']), $fromUrgency));
        $this->auditService->logPriorityChange($bloodRequest, $fromUrgency, $toUrgency);

        return back()->with('success', "Priority updated from {$fromUrgency} to {$toUrgency}.");
    }

    private function broadcastStatusUpdated(BloodRequest $request, string $previousStatus): void
    {
        try {
            event(new BloodRequestStatusUpdated($request->fresh(['user']), $previousStatus));
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    /**
     * Notify a single compatible donor
     */
    public function notify(BloodRequest $request, User $donor)
    {
        $this->authorizeRequestOwner($request);

        // Make sure $request is passed here!
        $donor->notify(new BloodRequestNotification($request));

        return back()->with('success', "Notification sent to {$donor->name}");
    }

    /**
     * Bulk notify all compatible donors
     */
    public function bulkNotify(BloodRequest $request)
    {
        $this->authorizeRequestOwner($request);

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
