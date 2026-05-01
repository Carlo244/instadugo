<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class HospitalProfileController extends Controller
{
    public function index()
    {
        $hospital = auth('hospital_admin')->user();
        return view('hospital.profile', compact('hospital'));
    }

    public function update(Request $request)
    {
        $hospital = auth('hospital_admin')->user();

        $data = $request->validate([
            'hospital_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:hospital_admins,email,' . $hospital->id,
            'contact' => 'nullable|string|max:20',
            'address' => 'required|string|max:500',
            'phlebotomist_count' => 'required|integer|min:1|max:10',
            'current_password' => 'required_with:password',
            'password' => 'nullable|confirmed|min:8',
        ]);

        if ($request->filled('password')) {
            if (!Hash::check((string) $request->input('current_password'), (string) $hospital->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
            }
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        unset($data['current_password']);

        $hospital->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }
}