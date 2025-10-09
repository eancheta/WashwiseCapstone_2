<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CarWashShop;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function create($shopId)
    {
        $shop = CarWashShop::findOrFail($shopId);

        return view('customer.feedback_form', compact('shop'));
    }

public function store(Request $request, $shopId)
{
    $request->validate([
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string|max:1000',
    ]);

    Feedback::create([
        'shop_id' => $shopId,
        'user_id' => Auth::id(),
        'rating'  => $request->rating,
        'comment' => $request->comment ?? ' ', // ✅ store space instead of null
    ]);

    return redirect()
        ->route('customer.feedback.create', $shopId)
        ->with('success', 'Feedback submitted successfully!');
}
}

