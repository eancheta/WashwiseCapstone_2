<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Controllers\ShopBookingController;
class CustomerAuthController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('auth/Login');
    }

public function login(Request $request)
{
    $data = $request->validate([
        'email'    => 'required|email',
        'password' => 'required|string',
    ]);

    $user = User::where('email', $data['email'])->first();

    if (! $user) {
        return back()->withErrors(['email' => 'Account not found.'])->withInput();
    }

    // Block login if email not verified
    $status = $user->status === null ? '' : trim(strtolower($user->status));
    if ($status !== 'verified') {
        return back()->withErrors(['email' => 'Your account is not verified. Please check your email.'])->withInput();
    }

    // Check password
    if (! Hash::check($data['password'], $user->password)) {
        return back()->withErrors(['email' => 'Wrong Password or Email'])->withInput();
    }

    // Login approved & verified user
    Auth::login($user);
    $request->session()->regenerate();

        // === Send booking reminders on login ===
    try {
        $controller = new ShopBookingController();
        $controller->sendBookingReminders(); // call it directly
    } catch (\Exception $e) {
        \Log::error("Failed to send reminders on login: " . $e->getMessage());
    }

    return redirect()->intended('/dashboard');
}

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
