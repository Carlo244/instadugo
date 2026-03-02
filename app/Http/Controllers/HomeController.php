<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        // This ensures only logged-in users (of either guard) can hit the index
        $this->middleware('auth:web,hospital_admin');
    }

    public function index()
    {
        // 1. Check for User (Web Guard) first 
        // Tip: Checking the 'web' guard first helps if you are trying to register/login as a user
        if (Auth::guard('web')->check()) {
            return redirect()->route('user.dashboard');
        }

        // 2. Check for Hospital Admin
        if (Auth::guard('hospital_admin')->check()) {
            return redirect()->route('hospital.dashboard');
        }

        // 3. Emergency Logout & Redirect
        // If they have a session but neither guard matches (rare), clear everything.
        Auth::logout();
        return redirect()->route('login')->with('error', 'Session invalid. Please login again.');
    }
}