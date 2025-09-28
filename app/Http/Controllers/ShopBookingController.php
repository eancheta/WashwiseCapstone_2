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
    private string $cloudBase = 'https://res.cloudinary.com/YOUR_CLOUD_NAME/image/upload/';

    /**
     * Show customer dashboard
     */
    public function customerDashboard()
    {
        $shops = CarWashShop::where('status', 'approved')
            ->get(['id','name','address','district','logo','qr_code','status']); // ✅ include status

        $districts = CarWashShop::distinct()->pluck('district');

        return Inertia::render('Customer/Dashboard', [
            'shops' => $shops,
            'districts' => $districts,
            'auth' => ['user' => Auth::user()],
        ]);
    }

    /**
     * Check for overlapping bookings within a 3-hour window
     */
    private function checkForOverlap(string $table, string $date, string $time, int $slotNumber): ?string
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
     * GET /shops/{shop}/availability
     */
    public function availability(Request $request, int $shopId)
    {
        $request->validate(['date' => 'required|date']);
        $shop = CarWashShop::findOrFail($shopId);
        $table = "bookings_shop_{$shop->id}";

        if (!Schema::hasTable($table)) {
            return response()->json(['bookings' => []]);
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

        return response()->json(['bookings' => $bookings]);
    }

    /**
     * GET /customer/book/{shop}/payment
     */
    public function showPaymentPage(int $shopId)
    {
        $shop = $this->getShopWithCloudinaryImages($shopId);

        // ✅ Block if closed
        if ($shop->status === 'closed') {
            return back()->with('error', "Sorry, {$shop->name} is currently closed.");
        }

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
            'time_of_booking' => ['required','date_format:H:i','regex:/^[0-2][0-3]:[0-5][0-9]$/'],
            'slot_number' => 'required|integer|min:1|max:4',
        ]);

        $shop = $this->getShopWithCloudinaryImages($shopId);

        // ✅ Block if closed
        if ($shop->status === 'closed') {
            return back()->with('error', "Sorry, {$shop->name} is currently closed.");
        }

        // Check overlaps
        $table = "bookings_shop_{$shop->id}";
        $error = $this->checkForOverlap($table, $data['date_of_booking'], $data['time_of_booking'], $data['slot_number']);
        if ($error) return back()->withErrors(['time_of_booking' => $error])->withInput();

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
            'time_of_booking' => ['required','date_format:H:i','regex:/^[0-2][0-3]:[0-5][0-9]$/'],
            'slot_number' => 'required|integer|min:1|max:4',
            'payment_amount' => 'required|numeric|in:50',
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $shop = $this->getShopWithCloudinaryImages($shopId);

        // ✅ Block if closed
        if ($shop->status === 'closed') {
            return back()->with('error', "Sorry, {$shop->name} is currently closed.");
        }

        $table = "bookings_shop_{$shop->id}";

        // Check overlaps
        $error = $this->checkForOverlap($table, $data['date_of_booking'], $data['time_of_booking'], $data['slot_number']);
        if ($error) return back()->withErrors(['time_of_booking' => $error])->withInput();

        // Upload payment proof to Cloudinary
        $cloudinary = new \Cloudinary\Cloudinary(
            ['cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ]]
        );

        $uploadResponse = $cloudinary->uploadApi()->upload(
            $request->file('payment_proof')->getRealPath(),
            ['folder' => 'carwash_payments']
        );

        $paymentProofUrl = $uploadResponse['secure_url'];

        $now = now();

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
                'status' => 'pending',
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
                'status' => 'pending',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return redirect()->route('dashboard')
            ->with('success', "Booked on slot #{$data['slot_number']} at {$shop->name}. Payment proof uploaded.");
    }

    /**
     * Helper: Get shop with Cloudinary images
     */
    private function getShopWithCloudinaryImages(int $shopId)
    {
        $shop = CarWashShop::findOrFail($shopId);

        if ($shop->logo && !str_starts_with($shop->logo, 'http')) {
            $shop->logo = $this->cloudBase . $shop->logo;
        }

        if ($shop->qr_code && !str_starts_with($shop->qr_code, 'http')) {
            $shop->qr_code = $this->cloudBase . $shop->qr_code;
        }

        return $shop;
    }
}
