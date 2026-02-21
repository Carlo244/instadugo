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
        // If you want only donations for this hospital, filter by hospital_admin_id
        $hospitalId = auth()->id(); // assuming hospital admin's ID matches donations
        $donations = Donation::with('user', 'hospitalAdmin')
            ->orderBy('donation_date', 'desc')
            ->orderBy('donation_time', 'desc')
            ->get();

        return view('hospital.donations.index', compact('donations'));
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
