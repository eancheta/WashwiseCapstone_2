<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CarWashOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OwnerVerificationCodeMail;

class OwnerAuthController extends Controller
{
    public function create()
    {
        return inertia('Owner/Register');
    }

    public function store(Request $request)
    {

            ini_set('max_execution_time', 3600);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:car_wash_owners,email',
            'password' => 'required|string|min:6|confirmed',
            'district' => 'required|integer|min:1|max:6',
            'address' => 'required|string|max:255',
            'photo1' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'photo2' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'photo3' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $data['photo1'] = $request->file('photo1')->store('carwash_owners', 'public');
        $data['photo2'] = $request->hasFile('photo2') ? $request->file('photo2')->store('carwash_owners', 'public') : null;
        $data['photo3'] = $request->hasFile('photo3') ? $request->file('photo3')->store('carwash_owners', 'public') : null;

        $data['password'] = Hash::make($data['password']);

        // Generate 6-digit verification code
        $code = (string) rand(100000, 999999);
        $data['verification_code'] = $code;

        $owner = CarWashOwner::create($data);

try {
    Mail::to($owner->email)
        ->send(new OwnerVerificationCodeMail($code, $owner->name));
    Log::info('Verification email sent to: ' . $owner->email);
} catch (\Exception $e) {
    Log::error('Email sending failed: ' . $e->getMessage());
}

        // Store email in session to use on verify page
        session(['owner_verification_email' => $owner->email]);

        // Redirect to verify code page
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
}
