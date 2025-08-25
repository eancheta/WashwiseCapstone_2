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
                'payment_amount'
            )
            ->orderBy('date_of_booking', 'desc')
            ->orderBy('time_of_booking', 'desc')
            ->get()
            ->map(function ($appointment) {
                if ($appointment->payment_proof) {
                    $appointment->payment_proof = Storage::url($appointment->payment_proof);
                }
                return $appointment;
            });

        Log::info('Appointments fetched: ', $appointments->toArray());

        return Inertia::render('OwnerAppointments', [
            'appointments' => $appointments
        ]);
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
            Log::warning('No bookings table found for shop ID: ' . $shopId);
            return back()->with('error', 'No bookings found for this shop.');
        }

        $booking = DB::table($tableName)
            ->where('id', $id)
            ->first();

        if (!$booking) {
            return back()->with('error', 'Booking not found.');
        }

        if (!$booking->email) {
            Log::warning('No email address found for booking ID: ' . $id);
            return back()->with('error', 'Booking approved but no email address provided for confirmation.');
        }

        DB::table($tableName)
            ->where('id', $id)
            ->update(['status' => 'approved']);

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
            Mail::to($booking->email)->send(new AppointmentApprovedMail($emailData));
            Log::info('Confirmation email sent for booking ID: ' . $id);
        } catch (\Exception $e) {
            Log::error('Failed to send confirmation email for booking ID: ' . $id . '. Error: ' . $e->getMessage());
            return back()->with('error', 'Booking approved but failed to send confirmation email.');
        }

        return back()->with('success', 'Booking approved and confirmation email sent.');
    }

    /**
     * Decline a booking.
     */
    public function decline($id)
    {
        $ownerId = Auth::guard('carwashowner')->id();
        $shopId = CarWashShop::where('owner_id', $ownerId)->value('id');
        $tableName = "bookings_shop_{$shopId}";

        if (DB::getSchemaBuilder()->hasTable($tableName)) {
            DB::table($tableName)
                ->where('id', $id)
                ->update(['status' => 'declined']);
        }

        return back();
    }
}
