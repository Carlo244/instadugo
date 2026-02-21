<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class BloodRequest extends Model
{
    protected $table = 'blood_requests';

    public function store(Request $request)
    {
        $request->validate([
            'blood_type' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'quantity' => 'required|integer|min:1',
            'hospital_admin_id' => 'required|exists:hospital_admins,id',
            'urgency' => 'required|in:Normal,High,Emergency',
            'reason' => 'required|string',
        ]);

        BloodRequest::create([
            'user_id' => auth()->id(),
            'blood_type' => $request->blood_type,
            'quantity' => $request->quantity,
            'hospital_admin_id' => $request->hospital_admin_id,
            'urgency' => $request->urgency,
            'date_needed' => now(),
            'status' => 'pending',
        ]);

        return back()->with('success', 'Blood request sent successfully.');
    }

    use HasFactory;

    protected $fillable = ['user_id', 'blood_type', 'quantity', 'urgency', 'hospital_admin_id', 'date_needed','reason', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hospitalAdmin()
    {
        return $this->belongsTo(HospitalAdmin::class);
    }
    public function index()
    {
        $requests = BloodRequest::with('user', 'hospitalAdmin')->orderBy('created_at', 'desc')->get();

        return view('hospital.requests', compact('requests'));
    }

    public function compatibleDonors()
    {
        return User::where('blood_type', $this->blood_type)->get();
    }
}
