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
                $donations = (clone $query)->whereDate('donation_date', $today)->where('status', 'scheduled')->orderBy('donation_time', 'asc')->get();
                $showActions = true;
            } elseif ($tab === 'upcoming') {
                $donations = (clone $query)->whereDate('donation_date', '>', $today)->where('status', 'scheduled')->orderBy('donation_date', 'asc')->get();
                $showActions = false;
            } else { // history
                $donations = (clone $query)->whereIn('status', ['completed', 'cancelled'])->latest()->get();
                $showActions = false;
            }

            return view('partials.hospital-donations-table', [
                'donations' => $donations,
                'showActions' => $showActions,
            ]);
        }

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
            // Get phlebotomist count for the hospital
            'phlebotomistCount' => auth('hospital_admin')->user()->phlebotomist_count ?? 1,
        ]);
    }

    /**
     * Mark a donation as completed
     */
    public function complete($id)
    {
        $donation = $this->findOwnedDonationOrFail($id);
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
        $donation = $this->findOwnedDonationOrFail($id);
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
        $donation = Donation::with('user', 'hospitalAdmin')
            ->where('hospital_admin_id', auth('hospital_admin')->id())
            ->findOrFail($id);

        return view('hospital.donations.show', compact('donation'));
    }

    /**
     * Ensure donation belongs to the logged-in hospital admin.
     */
    private function findOwnedDonationOrFail($id): Donation
    {
        return Donation::where('hospital_admin_id', auth('hospital_admin')->id())
            ->findOrFail($id);
    }
}
