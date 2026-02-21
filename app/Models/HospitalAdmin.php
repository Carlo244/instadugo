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
        'address'
    ];

    protected $hidden = ['password'];

    public function getRememberTokenName()
    {
        return ''; 
    }
    protected $table = 'hospital_admins';
}

