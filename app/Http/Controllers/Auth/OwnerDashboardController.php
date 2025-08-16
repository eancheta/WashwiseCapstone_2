<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        $owner = auth()->guard('carwashowner')->user();

        // Only redirect to shop setup if no shop exists yet
        if (!$owner->shop) {
            return redirect()->route('owner.shop.create');
        }

        return inertia('Owner/CarWashOwnerDashboard');
    }
}
