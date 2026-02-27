<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
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

        // Check if the "remember" checkbox was ticked
        $remember = $request->has('remember');

        // Try login as normal user (donor) - Added $remember here
        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->route('user.dashboard');
        }

        // Try login as hospital admin - Added $remember here
        if (Auth::guard('hospital_admin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->route('hospital.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ]);
    }

    public function logout(Request $request)
    {
        // Logout from both guards safely
        if (Auth::guard('hospital_admin')->check()) {
            Auth::guard('hospital_admin')->logout();
        }

        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        // Destroy session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
