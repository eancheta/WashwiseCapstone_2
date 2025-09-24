<?php

namespace App\Http\Controllers;

use App\Models\CarWashShop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class ShopBookingController extends Controller
{
    /**
     * Check for overlapping bookings within a 3-hour window
     */
    private function checkForOverlap(string $table, string $date, string $time, int $slotNumber, int $shopId): ?string
    {
        try {
            $newStart = Carbon::parse("$date $time");
            $newEnd = $newStart->copy()->addHours(3);

            $dates = [
                $newStart->copy()->subDay()->format('Y-m-d'),
                $newStart->format('Y-m-d'),
                $newStart->copy()->addDay()->format('Y-m-d'),
            ];

            $existing = DB::table($table)
                ->whereIn('date_of_booking', $dates)
                ->where('slot_number', $slotNumber)
                ->get();

            foreach ($existing as $booking) {
                $existingStart = Carbon::parse("{$booking->date_of_booking} {$booking->time_of_booking}");
                $existingEnd = $existingStart->copy()->addHours(3);
                if ($existingStart->lt($newEnd) && $newStart->lt($existingEnd)) {
                    return 'The selected slot is occupied within 3 hours of that time.';
                }
            }
        } catch (\Exception $e) {
            return 'Invalid date or time format provided.';
        }
        return null;
    }

    /**
     * Ensure shop has full Cloudinary URLs for logo and QR code
     */
    private function formatShopCloudinaryUrls(CarWashShop $shop): CarWashShop
    {
        $cloudBase = 'https://res.cloudinary.com/YOUR_CLOUD_NAME/image/upload/';

        $shop->logo = $shop->logo
            ? (str_starts_with($shop->logo, 'http') ? $shop->logo : $cloudBase . $shop->logo)
            : null;

        $shop->qr_code = $shop->qr_code
            ? (str_starts_with($shop->qr_code, 'http') ? $shop->qr_code : $cloudBase . $shop->qr_code)
            : null;

        return $shop;
    }

    /**
     * GET /shops/{shop}/availability?date=YYYY-MM-DD
     */
    public function availability(Request $request, int $shopId)
    {
        $request->validate(['date' => 'required|date']);
        $shop = CarWashShop::findOrFail($shopId);
        $shop = $this->formatShopCloudinaryUrls($shop);

        $table = "bookings_shop_{$shop->id}";
        if (!Schema::hasTable($table)) {
            return response()->json(['bookings' => [], 'shop' => $shop]);
        }

        $date = Carbon::parse($request->date)->format('Y-m-d');
        $dates = [
            Carbon::parse($date)->subDay()->format('Y-m-d'),
            $date,
            Carbon::parse($date)->addDay()->format('Y-m-d'),
        ];

        $bookings = DB::table($table)
            ->whereIn('date_of_booking', $dates)
            ->select('id', 'date_of_booking', 'time_of_booking', 'slot_number', 'name')
            ->orderBy('date_of_booking')
            ->orderBy('time_of_booking')
            ->get()
            ->map(function ($booking) use ($date) {
                $includeName = ($booking->date_of_booking === $date);
                return [
                    'id' => $booking->id,
                    'date_of_booking' => $booking->date_of_booking,
                    'time_of_booking' => $booking->time_of_booking,
                    'slot_number' => $booking->slot_number,
                    ...( $includeName ? ['name' => $booking->name] : [] ),
                ];
            });

        return response()->json(['bookings' => $bookings, 'shop' => $shop]);
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
            'date_of_booking' => 'required|date_format:Y-m-d',
            'time_of_booking' => ['required', 'date_format:H:i', 'regex:/^[0-2][0-3]:[0-5][0-9]$/'],
            'slot_number' => 'required|integer|min:1|max:4',
            'email' => 'nullable|email|max:255',
        ]);

        $shop = CarWashShop::findOrFail($shopId);
        $shop = $this->formatShopCloudinaryUrls($shop);

        $table = "bookings_shop_{$shop->id}";
        OwnerShopController::ensureBookingTableExists($shop->id);

        $data['date_of_booking'] = trim($data['date_of_booking']);
        $data['time_of_booking'] = trim($data['time_of_booking']);

        // Check for overlaps
        if ($error = $this->checkForOverlap($table, $data['date_of_booking'], $data['time_of_booking'], $data['slot_number'], $shop->id)) {
            return back()->withErrors(['time_of_booking' => $error])->withInput();
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
        $shop = $this->formatShopCloudinaryUrls($shop);

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
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'size_of_the_car' => 'required|in:HatchBack,Sedan,MPV,SUV,Pickup,Van,Motorcycle',
            'contact_no' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'date_of_booking' => 'required|date_format:Y-m-d',
            'time_of_booking' => ['required', 'date_format:H:i', 'regex:/^[0-2][0-3]:[0-5][0-9]$/'],
            'slot_number' => 'required|integer|min:1|max:4',
        ]);

        $shop = CarWashShop::findOrFail($shopId);
        $shop = $this->formatShopCloudinaryUrls($shop);

        $table = "bookings_shop_{$shop->id}";
        OwnerShopController::ensureBookingTableExists($shop->id);

        $data['date_of_booking'] = trim($data['date_of_booking']);
        $data['time_of_booking'] = trim($data['time_of_booking']);

        // Check for overlaps
        if ($error = $this->checkForOverlap($table, $data['date_of_booking'], $data['time_of_booking'], $data['slot_number'], $shop->id)) {
            return back()->withErrors(['time_of_booking' => $error])->withInput();
        }

        return Inertia::render('Public/PaymentPage', [
            'shop' => $shop,
            'booking' => $data,
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
            'date_of_booking' => 'required|date_format:Y-m-d',
            'time_of_booking' => ['required', 'date_format:H:i', 'regex:/^[0-2][0-3]:[0-5][0-9]$/'],
            'slot_number' => 'required|integer|min:1|max:4',
            'payment_amount' => 'required|numeric|in:50',
            'payment_proof' => 'required|string|max:1000', // now just Cloudinary URL
        ]);

        $shop = CarWashShop::findOrFail($shopId);
        $shop = $this->formatShopCloudinaryUrls($shop);

        $table = "bookings_shop_{$shop->id}";
        OwnerShopController::ensureBookingTableExists($shop->id);

        $data['date_of_booking'] = trim($data['date_of_booking']);
        $data['time_of_booking'] = trim($data['time_of_booking']);

        // Check for overlaps
        if ($error = $this->checkForOverlap($table, $data['date_of_booking'], $data['time_of_booking'], $data['slot_number'], $shop->id)) {
            return back()->withErrors(['time_of_booking' => $error])->withInput();
        }

        $now = now();
        $paymentProofUrl = $data['payment_proof']; // Cloudinary URL directly

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
                'payment_proof' => $paymentProofUrl,
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
                'payment_proof' => $paymentProofUrl,
                'payment_status' => 'paid',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return redirect()->route('dashboard')
            ->with('success', "Booked on slot #{$data['slot_number']} at {$shop->name}. Payment proof saved.");
    }
}
