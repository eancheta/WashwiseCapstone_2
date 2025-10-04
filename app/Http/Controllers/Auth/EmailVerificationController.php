<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EmailVerificationController extends Controller
{

public function show(Request $request)
{
    // prefer explicit query param over relying on flash
    $email = $request->query('email', '');

    return Inertia::render('EmailVerificationCode', [
        'email' => $email,
    ]);
}
public function verify(Request $request)
{
    $request->validate([
        'code' => 'required|string',
    ]);

    $user = Auth::user();

    if ($user->verification_code === $request->input('code')) {
        $user->email_verified_at = now();
        $user->verification_code = null;
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Email verified successfully.');
    }

    return back()->withErrors(['code' => 'Invalid verification code.']);
}
}

