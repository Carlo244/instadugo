<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    // In AppServiceProvider.php
    public function boot(): void
    {
        // Only run this query when 'layouts.user-sidebar' or 'layouts.app' is loaded
        View::composer(['layouts.user-sidebar', 'layouts.app'], function ($view) {
            if (Auth::check()) {
                // Using a limit and specific columns is faster
               $notifications = Auth::user()->unreadNotifications()->take(10)->get();
                $view->with('globalNotifications', $notifications);
            }
        });
    }
}
