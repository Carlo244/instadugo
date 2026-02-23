<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use App\Models\User; // Your donors
use App\Models\Donation;
use Carbon\Carbon;

class HospitalMatchingController extends Controller
{
    public function index()
    {
        // Use the ID of the logged-in Hospital Admin
        $hospitalId = auth()->id(); 

        // 1. Get Pending/Approved Requests for THIS Hospital
        $requests = BloodRequest::with('user')
            ->where('hospital_admin_id', $hospitalId)
            ->whereIn('status', ['pending', 'approved'])
            ->orderBy('urgency', 'desc')
            ->get();

        // 2. Standard Compatibility Matrix
        $compatibilityMatrix = [
            'O-'  => ['O-'],
            'O+'  => ['O+', 'O-'],
            'A-'  => ['A-', 'O-'],
            'A+'  => ['A+', 'A-', 'O+', 'O-'],
            'B-'  => ['B-', 'O-'],
            'B+'  => ['B+', 'B-', 'O+', 'O-'],
            'AB-' => ['AB-', 'A-', 'B-', 'O-'],
            'AB+' => ['O-', 'O+', 'A-', 'A+', 'B-', 'B+', 'AB-', 'AB+'],
        ];

        // 3. Get ALL Users (Donors) and filter by Eligibility (56-day rule)
        $donors = User::all()->filter(function($user) {
            $lastDonation = Donation::where('user_id', $user->id)
                ->where('status', 'completed')
                ->latest('donation_date')
                ->first();
                
            if (!$lastDonation) return true;

            // Use Carbon to check if 56 days have passed
            return Carbon::parse($lastDonation->donation_date)->addDays(56)->isPast();
        });

        return view('hospital.matching', compact('requests', 'compatibilityMatrix', 'donors'));
    }
}
