<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\View;
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
    $request->validate(['email' => 'required|email']);
    $email = $request->email;

    // Determine user type (owner or customer)
    $owner = CarWashOwner::where('email', $email)->first();
    $user  = $owner ? null : User::where('email', $email)->first();

    if (!$owner && !$user) {
        return back()->withErrors(['email' => 'No account found with this email.']);
    }

    $model = $owner ?? $user;
    $userType = $owner ? 'owner' : 'user';

    $code = rand(100000, 999999);

    PasswordResetCode::updateOrCreate(
        ['email' => $email, 'user_type' => $userType],
        ['code' => $code, 'expires_at' => now()->addMinutes(10)]
    );

    $toName = $model->name ?? 'WashWise User';

    // Build email content
    $htmlContent = View::make('emails.verify-code', [
        'name'   => $toName,
        'toName' => $toName,
        'code'   => $code,
    ])->render();

    $textContent = "Hello {$toName},\nYour WashWise verification code is: {$code}\n\nThanks,\nWashWise";

    // Send email via Sendinblue API
    try {
        $payload = [
            'sender' => [
                'name'  => env('MAIL_FROM_NAME', 'WashWise'),
                'email' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
            ],
            'to' => [
                ['email' => $email, 'name' => $toName],
            ],
            'subject' => 'WashWise — Your verification code',
            'htmlContent' => $htmlContent,
            'textContent' => $textContent,
        ];

        $response = Http::withHeaders([
            'api-key' => env('SENDINBLUE_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.sendinblue.com/v3/smtp/email', $payload);

        if ($response->failed()) {
            Log::error("Failed to send password reset email to {$email}", ['response' => $response->body()]);
        }
    } catch (\Throwable $e) {
        Log::error("Sendinblue API error for {$email}: " . $e->getMessage());
    }

    return back()->with('status', 'Verification code sent to your email (if it exists).');
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
