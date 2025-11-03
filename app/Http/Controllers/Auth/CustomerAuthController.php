<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class CustomerAuthController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('auth/Login');
    }

public function login(Request $request)
{
    $data = $request->validate([
        'email'    => 'required|email',
        'password' => 'required|string',
    ]);

    $user = User::where('email', $data['email'])->first();

    if (! $user) {
        return back()->withErrors(['email' => 'Account not found.'])->withInput();
    }

    // Block login if email not verified
    $status = $user->status === null ? '' : trim(strtolower($user->status));
    if ($status !== 'verified') {
        return back()->withErrors(['email' => 'Your account is not verified. Please check your email.'])->withInput();
    }

    // Check password
    if (! Hash::check($data['password'], $user->password)) {
        return back()->withErrors(['email' => 'Wrong Password or Email'])->withInput();
    }

    // Login approved & verified user
    Auth::login($user);
    $request->session()->regenerate();

    // === SEND REMINDER EMAIL ON LOGIN ===
    try {
        // Fetch the user's upcoming bookings
        $bookings = DB::table("bookings_shop_{$user->shop_id}") // adjust if user is linked to a shop
            ->where('status', 'Approved')
            ->whereDate('date_of_booking', now()->toDateString())
            ->get();

        foreach ($bookings as $booking) {
            $bookingDateTime = Carbon::parse("{$booking->date_of_booking} {$booking->time_of_booking}");
            // Only send if the booking is within 1 hour of login
            if ($bookingDateTime->between(now()->addMinutes(-5), now()->addHour()->addMinutes(5))) {
                $emailData = [
                    'customer_name' => $booking->name,
                    'service_name' => $booking->size_of_the_car . ' Wash',
                    'date_time' => $booking->date_of_booking . ' ' . $booking->time_of_booking,
                    'car_wash_name' => $booking->shop_name ?? 'WashWise',
                    'car_wash_address' => $booking->shop_address ?? '',
                ];

                $apiKey = env('SENDINBLUE_API_KEY');
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.sendinblue.com/v3/smtp/email', [
                    'sender' => [
                        'name' => env('MAIL_FROM_NAME', 'WashWise'),
                        'email' => env('MAIL_FROM_ADDRESS', 'no-reply@washwise.com'),
                    ],
                    'to' => [
                        ['email' => $booking->email, 'name' => $booking->name],
                    ],
                    'subject' => '⏰ Reminder: Your Car Wash Appointment in 1 Hour',
                    'htmlContent' => view('emails.booking_reminder', $emailData)->render(),
                ]);

                if ($response->failed()) {
                    \Illuminate\Support\Facades\Log::error("Reminder email failed: " . $response->body());
                }
            }
        }
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error("Failed to send reminder on login for user ID {$user->id}: {$e->getMessage()}");
    }

    return redirect()->intended('/dashboard');
}

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
