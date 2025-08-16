<?php

namespace App\Http\Controllers;

use App\Models\CarWashShop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ShopBookingController extends Controller
{
    /** GET /shops/{shop}/availability?date=YYYY-MM-DD */
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

    /** POST /shops/{shop}/book */
    public function store(Request $request, int $shopId)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'size_of_the_car' => 'required|in:HatchBack,Sedan,MPV,SUV,Pickup,Van,Motorcycle',
            'contact_no' => 'required|string|max:20',
            'date_of_booking' => 'required|date',
            'time_of_booking' => 'required|date_format:H:i',
        ]);

        $shop = CarWashShop::findOrFail($shopId);
        $table = "bookings_shop_{$shop->id}";

        // Ensure per-shop booking table exists
        OwnerShopController::ensureBookingTableExists($shop->id);

        $newStart = Carbon::createFromFormat('Y-m-d H:i', $data['date_of_booking'].' '.$data['time_of_booking']);
        $newEnd   = (clone $newStart)->addHours(3);

        // Assign the first slot (1..4) that has no overlap with the new booking
        $assignedSlot = null;

        for ($slot = 1; $slot <= 4; $slot++) {
            $existing = DB::table($table)
                ->where('date_of_booking', $data['date_of_booking'])
                ->where('slot_number', $slot)
                ->orderBy('time_of_booking')
                ->get();

            $overlap = false;
            foreach ($existing as $b) {
                // existing start/end
                $start = Carbon::createFromFormat('Y-m-d H:i', $data['date_of_booking'].' '.$b->time_of_booking);
                $end   = (clone $start)->addHours(3);

                // overlap if existing_start < new_end AND new_start < existing_end
                if ($start->lt($newEnd) && $newStart->lt($end)) {
                    $overlap = true;
                    break;
                }
            }

            if (!$overlap) {
                $assignedSlot = $slot;
                break;
            }
        }

        if (!$assignedSlot) {
            return back()->withErrors([
                'time_of_booking' => 'All 4 slots are occupied within 3 hours of that time. Please choose another time.'
            ]);
        }

        DB::table($table)->insert([
            'name' => $data['name'],
            'size_of_the_car' => $data['size_of_the_car'],
            'contact_no' => $data['contact_no'],
            'time_of_booking' => $data['time_of_booking'],
            'date_of_booking' => $data['date_of_booking'],
            'slot_number' => $assignedSlot,
            'created_at' => now(),
        ]);

        return back()->with('success', "Booked on slot #{$assignedSlot}.");
    }
}
