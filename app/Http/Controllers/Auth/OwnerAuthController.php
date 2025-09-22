<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CarWashOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;

class OwnerAuthController extends Controller
{
    public function create()
    {
        return inertia('Owner/Register');
    }

    public function store(Request $request)
    {
        // (Optional) extend execution time just for safety; not ideal but harmless here.
        ini_set('max_execution_time', 3600);

        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:car_wash_owners,email',
            'password'  => 'required|string|min:6|confirmed',
            'district'  => 'required|integer|min:1|max:6',
            'address'   => 'required|string|max:255',
            'photo1'    => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'photo2'    => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'photo3'    => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        // store files
        $data['photo1'] = $request->file('photo1')->store('carwash_owners', 'public');
        $data['photo2'] = $request->hasFile('photo2') ? $request->file('photo2')->store('carwash_owners', 'public') : null;
        $data['photo3'] = $request->hasFile('photo3') ? $request->file('photo3')->store('carwash_owners', 'public') : null;

        $data['password'] = Hash::make($data['password']);

        // generate verification code
        $code = (string) rand(100000, 999999);
        $data['verification_code'] = $code;

        // create owner
        $owner = CarWashOwner::create($data);

        // attempt to send verification via Brevo API
        try {
            $apiResponse = $this->sendVerificationCode($owner->email, $owner->name, $code);

            if ($apiResponse['ok']) {
                Log::info('Verification email sent via Brevo', [
                    'email' => $owner->email,
                    'response' => $apiResponse['body'] ?? null,
                ]);
            } else {
                // Save details for debugging and fallback
                Log::error('Brevo send failed', [
                    'email' => $owner->email,
                    'status' => $apiResponse['status'] ?? null,
                    'body' => $apiResponse['body'] ?? null,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Brevo API call exception', ['message' => $e->getMessage()]);
        }

        // store email for verification page
        session(['owner_verification_email' => $owner->email]);

        // redirect to verify page
        return redirect()->route('owner.verify.show')->with('email', $owner->email);
    }

    public function showLogin()
    {
        return inertia('Owner/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $owner = CarWashOwner::where('email', $request->email)->first();

        if (!$owner) {
            return back()->withErrors(['email' => 'Account not found.'])->withInput();
        }

        if ($owner->status !== 'approved') {
            return back()->withErrors(['email' => 'Your account is pending approval by the admin.'])->withInput();
        }

        if (! $owner->email_verified_at) {
            return back()->withErrors(['email' => 'Please verify your email before logging in.'])->withInput();
        }

        if (auth()->guard('carwashowner')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('carwashownerdashboard'));
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    /**
     * Send verification email using Brevo (Sendinblue) transactional API.
     * Returns array ['ok' => bool, 'status' => int|null, 'body' => mixed|null]
     */
public function sendVerificationCode($toEmail, $toName, $code)
{
    // Render your Blade template into HTML
    $htmlContent = View::make('emails.owner-verification-code', [
        'name' => $toName,
        'code' => $code,
    ])->render();

    // Create fallback text-only version
    $textContent = "Hello {$toName},\nYour WashWise verification code is: {$code}\n\nThanks,\nWashWise";

    // Build payload for Brevo API
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

    // Send to Brevo API
    $response = Http::withHeaders([
        'api-key' => env('SENDINBLUE_API_KEY'),
        'Content-Type' => 'application/json',
    ])->post('https://api.sendinblue.com/v3/smtp/email', $payload);

    return $response->json();
}
}
