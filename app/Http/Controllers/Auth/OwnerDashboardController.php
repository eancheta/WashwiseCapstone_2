<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        return inertia('Owner/CarWashOwnerDashboard');
    }
}
