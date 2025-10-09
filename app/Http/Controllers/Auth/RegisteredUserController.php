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
     * Handle an incoming registration request.
     */
    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8|confirmed',
        ]);

        // 🔑 Generate verification code
        $verificationCode = (string) rand(100000, 999999);

        // ✅ Create customer
        $user = User::create([
            'name'               => $validated['name'],
            'email'              => $validated['email'],
            'password'           => Hash::make($validated['password']),
            'verification_code'  => $verificationCode,
        ]);

        // ⚡️ Try sending verification email via Brevo API
        try {
            $apiResponse = $this->sendVerificationCode($user->email, $user->name, $verificationCode);

            if (isset($apiResponse['ok']) && $apiResponse['ok']) {
                Log::info('Verification email sent via Brevo', [
                    'email' => $user->email,
                    'response' => $apiResponse['body'] ?? null,
                ]);
            } else {
                Log::error('Brevo send failed', [
                    'email' => $user->email,
                    'status' => $apiResponse['status'] ?? null,
                    'body' => $apiResponse['body'] ?? null,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Brevo API call exception', ['message' => $e->getMessage()]);
        }

        // Store email for verification page
        session(['customer_verification_email' => $user->email]);

        // Redirect to customer verification page
        return redirect()->route('emailvcode')->with('email', $user->email);
    }

    /**
     * Send verification email via Brevo API (same as owner)
     */
    private function sendVerificationCode($toEmail, $toName, $code)
    {
        // Build HTML content
        $htmlContent = View::make('emails.customer-verification-code', [
            'name' => $toName,
            'code' => $code,
        ])->render();

        $textContent = "Hello {$toName},\nYour WashWise verification code is: {$code}\n\nThanks,\nWashWise";

        $payload = [
            'sender' => [
                'name'  => env('MAIL_FROM_NAME', 'WashWise'),
                'email' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
            ],
            'to' => [
                ['email' => $toEmail, 'name' => $toName],
            ],
            'subject' => 'WashWise — Your verification code',
            'htmlContent' => $htmlContent,
            'textContent' => $textContent,
        ];

        // Send email using Brevo API
        $response = Http::withHeaders([
            'api-key' => env('SENDINBLUE_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.sendinblue.com/v3/smtp/email', $payload);

        return $response->json();
    }

    /**
     * Show the customer login page
     */
    public function showLogin(): Response
    {
        return Inertia::render('auth/Login');
    }

    /**
     * Handle customer login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'     => 'required|email',
            'password'  => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

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
     * Show customer verification page
     */
    public function showVerificationPage()
    {
        $email = session('customer_verification_email');
        return Inertia::render('auth/CustomerVerificationCode', ['email' => $email]);
    }

    /**
     * Verify customer code
     */
    public function verifyCode(Request $request)
    {
        $request->validate(['code' => 'required|numeric']);

        $user = User::where('email', session('customer_verification_email'))->first();

        if (!$user || $user->verification_code !== $request->code) {
            return back()->withErrors(['code' => 'Invalid verification code.']);
        }

        $user->update([
            'email_verified_at' => now(),
            'verification_code' => null,
        ]);

        Auth::login($user);
        return redirect()->route('dashboard')->with('success', 'Email verified successfully.');
    }
}
