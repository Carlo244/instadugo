<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Http\Request;

class HospitalDonationController extends Controller
{
    /**
     * Display all donations for the hospital admin
     */
    public function index()
    {
        $hospitalId = auth()->id();
        $today = now()->toDateString();

        $query = Donation::with('user')->where('hospital_admin_id', $hospitalId);

        return view('hospital.donations', [
            // 1. Scheduled for today
            'todayQueue' => (clone $query)->whereDate('donation_date', $today)->where('status', 'scheduled')->orderBy('donation_time', 'asc')->get(),
            // 2. Scheduled for future dates
            'upcoming' => (clone $query)->whereDate('donation_date', '>', $today)->where('status', 'scheduled')->orderBy('donation_date', 'asc')->get(),
            // 3. Any completed or cancelled
            'history' => (clone $query)
                ->whereIn('status', ['completed', 'cancelled'])
                ->latest()
                ->get(),
        ]);
    }

    /**
     * Mark a donation as completed
     */
    public function complete($id)
    {
        $donation = Donation::findOrFail($id);

        $donation->update([
            'status' => 'completed',
        ]);

        return back()->with('success', "Donation #{$id} marked as completed.");
    }

    /**
     * Cancel a donation
     */
    public function cancel($id)
    {
        $donation = Donation::findOrFail($id);

        $donation->update([
            'status' => 'cancelled',
        ]);

        return back()->with('success', "Donation #{$id} has been cancelled.");
    }

    /**
     * Optional: Show detailed info for a specific donation
     */
    public function show($id)
    {
        $donation = Donation::with('user', 'hospitalAdmin')->findOrFail($id);

        return view('hospital.donations.show', compact('donation'));
    }
}
