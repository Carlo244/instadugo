<?php

namespace App\Providers;

use App\Events\BloodRequestCreated;
use App\Events\BloodRequestStatusUpdated;
use App\Events\DonationCreated;
use App\Events\DonationStatusUpdated;
use App\Listeners\CreateBloodRequestNotification;
use App\Listeners\CreateBloodRequestStatusNotification;
use App\Listeners\CreateDonationNotification;
use App\Listeners\CreateDonationStatusNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        BloodRequestCreated::class => [
            CreateBloodRequestNotification::class,
        ],
        BloodRequestStatusUpdated::class => [
            CreateBloodRequestStatusNotification::class,
        ],
        DonationCreated::class => [
            CreateDonationNotification::class,
        ],
        DonationStatusUpdated::class => [
            CreateDonationStatusNotification::class,
        ],
    ];
}