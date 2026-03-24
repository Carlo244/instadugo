<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Hospital\HospitalBloodRequestController;
use App\Http\Controllers\Hospital\HospitalDashboardController;
use App\Http\Controllers\Hospital\HospitalDonationController;
use App\Http\Controllers\Hospital\HospitalManageUsersController;
use App\Http\Controllers\Hospital\HospitalProfileController;
use App\Http\Controllers\Hospital\HospitalReportsController;
use App\Http\Controllers\User\UserBloodRequestController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\UserDonationController;
use App\Http\Controllers\User\UserProfileController;
use App\Models\BloodRequest;
use App\Models\HospitalAdmin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    $landingStats = [
        'fulfilled_requests' => BloodRequest::where('status', 'fulfilled')->count(),
        'successful_matches' => BloodRequest::whereIn('status', ['accepted', 'fulfilled'])->count(),
        'active_donors' => User::count(),
        'partners' => HospitalAdmin::count(),
    ];

    return view('landingpage', compact('landingStats'));
});
Broadcast::routes();

// Auth
Auth::routes(['verify' => true]);

// Guest Registration/Login
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Home
Route::get('/home', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:web', 'verified'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        // Dashboard
        Route::get('dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

        // Profile
        Route::get('profile', [UserProfileController::class, 'index'])->name('profile');

        Route::match(['post', 'put'], 'profile/update', [UserProfileController::class, 'update'])->name('profile.update');

        // Blood Requests
        Route::get('blood-requests', [UserBloodRequestController::class, 'userIndex'])->name('blood-requests');

        Route::post('blood-requests', [UserBloodRequestController::class, 'store'])->name('blood-requests.store');

        // Donate & Schedule
        Route::get('donate-schedule', [UserDonationController::class, 'index'])->name('donate-schedule');

        Route::post('donate-schedule', [UserDonationController::class, 'store'])->name('donate-schedule.store');

        // Respond to blood request
        Route::post('donate-schedule/respond/{bloodRequestId}', [UserDonationController::class, 'respond'])->name('donate-schedule.respond');

        // AJAX: occupied times
        Route::get('donations/occupied-times', [UserDonationController::class, 'getOccupiedTimes'])->name('donate-schedule.occupied-times');

        // Cancel donation
        Route::patch('donations/{donation}/cancel', [UserDonationController::class, 'cancel'])->name('donations.cancel');

        //Notify
        Route::post('notifications/mark-all-read', function () {
            auth()->user()->unreadNotifications->markAsRead();
            return back();
        })->name('notifications.markAllRead');

        Route::post('notify-donor', [UserDashboardController::class, 'notifyDonor'])->name('notify-donor');

        Route::post('send-donor-request', [UserDashboardController::class, 'sendDonorRequest'])->name('send-donor-request');

        Route::get('invitations', [UserDashboardController::class, 'invitations'])->name('invitations');
        Route::get('requests/{id}', [UserDashboardController::class, 'showRequest'])->name('requests.show');

    });
/*
|--------------------------------------------------------------------------
| Hospital Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('hospital')
    ->name('hospital.')
    ->middleware(['auth:hospital_admin'])
    ->group(function () {
        // Dashboard
        Route::get('dashboard', [HospitalDashboardController::class, 'index'])->name('dashboard');

        // Manage Users
        Route::get('manageusers', [HospitalManageUsersController::class, 'index'])->name('manageusers');
        Route::get('manageusers/create', [HospitalManageUsersController::class, 'create'])->name('manageusers.create');
        Route::post('manageusers', [HospitalManageUsersController::class, 'store'])->name('manageusers.store');

        // Profile
        Route::get('profile', [HospitalProfileController::class, 'index'])->name('profile');
        Route::match(['post', 'put'], 'profile/update', [HospitalProfileController::class, 'update'])->name('profile.update');

        // Blood Requests
        Route::get('requests', [HospitalBloodRequestController::class, 'index'])->name('requests');
        Route::patch('update-phlebotomist', [HospitalBloodRequestController::class, 'updatePhlebotomist'])->name('update-phlebotomist');
        Route::post('requests/{id}/approve', [HospitalBloodRequestController::class, 'approve'])->name('requests.approve');
        Route::post('requests/{id}/fulfill', [HospitalBloodRequestController::class, 'fulfill'])->name('requests.fulfill');
        Route::post('requests/{id}/cancel', [HospitalBloodRequestController::class, 'cancel'])->name('requests.cancel');
        Route::post('requests/{request}/notify/{donor}', [HospitalBloodRequestController::class, 'notify'])->name('request.notify');
        Route::post('requests/{request}/notify-all', [HospitalBloodRequestController::class, 'bulkNotify'])->name('request.bulk');

        // Donations
        Route::get('donations', [HospitalDonationController::class, 'index'])->name('donations');
        Route::get('donations/{id}', [HospitalDonationController::class, 'show'])->name('donations.show');
        Route::post('donations/{id}/complete', [HospitalDonationController::class, 'complete'])->name('donations.complete');
        Route::post('donations/{id}/cancel', [HospitalDonationController::class, 'cancel'])->name('donations.cancel');


         Route::get('reports', [HospitalReportsController::class, 'index'])->name('reports');
    });

Route::post('/notifications/mark-all-read', function () {
    auth()->user()->unreadNotifications->markAsRead();
    return back();
})
    ->name('notifications.markAllRead')
    ->middleware('auth');
