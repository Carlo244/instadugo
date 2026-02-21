<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\LoginController;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        return redirect()->action([LoginController::class, 'login']);
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    protected function redirectTo()
    {
        // Redirect verified users to dashboard
        return route('user.dashboard');
    }
}
