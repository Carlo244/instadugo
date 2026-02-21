<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Hospital\HospitalBloodRequestController;
use App\Http\Controllers\Hospital\HospitalDashboardController;
use App\Http\Controllers\Hospital\HospitalDonationController;
use App\Http\Controllers\User\UserBloodRequestController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\UserDonationController;
use App\Http\Controllers\User\UserProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => view('landingpage'));

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
Route::middleware(['auth:web'])->prefix('user')->name('user.')->group(function () {
    // Dashboard
    Route::get('dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('profile', [UserProfileController::class, 'index'])->name('profile');
    Route::match(['post', 'put'], 'profile/update', [UserProfileController::class, 'update'])->name('profile.update');

    // Blood Requests
    Route::get('blood-requests', [UserBloodRequestController::class, 'userIndex'])->name('blood-requests');
    Route::post('blood-requests', [UserBloodRequestController::class, 'store'])->name('blood-requests.store');

    // Donations / Scheduling
    Route::get('donate-schedule', [UserDonationController::class, 'index'])->name('donate-schedule');
    Route::post('donate-schedule', [UserDonationController::class, 'store'])->name('donate-schedule.store');

    // Respond to a blood request
    Route::post('donate-schedule/respond/{bloodRequestId}', [UserDonationController::class, 'respond'])->name('donate-schedule.respond');

    // Occupied donation times (AJAX)
    Route::get('donations/occupied-times', [UserDonationController::class, 'getOccupiedTimes'])->name('donate-schedule.occupied-times');

    // Cancel a scheduled donation
    Route::patch('donations/{donation}/cancel', [UserDonationController::class, 'cancel'])->name('donations.cancel');
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

        // Blood Requests
        Route::get('requests', [HospitalBloodRequestController::class, 'index'])->name('requests');
        Route::post('requests/{id}/approve', [HospitalBloodRequestController::class, 'approve'])->name('requests.approve');
        Route::post('requests/{id}/fulfill', [HospitalBloodRequestController::class, 'fulfill'])->name('requests.fulfill');
        Route::post('requests/{id}/cancel', [HospitalBloodRequestController::class, 'cancel'])->name('requests.cancel');

        // Donations
        Route::get('donations', [HospitalDonationController::class, 'index'])->name('donations.index');
        Route::get('donations/{id}', [HospitalDonationController::class, 'show'])->name('donations.show');
        Route::post('donations/{id}/complete', [HospitalDonationController::class, 'complete'])->name('donations.complete');
        Route::post('donations/{id}/cancel', [HospitalDonationController::class, 'cancel'])->name('donations.cancel');
    });
