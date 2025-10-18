<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\CarWashOwner;
use App\Models\PasswordResetCode;

class ForgotPasswordController extends Controller
{
    /**
     * Step 1: Send verification code (auto-detect owner or user)
     */
    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;

        // Check if email belongs to an owner first, then user
        $owner = CarWashOwner::where('email', $email)->first();
        $user  = $owner ? null : User::where('email', $email)->first();

        if (!$owner && !$user) {
            return back()->withErrors(['email' => 'No account found with this email.']);
        }

        // Determine user type and model instance
        $model = $owner ?? $user;
        $userType = $owner ? 'owner' : 'user';

        // Generate 6-digit code
        $code = rand(100000, 999999);

        // Save or update the password reset code
        PasswordResetCode::updateOrCreate(
            ['email' => $email, 'user_type' => $userType],
            ['code' => $code, 'expires_at' => Carbon::now()->addMinutes(10)]
        );

        // Send email (or log it if sending fails)
        try {
            Mail::raw("Your WashWise password reset code is: $code", function ($message) use ($email) {
                $message->to($email)
                        ->subject('WashWise Password Reset Code');
            });
        } catch (\Throwable $e) {
            Log::error("Failed to send password reset email to {$email}: " . $e->getMessage());
            Log::info("Password reset code for {$email}: {$code}");
        }

        return back()->with('status', 'Verification code sent to your email (if it exists).');
    }

    /**
     * Step 2: Verify code and reset password (auto-detect based on stored record)
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $email = $request->email;
        $code = $request->code;

        // Find the password reset record
        $record = PasswordResetCode::where('email', $email)
            ->where('code', $code)
            ->first();

        if (!$record || $record->expires_at < now()) {
            return back()->withErrors(['code' => 'Invalid or expired code.']);
        }

        // Determine which model to update based on user_type
        $model = $record->user_type === 'owner'
            ? CarWashOwner::where('email', $email)->first()
            : User::where('email', $email)->first();

        if (!$model) {
            return back()->withErrors(['email' => 'Account no longer exists.']);
        }

        // Update the password
        $model->password = Hash::make($request->password);
        $model->save();

        // Delete the used reset code
        $record->delete();

        return redirect()->route('login')->with('status', 'Password reset successful! You can now log in.');
    }
}
