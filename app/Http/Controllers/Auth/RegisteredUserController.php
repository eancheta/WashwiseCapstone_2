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
     * Handle registration submission.
     */
    public function store(Request $request)
    {
        ini_set('max_execution_time', 3600);

        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8|confirmed',
        ]);

        // Generate 6-digit verification code & hash password
        $data['verification_code'] = (string) rand(100000, 999999);
        $data['password'] = Hash::make($data['password']);

        // Create user
        $user = User::create($data);

        // Attempt to send verification email via Brevo (Sendinblue)
        try {
            $apiResponse = $this->sendVerificationCode($user->email, $user->name, $data['verification_code']);

            if (is_array($apiResponse) && (isset($apiResponse['messageId']) || isset($apiResponse['message']) || isset($apiResponse['id']))) {
                Log::info('✅ Customer verification email sent', [
                    'email' => $user->email,
                    'response' => $apiResponse,
                ]);
            } else {
                Log::error('❌ Failed to send customer email (Brevo response)', [
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

        // Store email for verification step
        session(['customer_verification_email' => $user->email]);

        // Redirect to verification page
        return redirect()->route('emailvcode')->with('email', $user->email);
    }

    /**
     * Send verification email using Brevo (Sendinblue).
     */
    public function sendVerificationCode($toEmail, $toName, $code)
    {
        try {
            $htmlContent = View::make('emails.verify-code', [
                'name'   => $toName,
                'toName' => $toName,
                'code'   => $code,
            ])->render();
        } catch (\Throwable $e) {
            Log::error('Email view render failed', [
                'view' => 'emails.verify-code',
                'error' => $e->getMessage(),
            ]);
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

        try {
            $response = Http::withHeaders([
                'api-key' => env('SENDINBLUE_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.sendinblue.com/v3/smtp/email', $payload);
        } catch (\Throwable $e) {
            Log::error('Brevo HTTP request failed (customer)', ['error' => $e->getMessage()]);
            return ['error' => 'http_exception', 'message' => $e->getMessage()];
        }

        $status = $response->status();
        try {
            $body = $response->json();
        } catch (\Throwable $e) {
            $body = $response->body();
        }

        Log::info('Brevo response (customer)', [
            'status' => $status,
            'body' => $body,
        ]);

        if ($status < 200 || $status >= 300) {
            Log::error('Brevo returned non-2xx (customer)', [
                'status' => $status,
                'body' => $body,
                'to' => $toEmail,
            ]);
        }

        return is_array($body) ? $body : ['raw' => $body, 'status' => $status];
    }

    /**
     * Show login page.
     */
    public function showLogin(): Response
    {
        return Inertia::render('auth/Login');
    }

    /**
     * Handle login request.
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

        // ✅ Block login if not verified yet
        if (is_null($user->email_verified_at)) {
            return back()->withErrors(['email' => 'Please verify your email before logging in.'])->withInput();
        }

        // Attempt login only after verification
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    /**
     * Show the verification code page.
     */
    public function showVerificationPage(): Response
    {
        $email = session('customer_verification_email');
        return Inertia::render('EmailVerificationCode', [
            'email' => $email,
        ]);
    }

    /**
     * Verify the input code and redirect to login.
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
