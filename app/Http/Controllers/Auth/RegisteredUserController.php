<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

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
     * Handle registration.
     */
    public function store(Request $request)
    {
        // ✅ Validate input
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8|confirmed',
        ]);

        // ✅ Generate verification code
        $verificationCode = (string) rand(100000, 999999);

        // ✅ Create customer user
        $user = User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'password'          => Hash::make($validated['password']),
            'verification_code' => $verificationCode,
        ]);

        // ✅ Send verification email via Brevo API
        try {
            $apiResponse = $this->sendVerificationCode($user->email, $user->name, $verificationCode);

            if (isset($apiResponse['ok']) && $apiResponse['ok']) {
                Log::info('Verification email sent via Brevo', ['email' => $user->email]);
            } else {
                Log::error('Failed to send verification email', [
                    'email' => $user->email,
                    'body'  => $apiResponse,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Brevo API call failed', ['message' => $e->getMessage()]);
        }

        // ✅ Store email in session for verification
        session(['customer_verification_email' => $user->email]);

        // ✅ Redirect to verification page
        return redirect()->route('emailvcode')->with('email', $user->email);
    }

    /**
     * Send email verification via Brevo API.
     */
    private function sendVerificationCode($toEmail, $toName, $code)
    {
        $htmlContent = View::make('emails.verify-code', [
            'name' => $toName,
            'code' => $code,
        ])->render();

        $payload = [
            'sender' => [
                'name'  => env('MAIL_FROM_NAME', 'WashWise'),
                'email' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
            ],
            'to' => [['email' => $toEmail, 'name' => $toName]],
            'subject' => 'WashWise — Your verification code',
            'htmlContent' => $htmlContent,
            'textContent' => "Your verification code is: {$code}",
        ];

        $response = Http::withHeaders([
            'api-key' => env('SENDINBLUE_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.sendinblue.com/v3/smtp/email', $payload);

        return $response->json();
    }

    /**
     * Show login page.
     */
    public function showLogin(): Response
    {
        return Inertia::render('auth/Login');
    }

    /**
     * Handle login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Account not found.'])->withInput();
        }

        if (!$user->email_verified_at) {
            return back()->withErrors(['email' => 'Please verify your email before logging in.'])->withInput();
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    /**
     * Show the email verification code input page.
     */
    public function showVerificationPage(): Response
    {
        $email = session('customer_verification_email');
        return Inertia::render('EmailVerificationCode', [
            'email' => $email,
        ]);
    }

    /**
     * Verify code submission.
     */
    public function verifyCode(Request $request)
    {
        $request->validate(['code' => 'required|numeric']);

        $email = session('customer_verification_email');
        $user = User::where('email', $email)->first();

        if (!$user || $user->verification_code !== $request->code) {
            return back()->withErrors(['code' => 'Invalid verification code.']);
        }

        $user->update([
            'email_verified_at' => now(),
            'verification_code' => null,
        ]);

        return redirect()->route('login')->with('success', 'Email verified successfully. You can now log in.');
    }
}
