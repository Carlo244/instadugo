<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\HospitalAdmin;
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
    $donations = Donation::where('user_id', $userId)
        ->orderBy('donation_date', 'desc')
        ->orderBy('donation_time', 'desc')
        ->get();

    // 2. Calculate eligibility
    $lastCompleted = Donation::where('user_id', $userId)
        ->where('status', 'completed')
        ->latest('donation_date')
        ->first();

    $isEligible = true;
    $nextEligibleDate = null;

    if ($lastCompleted) {
        $lastDate = Carbon::parse($lastCompleted->donation_date)
            ->setTimeFromTimeString($lastCompleted->donation_time);
        $nextDate = $lastDate->copy()->addDays(56);
        $isEligible = now()->greaterThanOrEqualTo($nextDate);
        $nextEligibleDate = $nextDate->format('M d, Y');
    }

    $hospitals = HospitalAdmin::all();

    // Removed compatibleRequests from the return
    return view('user.donate-schedule', compact('donations', 'hospitals', 'isEligible', 'nextEligibleDate'));
}

    public function store(Request $request)
    {
        $request->validate([
            'hospital_admin_id' => 'required|exists:hospital_admins,id',
            'donation_date' => 'required|date|after_or_equal:today',
            'donation_time' => 'required',
            'notes' => 'nullable|string|max:500',
        ]);

        // 1. COMBINE DATE AND TIME FIRST
        $scheduledDateTime = Carbon::parse($request->donation_date . ' ' . $request->donation_time);

        // 2. CHECK IF THE TIME HAS ALREADY PASSED (Most important check)
        if ($scheduledDateTime->isPast()) {
            return back()
                ->withErrors(['donation_time' => 'You cannot schedule a donation in the past.'])
                ->withInput();
        }

        $hospitalId = $request->hospital_admin_id;
        $date = $request->donation_date;
        $time = $scheduledDateTime; // Use the parsed carbon instance

        // 3. CONFLICT CHECK (30-min buffer)
        $startTime = $time->copy()->subMinutes(30)->format('H:i:s');
        $endTime = $time->copy()->addMinutes(60)->format('H:i:s');

        $conflict = Donation::where('hospital_admin_id', $hospitalId)
            ->where('donation_date', $date)
            ->whereBetween('donation_time', [$startTime, $endTime])
            ->exists();

        if ($conflict) {
            return back()
                ->withErrors(['donation_time' => 'This time slot is already occupied. Please choose another time.'])
                ->withInput();
        }

        // 4. CREATE AFTER ALL CHECKS PASS
        Donation::create([
            'user_id' => Auth::id(),
            'hospital_admin_id' => $hospitalId,
            'donation_date' => $date,
            'donation_time' => $time->format('H:i:s'),
            'blood_type' => Auth::user()->blood_type,
            'notes' => $request->notes,
            'status' => 'scheduled',
        ]);

        return back()->with('success', 'Donation scheduled successfully.');
    }

    public function respond(Request $request, $bloodRequestId)
    {
        $bloodRequest = BloodRequest::findOrFail($bloodRequestId);

        Donation::create([
            'user_id' => Auth::id(),
            'hospital_admin_id' => $bloodRequest->hospital_admin_id,
            'donation_date' => now(),
            'donation_time' => now()->format('H:i:s'),
            'blood_type' => Auth::user()->blood_type,
            'notes' => 'Responded to blood request #' . $bloodRequest->id,
            'status' => 'scheduled',
        ]);

        return back()->with('success', 'You have agreed to fulfill this blood request.');
    }

    /**
     * Fetch occupied donation times for a hospital
     */
    public function getOccupiedTimes(Request $request)
    {
        $times = Donation::where('hospital_admin_id', $request->hospital_id)->where('donation_date', $request->date)->where('status', 'scheduled')->pluck('donation_time')->toArray();

        return response()->json($times);
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
            $donation->update(['status' => 'cancelled']);
            return back()->with('success', 'Schedule cancelled successfully.');
        }

        return back()->with('error', 'This schedule cannot be cancelled.');
    }
}
