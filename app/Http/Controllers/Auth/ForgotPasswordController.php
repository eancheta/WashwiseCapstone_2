<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\CarWashOwner;
use App\Models\PasswordResetCode;

class ForgotPasswordController extends Controller
{
    // Step 1: Send verification code
    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'type' => 'required|in:user,owner',
        ]);

        $user = $request->type === 'owner'
            ? CarWashOwner::where('email', $request->email)->first()
            : User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No account found with this email.']);
        }

        $code = rand(100000, 999999);

        PasswordResetCode::updateOrCreate(
            ['email' => $request->email, 'user_type' => $request->type],
            ['code' => $code, 'expires_at' => Carbon::now()->addMinutes(10)]
        );

        // Send email
        Mail::raw("Your WashWise password reset code is: $code", function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('WashWise Password Reset Code');
        });

        return back()->with('status', 'Verification code sent to your email.');
    }

    // Step 2: Verify code and reset password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'type' => 'required|in:user,owner',
            'code' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $record = PasswordResetCode::where('email', $request->email)
            ->where('user_type', $request->type)
            ->where('code', $request->code)
            ->first();

        if (!$record || $record->expires_at < now()) {
            return back()->withErrors(['code' => 'Invalid or expired code.']);
        }

        $user = $request->type === 'owner'
            ? CarWashOwner::where('email', $request->email)->first()
            : User::where('email', $request->email)->first();

        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        // Delete the used record
        $record->delete();

        return redirect()->route('login')->with('status', 'Password reset successful! You can now log in.');
    }
}
