<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;


class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request)
{
    // Validate input once
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    // Generate verification code
    $verificationCode = random_int(100000, 999999);

    // Create user with verification code
    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'verification_code' => $verificationCode,
    ]);

    // Send verification email
    try {
        Mail::to($user->email)->send(new VerificationCodeMail($verificationCode, $user->name));
    } catch (\Exception $e) {
        Log::error('Verification email failed: ' . $e->getMessage());

        return redirect()->back()->withErrors([
            'email' => 'Failed to send verification email. Please try again.',
        ]);
    }

    // Log in the user
    event(new Registered($user));
    Auth::login($user);

    return redirect()->route('emailvcode')->with('email', $user->email);
}
}
