<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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

    // Block login if customer not yet approved
    if ($user->customer_status !== 'approved') {
        return back()->withErrors(['email' => 'Your account is pending approval by the admin.'])->withInput();
    }

    // Check password
    if (! Hash::check($data['password'], $user->password)) {
        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    // Login approved & verified user
    Auth::login($user);
    $request->session()->regenerate();

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
