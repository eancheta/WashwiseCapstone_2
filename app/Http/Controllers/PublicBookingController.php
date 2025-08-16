<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CarWashShop;

class PublicBookingController extends Controller
{
    public function store(Request $request, CarWashShop $shop)
    {
        // Validate
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'size_of_the_car' => 'required|string',
            'contact_no' => 'required|string|max:20',
            'time_of_booking' => 'required',
            'date_of_booking' => 'required|date',
            'slot_number' => 'required|integer|min:1'
        ]);

        // Dynamic table name (per shop)
        $tableName = 'bookings_shop_' . $shop->id;

        // Insert booking
        DB::table($tableName)->insert([
            'name' => $validated['name'],
            'size_of_the_car' => $validated['size_of_the_car'],
            'contact_no' => $validated['contact_no'],
            'time_of_booking' => $validated['time_of_booking'],
            'date_of_booking' => $validated['date_of_booking'],
            'slot_number' => $validated['slot_number'],
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Booking submitted successfully!');
    }
}
