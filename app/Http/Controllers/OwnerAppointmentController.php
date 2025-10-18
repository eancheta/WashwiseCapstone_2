<?php

namespace App\Http\Controllers;

use App\Models\CarWashShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentApprovedMail;
use Illuminate\Support\Facades\Http;

class OwnerAppointmentController extends Controller
{
    /**
     * Show all appointments for the logged-in owner's shop.
     */
public function index()
{
    $ownerId = Auth::guard('carwashowner')->id();
    if (!$ownerId) {
        return redirect()->route('owner.login.show');
    }

    $shopId = CarWashShop::where('owner_id', $ownerId)->value('id');
    if (!$shopId) {
        return redirect()->route('owner.shop.create');
    }

    $tableName = "bookings_shop_{$shopId}";

    if (!DB::getSchemaBuilder()->hasTable($tableName)) {
        Log::warning('No bookings table found for shop ID: ' . $shopId);
        return Inertia::render('OwnerAppointments', ['appointments' => []]);
    }

$appointments = DB::table($tableName)
    ->select(
        'id',
        'name',
        'size_of_the_car',
        'contact_no',
        'email',
        'time_of_booking',
        'date_of_booking',
        'slot_number',
        'created_at',
        'status',
        'payment_proof',
        'payment_amount',
        'services_offered'
    )
        ->orderBy('date_of_booking', 'desc')
        ->orderBy('time_of_booking', 'desc')
        ->get()
        ->map(function ($appointment) {
            if ($appointment->payment_proof) {
                // ✅ Only prepend storage URL if it's not already a full URL
                if (!str_starts_with($appointment->payment_proof, 'http')) {
                    $appointment->payment_proof = Storage::url($appointment->payment_proof);
                }
            }
            return $appointment;
        });

    return Inertia::render('OwnerAppointments', [
        'appointments' => $appointments
    ]);
}


    /**
     * Show Walk-in form.
     */
    public function walkinForm()
    {
        return view('owner.walkin');
    }

    /**
     * Store Walk-in appointment.
     */
    public function storeWalkin(Request $request)
    {
        $ownerId = Auth::guard('carwashowner')->id();
        if (!$ownerId) {
            return redirect()->route('owner.login.show');
        }

        $shopId = CarWashShop::where('owner_id', $ownerId)->value('id');
        if (!$shopId) {
            return redirect()->route('owner.shop.create');
        }

        $tableName = "bookings_shop_{$shopId}";

        if (!DB::getSchemaBuilder()->hasTable($tableName)) {
            return back()->with('error', 'Bookings table not found.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'size_of_the_car' => 'required|string',
            'contact_no' => 'required|string|max:20',
            'date_of_booking' => 'required|date',
            'time_of_booking' => 'required',
            'slot_number' => 'required|integer|min:1|max:4',
        ]);

        $validated['status'] = 'Approved'; // walk-ins are instantly approved
        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        DB::table($tableName)->insert($validated);

        return redirect()->route('owner.appointments')
            ->with('success', 'Walk-in appointment added successfully.');
    }

    /**
     * Approve a booking and send confirmation email.
     */
public function approve($id)
{
    $ownerId = Auth::guard('carwashowner')->id();
    $shopId = CarWashShop::where('owner_id', $ownerId)->value('id');
    $tableName = "bookings_shop_{$shopId}";

    if (!DB::getSchemaBuilder()->hasTable($tableName)) {
        return back()->with('error', 'No bookings found for this shop.');
    }

    $booking = DB::table($tableName)->where('id', $id)->first();

    if (!$booking) {
        return back()->with('error', 'Booking not found.');
    }

    DB::table($tableName)
        ->where('id', $id)
        ->update(['status' => 'Approved']);

    if ($booking->email) {
        $shop = CarWashShop::findOrFail($shopId);

            $emailData = [
                'customer_name' => $booking->name,
                'service_name' => $booking->size_of_the_car . ' Wash',
                'date_time' => $booking->date_of_booking . ' ' . $booking->time_of_booking,
                'car_wash_name' => $shop->name,
                'car_wash_address' => $shop->address,
                'amount_paid' => $booking->payment_amount,
            ];

        try {
            // ✅ Use Brevo API directly
            $apiKey = env('SENDINBLUE_API_KEY');

            $response = Http::withHeaders([
                'api-key'      => $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.sendinblue.com/v3/smtp/email', [
                'sender' => [
                    'name'  => env('MAIL_FROM_NAME', 'WashWise'),
                    'email' => env('MAIL_FROM_ADDRESS', 'no-reply@washwise.com'),
                ],
                'to' => [
                    ['email' => $booking->email, 'name' => $booking->name],
                ],
                'subject' => 'Your Car Wash Appointment is Approved',
                'htmlContent' => view('emails.appointment_approved', $emailData)->render(),
            ]);

            if ($response->failed()) {
                Log::error("Brevo email failed: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Failed to send confirmation email for booking ID: ' . $id . ' | ' . $e->getMessage());
        }
    }

    return redirect()->route('owner.appointments')->with('success', 'Booking approved successfully.');
}


    /**
     * Decline a booking.
     */
public function decline(Request $request, $id)
{
    $request->validate([
        'reason' => 'required|string|max:1000',
    ]);

    $ownerId = Auth::guard('carwashowner')->id();
    $shopId = CarWashShop::where('owner_id', $ownerId)->value('id');
    $tableName = "bookings_shop_{$shopId}";

    if (!DB::getSchemaBuilder()->hasTable($tableName)) {
        Log::error("Decline: bookings table missing for shop {$shopId}");
        return back()->with('error', 'No bookings found for this shop.');
    }

    $booking = DB::table($tableName)->where('id', $id)->first();
    if (!$booking) {
        Log::error("Decline: booking not found, id={$id}, table={$tableName}");
        return back()->with('error', 'Booking not found.');
    }

    DB::table($tableName)
        ->where('id', $id)
        ->update(['status' => 'Declined', 'reason' => $request->reason]);

    // Prepare email payload (same structure as approve)
    $shop = CarWashShop::find($shopId);
    $emailData = [
        'customer_name' => $booking->name,
        'service_name' => $booking->size_of_the_car . ' Wash',
        'date_time' => $booking->date_of_booking . ' ' . $booking->time_of_booking,
        'car_wash_name' => $shop->name ?? 'WashWise',
        'car_wash_address' => $shop->address ?? '',
        'decline_reason' => $request->reason, // ✅ changed key to match Blade
    ];

    if (empty($booking->email)) {
        Log::info("Decline: no email for booking id={$id}, skipping send.");
        return back()->with('success', 'Appointment declined (no email to send).');
    }

    Log::info('Decline: sending decline email', [
        'to' => $booking->email,
        'booking_id' => $id,
        'payload' => $emailData,
    ]);

    try {
        $apiKey = env('SENDINBLUE_API_KEY');
        $payload = [
            'sender' => [
                'name' => env('MAIL_FROM_NAME', 'WashWise'),
                'email' => env('MAIL_FROM_ADDRESS', 'no-reply@washwise.com'),
            ],
            'to' => [
                ['email' => $booking->email, 'name' => $booking->name],
            ],
            'subject' => 'Your Car Wash Appointment Has Been Declined',
            'htmlContent' => view('emails.appointment_declined', $emailData)->render(),
        ];

        $resp = Http::withHeaders([
            'api-key' => $apiKey,
            'Accept' => 'application/json',
        ])->post('https://api.sendinblue.com/v3/smtp/email', $payload);

        Log::info('Decline: sendinblue response', ['status' => $resp->status(), 'body' => $resp->body()]);

        if ($resp->failed()) {
            Log::error('Decline: sendinblue failed', ['body' => $resp->body()]);
            \Mail::to($booking->email)->send(new \App\Mail\AppointmentDeclined($emailData));
            Log::info('Decline: fallback Mail::send used (log/mail driver).');
        }
    } catch (\Exception $e) {
        Log::error('Decline: exception when sending via sendinblue', ['message' => $e->getMessage()]);
        try {
            \Mail::to($booking->email)->send(new \App\Mail\AppointmentDeclined($emailData));
            Log::info('Decline: fallback Mail::send used after exception.');
        } catch (\Exception $e2) {
            Log::error('Decline: fallback Mail::send also failed', ['message' => $e2->getMessage()]);
        }
    }

    return redirect()->route('owner.appointments')->with('success', 'Appointment declined and email attempted.');
}
}
