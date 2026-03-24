<?php

namespace App\Http\Controllers\user;

use App\Events\DonationStatusUpdated;
use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\HospitalAdmin;
use App\Notifications\DonorFoundNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDonationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userId = Auth::id();

        // 1. Get user's donations
        $donations = Donation::where('user_id', $userId)->orderBy('donation_date', 'desc')->orderBy('donation_time', 'desc')->get();

        // 2. Calculate eligibility
        $lastCompleted = Donation::where('user_id', $userId)->where('status', 'completed')->latest('donation_date')->first();

        $isEligible = true;
        $nextEligibleDate = null;

        if ($lastCompleted) {
            $lastDate = Carbon::parse($lastCompleted->donation_date)->setTimeFromTimeString($lastCompleted->donation_time);
            $nextDate = $lastDate->copy()->addDays(56);
            $isEligible = now()->greaterThanOrEqualTo($nextDate);
            $nextEligibleDate = $nextDate->format('M d, Y');
        }

        $activeScheduledDonation = Donation::where('user_id', $userId)
            ->where('status', 'scheduled')
            ->orderBy('donation_date', 'asc')
            ->orderBy('donation_time', 'asc')
            ->first();

        $hasActiveSchedule = (bool) $activeScheduledDonation;

        $hospitals = HospitalAdmin::all();

        // Removed compatibleRequests from the return
        return view('user.donate-schedule', compact('donations', 'hospitals', 'isEligible', 'nextEligibleDate', 'hasActiveSchedule', 'activeScheduledDonation'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hospital_admin_id' => 'required|exists:hospital_admins,id',
            'donation_date' => 'required|date|after_or_equal:today',
            'donation_time' => 'required',
            'notes' => 'nullable|string|max:500',
        ]);

        $hasActiveSchedule = Donation::where('user_id', auth()->id())
            ->where('status', 'scheduled')
            ->exists();

        if ($hasActiveSchedule) {
            return back()
                ->withErrors(['donation_time' => 'You already have a scheduled donation. Please complete or cancel it before booking a new one.'])
                ->withInput();
        }

        $scheduledDateTime = \Carbon\Carbon::parse($request->donation_date . ' ' . $request->donation_time);

        if ($scheduledDateTime->isPast()) {
            return back()
                ->withErrors(['donation_time' => 'You cannot schedule a donation in the past.'])
                ->withInput();
        }

        // Check capacity based on phlebotomist count
        $hospital = HospitalAdmin::findOrFail($request->hospital_admin_id);
        $phlebotomistCount = $hospital->phlebotomist_count ?? 1;

        $bookedSlots = Donation::where('hospital_admin_id', $request->hospital_admin_id)
            ->where('donation_date', $request->donation_date)
            ->where('donation_time', $scheduledDateTime->format('H:i:s'))
            ->where('status', 'scheduled')
            ->count();

        if ($bookedSlots >= $phlebotomistCount) {
            return back()
                ->withErrors(['donation_time' => "This time slot is full. Only {$phlebotomistCount} donor(s) can be scheduled at this time."])
                ->withInput();
        }

        // Create donation
        $donation = Donation::create([
            'user_id' => auth()->id(),
            'hospital_admin_id' => $request->hospital_admin_id,
            'donation_date' => $request->donation_date,
            'donation_time' => $scheduledDateTime->format('H:i:s'),
            'blood_type' => auth()->user()->blood_type,
            'notes' => $request->notes,
            'status' => 'scheduled',
        ]);

        // 🔴 Fire event immediately for real-time notification
        event(new \App\Events\DonationCreated($donation));

        return back()->with('success', 'Donation scheduled successfully.');
    }

    public function respond(Request $request, $bloodRequestId)
    {
        // 1. Define initial variables
        $donor = auth()->user();
        $action = $request->input('action');

        // Find the specific request being addressed
        $bloodRequest = BloodRequest::findOrFail($bloodRequestId);

        // 2. Handle Acceptance Logic
        if ($action === 'accept') {
            // Validation
            $validated = $request->validate([
                'donation_date' => 'required|date|after_or_equal:today',
                'donation_time' => 'required',
                'hospital_admin_id' => 'required|exists:hospital_admins,id',
            ]);

            $hasActiveSchedule = Donation::where('user_id', $donor->id)
                ->where('status', 'scheduled')
                ->exists();

            if ($hasActiveSchedule) {
                return back()
                    ->withErrors(['donation_time' => 'You already have a scheduled donation. Please complete or cancel it before accepting another schedule.'])
                    ->withInput();
            }

            // Get Hospital details for the response if needed
            $hospital = HospitalAdmin::findOrFail($validated['hospital_admin_id']);

            // Check capacity based on phlebotomist count
            $phlebotomistCount = $hospital->phlebotomist_count ?? 1;
            $bookedSlots = Donation::where('hospital_admin_id', $validated['hospital_admin_id'])
                ->where('donation_date', $validated['donation_date'])
                ->where('donation_time', $validated['donation_time'])
                ->where('status', 'scheduled')
                ->count();

            if ($bookedSlots >= $phlebotomistCount) {
                return back()
                    ->withErrors(['donation_time' => "This time slot is full. Only {$phlebotomistCount} donor(s) can be scheduled at this time."])
                    ->withInput();
            }

            // 3. Create Donation Record (Link the donor to the request)
            $donation = Donation::create([
                'user_id' => $donor->id,
                'hospital_admin_id' => $validated['hospital_admin_id'],
                'blood_request_id' => $bloodRequest->id,
                'blood_type' => $donor->blood_type,
                'donation_date' => $validated['donation_date'],
                'donation_time' => $validated['donation_time'],
                'status' => 'scheduled',
            ]);

            // 4. Update the Blood Request status
            $bloodRequest->update(['status' => 'accepted']);

            // 5. Notify the Original Requester
            // We notify the user who created the blood request ($bloodRequest->user)
            $bloodRequest->user->notify(new DonorFoundNotification($donation));

            return back()->with('success', 'You have successfully scheduled your donation at ' . $hospital->hospital_name . '!');
        }

        // 6. Handle Decline Logic
        $bloodRequest->update(['status' => 'declined']);

        return back()->with('info', 'Invitation declined.');
    }

    /**
     * Fetch available donation times for a hospital based on phlebotomist capacity
     */
    public function getOccupiedTimes(Request $request)
    {
        $hospital = HospitalAdmin::find($request->hospital_id);
        $phlebotomistCount = $hospital->phlebotomist_count ?? 1;

        // Get all time slots that are fully booked
        $fullyBooked = Donation::where('hospital_admin_id', $request->hospital_id)
            ->where('donation_date', $request->date)
            ->where('status', 'scheduled')
            ->select('donation_time')
            ->groupBy('donation_time')
            ->havingRaw('count(*) >= ?', [$phlebotomistCount])
            ->pluck('donation_time')
            ->toArray();

        return response()->json($fullyBooked);
    }

    /**
     * Cancel a scheduled donation
     */
    public function cancel(Donation $donation)
    {
        if ($donation->user_id !== Auth::id()) {
            abort(403);
        }

        if ($donation->status === 'scheduled') {
            $fromStatus = (string) $donation->status;
            $donation->update(['status' => 'cancelled']);
            event(new DonationStatusUpdated($donation->fresh('user'), $fromStatus, 'cancelled'));
            return back()->with('success', 'Schedule cancelled successfully.');
        }

        return back()->with('error', 'This schedule cannot be cancelled.');
    }
}
