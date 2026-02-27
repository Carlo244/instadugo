<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class HospitalManageUsersController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        // Fetch only donors and staff if you want to exclude admins
        // Or simply fetch all users related to this hospital system
        $users = User::latest()->paginate(10);

        return view('hospital.manageusers', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('hospital.users.create');
    }

    // You can add store, edit, update, and destroy methods here later
}
