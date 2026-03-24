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
            'password' => 'nullable|confirmed|min:6',
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        $hospital->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }
}