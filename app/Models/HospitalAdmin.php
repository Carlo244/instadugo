<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class HospitalAdmin extends Authenticatable
{
    protected $fillable = [
        'hospital_name',
        'email',
        'password',
        'contact',
        'address',
        'phlebotomist_count'
    ];

    protected $hidden = ['password'];

    public function getRememberTokenName()
    {
        return '';
    }

    public function bloodRequests()
    {
        return $this->hasMany(BloodRequest::class, 'hospital_admin_id');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class, 'hospital_admin_id');
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    protected $table = 'hospital_admins';
}

