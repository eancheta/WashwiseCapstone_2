<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
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
        ]);

        $email = $request->email;

        // Check if the email belongs to an owner or a user
        $owner = CarWashOwner::where('email', $email)->first();
        $user  = $owner ? null : User::where('email', $email)->first();

        if (!$owner && !$user) {
            return back()->withErrors(['email' => 'No account found with this email.']);
        }

        $model = $owner ?? $user;
        $userType = $owner ? 'owner' : 'user';

        // Generate 6-digit code
        $code = rand(100000, 999999);

        // Store code in database
        PasswordResetCode::updateOrCreate(
            ['email' => $email, 'user_type' => $userType],
            ['code' => $code, 'expires_at' => Carbon::now()->addMinutes(10)]
        );

        // Send email
        try {
            $toName = $model->name ?? 'WashWise User';
            Mail::raw("Hello {$toName},\nYour WashWise password reset code is: {$code}", function ($message) use ($email) {
                $message->to($email)
                        ->subject('WashWise Password Reset Code');
            });
        } catch (\Throwable $e) {
            Log::error("Failed to send password reset email to {$email}: " . $e->getMessage());
        }

        return back()->with('status', 'Verification code sent to your email.');
    }

    // Step 2: Verify code and reset password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $email = $request->email;
        $code = $request->code;

        $record = PasswordResetCode::where('email', $email)
                                   ->where('code', $code)
                                   ->first();

        if (!$record || $record->expires_at < now()) {
            return back()->withErrors(['code' => 'Invalid or expired code.']);
        }

        // Determine which model to update
        $model = $record->user_type === 'owner'
            ? CarWashOwner::where('email', $email)->first()
            : User::where('email', $email)->first();

        if (!$model) {
            return back()->withErrors(['email' => 'Account no longer exists.']);
        }

        // Update password
        $model->password = Hash::make($request->password);
        $model->save();

        // Delete used reset code
        $record->delete();

        return redirect()->route($record->user_type === 'owner' ? 'owner.login.show' : 'login')
                         ->with('status', 'Password reset successful! You can now log in.');
    }
}
