<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodRequestActionAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'blood_request_id',
        'hospital_admin_id',
        'action',
        'from_status',
        'to_status',
        'from_urgency',
        'to_urgency',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function bloodRequest()
    {
        return $this->belongsTo(BloodRequest::class);
    }

    public function hospitalAdmin()
    {
        return $this->belongsTo(HospitalAdmin::class);
    }
}
