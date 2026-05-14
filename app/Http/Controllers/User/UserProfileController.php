<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
            'age' => 'nullable|integer',
            'sex' => 'nullable|string',
            'address' => 'nullable|string',
            'current_password' => 'required_with:password',
            'password' => 'nullable|confirmed|min:8',
        ]);

        if ($request->filled('password')) {
            if (!Hash::check((string) $request->input('current_password'), (string) $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
            }
            $data['password'] = bcrypt($request->password);
        } else {
            unset($data['password']);
        }

        unset($data['current_password']);

        $user->update($data);

        return back()->with('success', 'Profile updated successfully!');
    }
}
