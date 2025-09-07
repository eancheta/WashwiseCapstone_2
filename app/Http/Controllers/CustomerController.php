<?php

namespace App\Http\Controllers;

use App\Models\CarWashShop;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Illuminate\Support\Collection;

class CustomerController extends Controller
{
    public function transactions()
    {
        $shops = CarWashShop::all();
        $bookings = collect();

        foreach ($shops as $shop) {
            $table = "bookings_shop_{$shop->id}";
            if (Schema::hasTable($table)) {
                $shopBookings = DB::table($table)
                    ->where('user_id', Auth::id())
                    ->get()
                    ->map(function ($booking) use ($shop) {
                        $booking->shop = (object) [
                            'id' => $shop->id,
                            'name' => $shop->name,
                            'address' => $shop->address,
                        ];
                        // Handle optional fields with defaults
                        $booking->payment_amount = $booking->payment_amount ?? 0;
                        $booking->payment_status = $booking->payment_status ?? 'unpaid'; // Assuming you add this field or logic
                        return $booking;
                    });
                $bookings = $bookings->merge($shopBookings);
            }
        }

        // Optional: Sort by date descending
        $bookings = $bookings->sortByDesc('date_of_booking');

        return Inertia::render('settings/Appearance', [  // Adjust path to match your Vue component location, e.g., resources/js/Pages/Customer/Transactions.vue
            'bookings' => $bookings->values()->toArray(),
            'message' => $bookings->isEmpty() ? 'You don’t have any bookings yet.' : null,
            'pageTitle' => 'Your Bookings',
        ]);
    }
}
