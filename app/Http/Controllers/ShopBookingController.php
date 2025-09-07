<?php

namespace App\Http\Controllers;

use App\Models\CarWashShop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class ShopBookingController extends Controller
{
    /**
     * GET /shops/{shop}/availability?date=YYYY-MM-DD
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
            'email' => 'nullable|email|max:255',
        ]);

        $shop = CarWashShop::findOrFail($shopId);
        $table = "bookings_shop_{$shop->id}";

        // Ensure per-shop booking table exists
        OwnerShopController::ensureBookingTableExists($shop->id);

        $data['date_of_booking'] = trim($data['date_of_booking']);
        $data['time_of_booking'] = trim($data['time_of_booking']);

        $newStart = Carbon::createFromFormat('Y-m-d H:i', $data['date_of_booking'] . ' ' . $data['time_of_booking']);
        $newEnd = (clone $newStart)->addHours(3);

        // Check slot overlap
        $existing = DB::table($table)
            ->where('date_of_booking', $data['date_of_booking'])
            ->where('slot_number', $data['slot_number'])
            ->get();

        foreach ($existing as $b) {
            $start = Carbon::createFromFormat('Y-m-d H:i', $data['date_of_booking'] . ' ' . $b->time_of_booking);
            $end = (clone $start)->addHours(3);

            if ($start->lt($newEnd) && $newStart->lt($end)) {
                return back()->withErrors([
                    'time_of_booking' => 'The selected slot is occupied within 3 hours of that time.'
                ])->withInput();
            }
        }

        DB::table($table)->insert([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'size_of_the_car' => $data['size_of_the_car'],
            'contact_no' => $data['contact_no'],
            'time_of_booking' => $data['time_of_booking'],
            'date_of_booking' => $data['date_of_booking'],
            'slot_number' => $data['slot_number'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', "Booked on slot #{$data['slot_number']} at {$shop->name}.");
    }

    /**
     * GET /customer/book/{shop}/payment
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
     */
    public function paymentPage(Request $request, int $shopId)
    {
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

        $data['date_of_booking'] = trim($data['date_of_booking']);
        $data['time_of_booking'] = trim($data['time_of_booking']);

        $newStart = Carbon::createFromFormat('Y-m-d H:i', $data['date_of_booking'] . ' ' . $data['time_of_booking']);
        $newEnd = (clone $newStart)->addHours(3);

        $existing = DB::table($table)
            ->where('date_of_booking', $data['date_of_booking'])
            ->where('slot_number', $data['slot_number'])
            ->get();

        foreach ($existing as $b) {
            $start = Carbon::createFromFormat('Y-m-d H:i', $data['date_of_booking'] . ' ' . $b->time_of_booking);
            $end = (clone $start)->addHours(3);

            if ($start->lt($newEnd) && $newStart->lt($end)) {
                return back()->withErrors([
                    'time_of_booking' => 'The selected slot is occupied within 3 hours of that time.'
                ])->withInput();
            }
        }

        $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');

        $now = now();

        // Update existing booking or insert new
        $query = DB::table($table)
            ->where('name', $data['name'])
            ->where('email', $data['email'])
            ->where('date_of_booking', $data['date_of_booking'])
            ->where('time_of_booking', $data['time_of_booking'])
            ->where('slot_number', $data['slot_number']);

        $existingBooking = $query->first();

        if ($existingBooking) {
            $query->update([
                'payment_amount' => $data['payment_amount'],
                'payment_proof' => $paymentProofPath,
                'payment_status' => 'paid',
                'user_id' => Auth::id(),
                'updated_at' => $now,
            ]);
        } else {
            DB::table($table)->insert([
                'user_id' => Auth::id(),
                'name' => $data['name'],
                'email' => $data['email'],
                'size_of_the_car' => $data['size_of_the_car'],
                'contact_no' => $data['contact_no'],
                'time_of_booking' => $data['time_of_booking'],
                'date_of_booking' => $data['date_of_booking'],
                'slot_number' => $data['slot_number'],
                'payment_amount' => $data['payment_amount'],
                'payment_proof' => $paymentProofPath,
                'payment_status' => 'paid',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return redirect()->route('dashboard')
            ->with('success', "Booked on slot #{$data['slot_number']} at {$shop->name}. Payment proof saved.");
    }
}
