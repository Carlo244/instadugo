<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    // Show profile page
    public function index()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    // Update profile info
    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'contact' => 'nullable|string|max:15',
            'blood_type' => 'nullable|string',
            'age' => 'nullable|integer',
            'sex' => 'nullable|string',
            'address' => 'nullable|string',
            'password' => 'nullable|confirmed|min:6',
        ]);

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return back()->with('success', 'Profile updated successfully!');
    }
}
