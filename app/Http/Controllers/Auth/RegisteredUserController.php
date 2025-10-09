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
     * Show the customer registration page
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle registration
     */
    public function store(Request $request)
    {
        ini_set('max_execution_time', 3600);

        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8|confirmed',
        ]);

        // Generate 6-digit verification code
        $data['verification_code'] = (string) rand(100000, 999999);
        $data['password'] = Hash::make($data['password']);

        // Create user
        $user = User::create($data);

        // Send verification email via Brevo (same pattern as owner)
        try {
            $apiResponse = $this->sendVerificationCode($user->email, $user->name, $data['verification_code']);

            if (isset($apiResponse['messageId'])) {
                Log::info('✅ Customer verification email sent', [
                    'email' => $user->email,
                    'response' => $apiResponse,
                ]);
            } else {
                Log::error('❌ Failed to send customer email', [
                    'email' => $user->email,
                    'response' => $apiResponse,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('⚠️ Brevo API exception (customer)', [
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }

        // Store email for verification page
        session(['customer_verification_email' => $user->email]);

        return redirect()->route('emailvcode')->with('email', $user->email);
    }

    /**
     * Send verification email using Brevo (same as owner)
     */
    public function sendVerificationCode($toEmail, $toName, $code)
{
    try {
        $htmlContent = View::make('emails.verify-code', [
            'name' => $toName,
            'code' => $code,
        ])->render();
    } catch (\Throwable $e) {
        Log::error('Email view render failed', ['view' => 'emails.verify-code', 'error' => $e->getMessage()]);
        // Return a structure similar to Brevo error so calling code logs it
        return ['error' => 'view_render_failed', 'message' => $e->getMessage()];
    }

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

    Log::info('Brevo: sending payload', ['to' => $toEmail, 'payload' => $payload]);

    try {
        $response = Http::withHeaders([
            'api-key' => env('SENDINBLUE_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.sendinblue.com/v3/smtp/email', $payload);
    } catch (\Throwable $e) {
        Log::error('Brevo HTTP request failed', ['error' => $e->getMessage()]);
        return ['error' => 'http_exception', 'message' => $e->getMessage()];
    }

    // Save everything to log so we can inspect it
    $status = $response->status();
    $body = null;
    try {
        $body = $response->json();
    } catch (\Throwable $e) {
        $body = $response->body();
    }

    Log::info('Brevo response', [
        'status' => $status,
        'body' => $body,
        'headers' => $response->headers(),
    ]);

    // If non-2xx, log error explicitly
    if ($status < 200 || $status >= 300) {
        Log::error('Brevo returned non-2xx', [
            'status' => $status,
            'body' => $body,
            'to' => $toEmail,
        ]);
    }

    return is_array($body) ? $body : ['raw' => $body, 'status' => $status];
}


    /**
     * Show login page
     */
    public function showLogin(): Response
    {
        return Inertia::render('auth/Login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'     => 'required|email',
            'password'  => 'required|string',
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
     * Show the verification code page
     */
    public function showVerificationPage(): Response
    {
        $email = session('customer_verification_email');
        return Inertia::render('EmailVerificationCode', [
            'email' => $email,
        ]);
    }

    /**
     * Verify the inputted code
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
