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

    public function bloodRequests()
    {
        return $this->hasMany(BloodRequest::class, 'user_id');
    }

    public function receivedBloodRequests()
    {
        return $this->hasMany(BloodRequest::class, 'receiver_id');
    }

    public function isEligible()
    {
        // We only need the date for the 56-day rule usually
        $lastDonation = $this->donations()
            ->where('status', 'completed')
            ->latest('donation_date') // latest() is shorthand for orderBy desc
            ->first();

        if ($lastDonation) {
            $nextDate = \Carbon\Carbon::parse($lastDonation->donation_date)->addDays(56);
            // Use isPast() or greaterThan to see if we've reached that day
            return now()->startOfDay()->greaterThanOrEqualTo($nextDate->startOfDay());
        }

        return true;
    }

    public function nextEligibleDate()
    {
        $lastDonation = $this->donations()->where('status', 'completed')->latest('donation_date')->first();

        if (!$lastDonation) {
            return now()->startOfDay();
        }

        return \Carbon\Carbon::parse($lastDonation->donation_date)->addDays(56)->startOfDay();
    }

    public function daysUntilEligible()
    {
        $nextDate = $this->nextEligibleDate();

        if (now()->startOfDay()->greaterThanOrEqualTo($nextDate)) {
            return 0;
        }

        // diffInDays on startOfDay gives you the exact number of midnights between now and then
        return (int) now()->startOfDay()->diffInDays($nextDate);
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
