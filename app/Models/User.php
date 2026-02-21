<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Donation;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'email', 'password', 'contact', 'age', 'sex', 'blood_type', 'address'];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be cast.
     */
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
        // Get the latest completed donation
        $lastDonation = $this->donations()
            ->where('status', 'completed') // only consider completed donations
            ->orderBy('donation_date', 'desc')
            ->orderBy('donation_time', 'desc')
            ->first();

        if ($lastDonation) {
            // Start with the date, then specifically set the time from the time column
            $donationDateTime = \Carbon\Carbon::parse($lastDonation->donation_date)->setTimeFromTimeString($lastDonation->donation_time);
            // Check 8-week (56 days) rule
            $daysSinceLast = now()->diffInDays($donationDateTime);
            if ($daysSinceLast < 56) {
                return false;
            }
        }

        return true;
    }
}
