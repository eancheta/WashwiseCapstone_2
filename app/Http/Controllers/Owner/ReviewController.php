<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\CarWashShop;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        // ✅ Get all shops owned by the logged-in user with feedback + user info
        $shops = CarWashShop::where('owner_id', Auth::id())
            ->with(['feedback.user'])
            ->get();

        return view('owner.reviews', compact('shops'));
    }
}
