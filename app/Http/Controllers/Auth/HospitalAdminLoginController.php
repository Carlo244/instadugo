<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HospitalAdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('hospital_admin')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('hospital.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout(Request $request)
    {
        Auth::guard('hospital_admin')->logout();

        // Clear all session data on logout
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
