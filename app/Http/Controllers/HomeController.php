<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web,hospital_admin');
    }

    public function index()
    {
        // 1. Check if it's a Hospital Admin
        if (Auth::guard('hospital_admin')->check()) {
            return redirect()->route('hospital.dashboard');
        }

        // 2. Check if it's a regular User
        if (Auth::guard('web')->check()) {
            return redirect()->route('user.dashboard');
        }

        // 3. Fallback to login if somehow they got here without a guard
        return redirect()->route('login');
    }
}