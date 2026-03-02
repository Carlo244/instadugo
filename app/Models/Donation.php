<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

protected $fillable = [
        'user_id',
        'hospital_admin_id',
        'blood_request_id', 
        'blood_type',
        'donation_date',
        'donation_time',
        'status',
        'notes',
    ];


    public function bloodRequest()
    {
        return $this->belongsTo(BloodRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hospitalAdmin()
    {
        return $this->belongsTo(HospitalAdmin::class);
    }

    protected $casts = [
        'donation_date' => 'date',
    ];
}
