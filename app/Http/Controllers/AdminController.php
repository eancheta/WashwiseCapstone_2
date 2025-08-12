<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index()
    {
        // Custom session-based authentication check
        if (!Session::get('authenticated')) {
            return redirect()->route('loginAdmin');
        }

        // Fetch all users from the database
        $users = User::all();

        // Pass users to the Inertia view
        return Inertia::render('settings/AdminDashboard', [
            'users' => $users
        ]);
    }
}
