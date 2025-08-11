<?php

namespace app\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CarWashOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Mail\OwnerVerificationCodeMail;
use Illuminate\Support\Facades\Mail;

class OwnerAuthController extends Controller
{

    public function create()
    {
        return inertia('Owner/Register');
    }

    public function store(Request $request)
    {
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

        $code = (string) rand(100000, 999999);
        $data['verification_code'] = $code;

        $owner = CarWashOwner::create($data);

        try {
            Mail::to($owner->email)->send(new OwnerVerificationCodeMail($code, $owner->name));
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
        }


        session(['owner_verification_email' => $owner->email]);


        Auth::login($owner);
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

    // Find owner by email
    $owner = CarWashOwner::where('email', $request->email)->first();

    if (!$owner) {
        return back()->withErrors(['email' => 'Account not found.']);
    }

    // Block login if account is not approved
    if ($owner->status !== 'approved') {
        return back()->withErrors(['email' => 'Your account is pending approval by the admin.']);
    }

    // Attempt login
    if (Auth::guard('carwashowner')->attempt($credentials)) {

        // If email is not verified yet, redirect to verification page
        if (!$owner->email_verified_at) {
            Auth::guard('carwashowner')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            session(['owner_verification_email' => $owner->email]);

            return redirect()->route('owner.verify.show')->with('email', $owner->email);
        }

        $request->session()->regenerate();
        return redirect()->intended(route('carwashownerdashboard'));
    }

    return back()->withErrors(['email' => 'Invalid credentials'])->withInput($request->only('email'));
}
}
