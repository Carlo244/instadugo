<?php

namespace App\Models;

use App\Models\Donation;
use App\Notifications\CustomResetPassword;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'contact', 'age', 'sex', 'blood_type', 'address'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function isEligible()
    {
        $lastDonation = $this->donations()->where('status', 'completed')->orderBy('donation_date', 'desc')->orderBy('donation_time', 'desc')->first();

        if ($lastDonation) {
            $donationDateTime = \Carbon\Carbon::parse($lastDonation->donation_date)->setTimeFromTimeString($lastDonation->donation_time);
            $daysSinceLast = now()->diffInDays($donationDateTime);
            if ($daysSinceLast < 56) {
                return false;
            }
        }
        return true;
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail());
    }

    public function sendPasswordResetNotification($token)
{
    $this->notify(new CustomResetPassword($token));
}
}
