<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodRequest extends Model
{
    use HasFactory;

    protected $table = 'blood_requests';

    protected $fillable = [
        'user_id', 
        'blood_type', 
        'quantity', 
        'urgency', 
        'hospital_admin_id', 
        'date_needed',
        'reason', 
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hospitalAdmin()
    {
        return $this->belongsTo(HospitalAdmin::class);
    }

    public function getMatchedDonorsAttribute()
{
    $compatibility = [
        'A+' => ['A+', 'A-', 'O+', 'O-'],
        'A-' => ['A-', 'O-'],
        'B+' => ['B+', 'B-', 'O+', 'O-'],
        'B-' => ['B-', 'O-'],
        'AB+' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'],
        'AB-' => ['A-', 'B-', 'AB-', 'O-'],
        'O+' => ['O+', 'O-'],
        'O-' => ['O-'],
    ];

    $allowedTypes = $compatibility[$this->blood_type] ?? [];

    return User::whereIn('blood_type', $allowedTypes)
        ->where('id', '!=', $this->user_id)
        ->get();
}
}
