<?php

namespace App\Http\Controllers\Hospital;

use App\Events\DonationStatusUpdated;
use App\Http\Controllers\Controller;
use App\Models\Donation;

class HospitalDonationController extends Controller
{
    /**
     * Display all donations for the hospital admin
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $hospitalId = auth('hospital_admin')->id();
        $today = now()->toDateString();

        $query = Donation::with('user')->where('hospital_admin_id', $hospitalId);
        
        // Apply blood type filter
        if ($request->has('blood_type')) {
            $query->where('blood_type', $request->get('blood_type'));
        }
        
        // Apply status filter
        if ($request->has('status_filter')) {
            $query->where('status', $request->get('status_filter'));
        }
        
        // If this is an AJAX partial request, return the requested partial only
        if ($request->boolean('ajax')) {
            $tab = $request->get('tab', 'today');
            if ($tab === 'today') {
                $donations = (clone $query)->whereDate('donation_date', $today)->where('status', 'scheduled')->orderBy('donation_time', 'asc')->paginate(15);
                $showActions = true;
            } elseif ($tab === 'upcoming') {
                $donations = (clone $query)->whereDate('donation_date', '>', $today)->where('status', 'scheduled')->orderBy('donation_date', 'asc')->paginate(15);
                $showActions = false;
            } else { // history
                $donations = (clone $query)->whereIn('status', ['completed', 'cancelled'])->latest()->paginate(15);
                $showActions = false;
            }

            return view('partials.hospital-donations-table', [
                'donations' => $donations,
                'showActions' => $showActions,
            ]);
        }

        return view('hospital.donations', [
            // 1. Scheduled for today
            'todayQueue' => (clone $query)->whereDate('donation_date', $today)->where('status', 'scheduled')->orderBy('donation_time', 'asc')->paginate(15, ['*'], 'today_page'),
            // 2. Scheduled for future dates
            'upcoming' => (clone $query)->whereDate('donation_date', '>', $today)->where('status', 'scheduled')->orderBy('donation_date', 'asc')->paginate(15, ['*'], 'upcoming_page'),
            // 3. Any completed or cancelled
            'history' => (clone $query)
                ->whereIn('status', ['completed', 'cancelled'])
                ->latest()
                ->paginate(15, ['*'], 'history_page'),
            // Get phlebotomist count for the hospital
            'phlebotomistCount' => auth()->user()->phlebotomist_count ?? 1,
        ]);
    }

    /**
     * Mark a donation as completed
     */
    public function complete($id)
    {
        $donation = Donation::findOrFail($id);
        $fromStatus = (string) $donation->status;

        $donation->update([
            'status' => 'completed',
        ]);

        event(new DonationStatusUpdated($donation->fresh('user'), $fromStatus, 'completed'));

        return back()->with('success', "Donation #{$id} marked as completed.");
    }

    /**
     * Cancel a donation
     */
    public function cancel($id)
    {
        $donation = Donation::findOrFail($id);
        $fromStatus = (string) $donation->status;

        $donation->update([
            'status' => 'cancelled',
        ]);

        event(new DonationStatusUpdated($donation->fresh('user'), $fromStatus, 'cancelled'));

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
