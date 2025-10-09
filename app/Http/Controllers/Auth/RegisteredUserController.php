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
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

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
        $data['status'] = 'not verified'; // ✅ default

        // Create user
        $user = User::create($data);

        // Attempt to send verification email via Brevo (Sendinblue)
        try {
            $this->sendVerificationCode($user->email, $user->name, $data['verification_code']);
        } catch (\Exception $e) {
            Log::error('⚠️ Brevo API exception (customer)', [
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }

        // Store email for verification step
        session(['customer_verification_email' => $user->email]);

        return redirect()->route('emailvcode')->with('email', $user->email);
    }

    public function sendVerificationCode($toEmail, $toName, $code)
    {
        $htmlContent = View::make('emails.verify-code', [
            'name'   => $toName,
            'toName' => $toName,
            'code'   => $code,
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

        return Http::withHeaders([
            'api-key' => env('SENDINBLUE_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.sendinblue.com/v3/smtp/email', $payload);
    }

    public function showLogin(): Response
    {
        return Inertia::render('auth/Login');
    }

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

        // 🚫 Block login if user is not verified
        if ($user->status !== 'verified') {
            return back()->withErrors(['email' => 'Your account is not verified. Please check your email.'])->withInput();
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    public function showVerificationPage(): Response
    {
        $email = session('customer_verification_email');
        return Inertia::render('EmailVerificationCode', ['email' => $email]);
    }

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
            'status' => 'verified', // ✅ update to verified
        ]);

        return redirect()->route('login')->with('success', 'Email verified successfully! You can now log in.');
    }
}
