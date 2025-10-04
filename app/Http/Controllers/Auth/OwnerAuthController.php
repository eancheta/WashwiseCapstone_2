<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CarWashOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Auth;

class OwnerAuthController extends Controller
{

public function edit()
{
    return view('owner.change-password', [
        'pageTitle' => 'Change Password',
    ]);
}

public function update(Request $request)
{
    $owner = Auth::guard('carwashowner')->user();

    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|string|min:6|confirmed',
    ]);

    if (!Hash::check($request->current_password, $owner->password)) {
        return back()->withErrors(['current_password' => 'Current password is incorrect.']);
    }

    $owner->password = Hash::make($request->new_password);
    $owner->save();

    return back()->with('success', 'Password updated successfully.');
}

    public function create()
    {
        return inertia('Owner/Register');
    }

    public function store(Request $request)
    {
        // (Optional) extend execution time
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

        // 🔑 Hash password
        $data['password'] = Hash::make($data['password']);

        // 🔑 Verification code
        $data['verification_code'] = (string) rand(100000, 999999);

        // ✅ Cloudinary client (same as OwnerShopController)
        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => ['secure' => true],
        ]);

        // ✅ Upload photo1 (required)
        if ($request->hasFile('photo1')) {
            $file = $request->file('photo1');
            $response = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                'folder' => 'carwash_owners',
                'resource_type' => 'image',
                'overwrite' => true,
            ]);
            $data['photo1'] = $response['secure_url'];
        }

        // ✅ Upload photo2 (optional)
        if ($request->hasFile('photo2')) {
            $file = $request->file('photo2');
            $response = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                'folder' => 'carwash_owners',
                'resource_type' => 'image',
                'overwrite' => true,
            ]);
            $data['photo2'] = $response['secure_url'];
        } else {
            $data['photo2'] = null;
        }

        // ✅ Upload photo3 (optional)
        if ($request->hasFile('photo3')) {
            $file = $request->file('photo3');
            $response = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                'folder' => 'carwash_owners',
                'resource_type' => 'image',
                'overwrite' => true,
            ]);
            $data['photo3'] = $response['secure_url'];
        } else {
            $data['photo3'] = null;
        }

        // ✅ Create owner in DB
        $owner = CarWashOwner::create($data);

        // ⚡️ Attempt to send verification via Brevo API
        try {
            $apiResponse = $this->sendVerificationCode($owner->email, $owner->name, $data['verification_code']);

            if (isset($apiResponse['ok']) && $apiResponse['ok']) {
                Log::info('Verification email sent via Brevo', [
                    'email' => $owner->email,
                    'response' => $apiResponse['body'] ?? null,
                ]);
            } else {
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

        if (!$owner->email_verified_at) {
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
