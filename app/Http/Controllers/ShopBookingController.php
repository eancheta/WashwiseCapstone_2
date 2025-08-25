<?php

namespace App\Http\Controllers;

use App\Models\CarWashShop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ShopBookingController extends Controller
{
    /**
     * GET /shops/{shop}/availability?date=YYYY-MM-DD
     * Returns available bookings for a shop on a specific date.
     */
    public function availability(Request $request, int $shopId)
    {
        $request->validate(['date' => 'required|date']);

        $shop = CarWashShop::findOrFail($shopId);
        $table = "bookings_shop_{$shop->id}";

        if (!Schema::hasTable($table)) {
            return response()->json(['bookings' => []]);
        }

        $bookings = DB::table($table)
            ->where('date_of_booking', $request->date)
            ->orderBy('time_of_booking')
            ->get();

        return response()->json(['bookings' => $bookings]);
    }

    /**
     * POST /shops/{shop}/book
     * Creates a booking without payment, assigns a slot, and redirects back.
     */
    public function store(Request $request, int $shopId)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'size_of_the_car' => 'required|in:HatchBack,Sedan,MPV,SUV,Pickup,Van,Motorcycle',
            'contact_no' => 'required|string|max:20',
            'date_of_booking' => 'required|date',
            'time_of_booking' => ['required', 'date_format:H:i', 'regex:/^[0-2][0-3]:[0-5][0-9]$/'],
            'slot_number' => 'required|integer|min:1|max:4',
        ]);

        $shop = CarWashShop::findOrFail($shopId);
        $table = "bookings_shop_{$shop->id}";

        // Ensure per-shop booking table exists
        OwnerShopController::ensureBookingTableExists($shop->id);

        // Trim inputs to avoid trailing spaces
        $data['date_of_booking'] = trim($data['date_of_booking']);
        $data['time_of_booking'] = trim($data['time_of_booking']);

        try {
            $newStart = Carbon::createFromFormat('Y-m-d H:i', $data['date_of_booking'] . ' ' . $data['time_of_booking']);
            $newEnd = (clone $newStart)->addHours(3);
        } catch (\Exception $e) {
            Log::error('Carbon parsing error: ' . $e->getMessage());
            return back()->withErrors(['time_of_booking' => 'Invalid date or time format. Please use YYYY-MM-DD for date and HH:MM for time.'])->withInput();
        }

        // Check for slot overlap
        $existing = DB::table($table)
            ->where('date_of_booking', $data['date_of_booking'])
            ->where('slot_number', $data['slot_number'])
            ->orderBy('time_of_booking')
            ->get();

        foreach ($existing as $b) {
            try {
                $start = Carbon::createFromFormat('Y-m-d H:i', $data['date_of_booking'] . ' ' . $b->time_of_booking);
                $end = (clone $start)->addHours(3);
            } catch (\Exception $e) {
                Log::error('Carbon parsing error for existing booking: ' . $e->getMessage());
                continue;
            }

            if ($start->lt($newEnd) && $newStart->lt($end)) {
                return back()->withErrors([
                    'time_of_booking' => 'The selected slot is occupied within 3 hours of that time. Please choose another time or slot.'
                ])->withInput();
            }
        }

        DB::table($table)->insert([
            'name' => $data['name'],
            'size_of_the_car' => $data['size_of_the_car'],
            'contact_no' => $data['contact_no'],
            'time_of_booking' => $data['time_of_booking'],
            'date_of_booking' => $data['date_of_booking'],
            'slot_number' => $data['slot_number'],
            'created_at' => now(),
        ]);

        return back()->with('success', "Booked on slot #{$data['slot_number']} at {$shop->name}.");
    }

    /**
     * GET /customer/book/{shop}/payment
     * Handles invalid GET requests to the payment route.
     */
    public function showPaymentPage(int $shopId)
    {
        $shop = CarWashShop::findOrFail($shopId);
        return Inertia::render('Public/PaymentPage', [
            'shop' => $shop,
            'booking' => null,
            'pageTitle' => "Booking required for {$shop->name}",
            'error' => 'Please submit booking details to proceed with payment.',
        ]);
    }

    /**
     * POST /customer/book/{shop}/payment
     * Accepts preliminary booking fields and renders the payment page (Inertia).
     */
    public function paymentPage(Request $request, int $shopId)
    {
        Log::info('Request to paymentPage', [
            'method' => $request->method(),
            'shopId' => $shopId,
            'input' => $request->all(),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'size_of_the_car' => 'required|in:HatchBack,Sedan,MPV,SUV,Pickup,Van,Motorcycle',
            'contact_no' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'date_of_booking' => 'required|date',
            'time_of_booking' => ['required', 'date_format:H:i', 'regex:/^[0-2][0-3]:[0-5][0-9]$/'],
            'slot_number' => 'required|integer|min:1|max:4',
        ]);

        $shop = CarWashShop::findOrFail($shopId);

        return Inertia::render('Public/PaymentPage', [
            'shop' => $shop,
            'booking' => $validated,
            'pageTitle' => "Pay for booking at {$shop->name}",
        ]);
    }

    /**
     * POST /customer/book/{shop}/confirm
     * Validates payment (image), assigns a slot, and saves booking.
     * Redirects to 'dashboard' route.
     */
    public function confirmBooking(Request $request, int $shopId)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'size_of_the_car' => 'required|in:HatchBack,Sedan,MPV,SUV,Pickup,Van,Motorcycle',
            'contact_no' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'date_of_booking' => 'required|date',
            'time_of_booking' => ['required', 'date_format:H:i', 'regex:/^[0-2][0-3]:[0-5][0-9]$/'],
            'slot_number' => 'required|integer|min:1|max:4',
            'payment_amount' => 'required|numeric|in:50',
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $shop = CarWashShop::findOrFail($shopId);
        $table = "bookings_shop_{$shop->id}";

        // Ensure per-shop booking table exists
        OwnerShopController::ensureBookingTableExists($shop->id);

        // Trim inputs to avoid trailing spaces
        $data['date_of_booking'] = trim($data['date_of_booking']);
        $data['time_of_booking'] = trim($data['time_of_booking']);

        try {
            $newStart = Carbon::createFromFormat('Y-m-d H:i', $data['date_of_booking'] . ' ' . $data['time_of_booking']);
            $newEnd = (clone $newStart)->addHours(3);
        } catch (\Exception $e) {
            Log::error('Carbon parsing error: ' . $e->getMessage());
            return back()->withErrors(['time_of_booking' => 'Invalid date or time format. Please use YYYY-MM-DD for date and HH:MM for time.'])->withInput();
        }

        // Check for slot overlap
        $existing = DB::table($table)
            ->where('date_of_booking', $data['date_of_booking'])
            ->where('slot_number', $data['slot_number'])
            ->orderBy('time_of_booking')
            ->get();

        foreach ($existing as $b) {
            try {
                $start = Carbon::createFromFormat('Y-m-d H:i', $data['date_of_booking'] . ' ' . $b->time_of_booking);
                $end = (clone $start)->addHours(3);
            } catch (\Exception $e) {
                Log::error('Carbon parsing error for existing booking: ' . $e->getMessage());
                continue;
            }

            if ($start->lt($newEnd) && $newStart->lt($end)) {
                return back()->withErrors([
                    'time_of_booking' => 'The selected slot is occupied within 3 hours of that time. Please choose another time or slot.'
                ])->withInput();
            }
        }

        // Store payment proof
        $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');

        DB::table($table)->insert([
            'name' => $data['name'],
            'size_of_the_car' => $data['size_of_the_car'],
            'contact_no' => $data['contact_no'],
            'email' => $data['email'],
            'time_of_booking' => $data['time_of_booking'],
            'date_of_booking' => $data['date_of_booking'],
            'slot_number' => $data['slot_number'],
            'payment_amount' => $data['payment_amount'],
            'payment_proof' => $paymentProofPath,
            'created_at' => now(),
        ]);

        return redirect()->route('dashboard')
            ->with('success', "Booked on slot #{$data['slot_number']} at {$shop->name}. Payment proof saved.");
    }
}
