<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodRequest extends Model
{
    use HasFactory;

    protected $table = 'blood_requests';

    protected $fillable = ['user_id', 'receiver_id', 'hospital_admin_id', 'blood_type', 'urgency', 'reason', 'status', 'date_needed', 'quantity'];

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

        return User::whereIn('blood_type', $allowedTypes)->where('id', '!=', $this->user_id)->get();
    }
    public function hospital()
    {
        // We specify 'hospital_admin_id' because it doesn't follow
        // the default 'hospital_id' naming convention.
        return $this->belongsTo(HospitalAdmin::class, 'hospital_admin_id');
    }

    public function receiver()
{
    return $this->belongsTo(User::class, 'receiver_id');
}
}
