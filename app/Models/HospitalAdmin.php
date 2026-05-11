<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class HospitalAdmin extends Authenticatable
{
    use Notifiable;

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

    protected $table = 'hospital_admins';
}

