<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarWashOwner;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OwnerVerificationCodeMail;

class OwnerVerificationController extends Controller
{

    public function show()
    {
        $email = session('owner_verification_email') ?? session('flash.email') ?? request()->query('email');
        return inertia('Owner/VerifyCode', [
            'email' => $email
        ]);
    }

    public function resend(Request $request)
    {
        $email = $request->email ?? session('owner_verification_email');
        $owner = CarWashOwner::where('email', $email)->firstOrFail();

        $code = (string) rand(100000, 999999);
        $owner->verification_code = $code;
        $owner->save();

        Mail::to($owner->email)->send(new OwnerVerificationCodeMail($code, $owner->name));

        session(['owner_verification_email' => $owner->email]);

        return back()->with('success', 'Verification code resent.')->with('email', $owner->email);
    }


    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
            'email' => 'required|email',
        ]);

        $owner = CarWashOwner::where('email', $request->email)->first();

        if (! $owner) {
            return back()->withErrors(['email' => 'Owner not found']);
        }

        if ($owner->verification_code !== $request->code) {
            return back()->withErrors(['code' => 'Invalid verification code'])->with('email', $owner->email);
        }


        $owner->email_verified_at = now();
        $owner->verification_code = null;
        $owner->save();

        Auth::login($owner);

        return redirect()->route('carwashownerdashboard')->with('success', 'Email verified.');
    }
}
