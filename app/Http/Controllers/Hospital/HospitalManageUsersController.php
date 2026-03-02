<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class HospitalManageUsersController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::query();
        
        // Apply search filter
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact', 'like', "%{$search}%");
            });
        }
        
        // Apply blood type filter
        if ($request->has('blood_type') && $request->get('blood_type')) {
            $query->where('blood_type', $request->get('blood_type'));
        }
        
        // Note: Eligibility filter removed since it requires checking donations for each user
        // which is too expensive for large datasets. Use client-side filtering in view instead.
        
        // Fetch users with pagination
        $users = $query->latest()->paginate(15);
        
        // Get counts for statistics
        $totalUsers = User::count();
        
        // Count eligible users (users with no completed donations or last donation > 56 days ago)
        // This is expensive but only runs once per page load for the stat card
        $eligibleCount = User::with(['donations' => function($query) {
            $query->where('status', 'completed')->latest('donation_date')->limit(1);
        }])->get()->filter(function($user) {
            return $user->isEligible();
        })->count();

        return view('hospital.manageusers', compact('users', 'totalUsers', 'eligibleCount'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('hospital.users.create');
    }

    // You can add store, edit, update, and destroy methods here later
    public function store(Request $request)
{
    $validated = $request->validate([
        'name'       => 'required|string|max:255',
        'email'      => 'required|string|email|max:255|unique:users',
        'password'   => 'required|string|min:8|confirmed',
        'contact'    => 'required|digits:10',
        'age'        => 'required|integer|min:18',
        'sex'        => 'required|in:Male,Female',
        'blood_type' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
        'address'    => 'required|string|max:500',
    ], [
        'contact.digits'    => 'Contact number must be exactly 10 digits (without +63).',
        'age.min'           => 'You must be at least 18 years old.',
        'sex.in'            => 'Please select a valid sex (Male or Female).',
        'blood_type.in'     => 'Please select a valid blood type.',
    ]);

    User::create([
        'name'       => $validated['name'],
        'email'      => $validated['email'],
        'password'   => bcrypt($validated['password']),
        'contact'    => $validated['contact'],
        'age'        => $validated['age'],
        'sex'        => $validated['sex'],
        'blood_type' => $validated['blood_type'],
        'address'    => $validated['address'],
    ]);

    return redirect()->route('hospital.manageusers')->with('success', 'User created successfully.');
}
}
