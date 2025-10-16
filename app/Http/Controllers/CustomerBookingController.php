<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Illuminate\Database\QueryException;

class CustomerBookingController extends Controller
{
    // Show booking page via Inertia
    public function create($shop)
    {
        Log::debug('Attempting to load booking page for shop ID: ' . $shop, [
            'user' => Auth::user() ? Auth::user()->toArray() : null
        ]);

        $shopData = DB::table('car_wash_shops')->where('id', $shop)->first();
        Log::info('Shop data for ID ' . $shop . ':', (array) $shopData);

        if (!$shopData) {
            Log::warning('Shop not found for ID: ' . $shop);
            return redirect()->route('dashboard')->with('error', 'Shop not found');
        }

        return Inertia::render('Booking', [
            'shop' => $shopData,
            'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : [],
        ]);
    }

    // Handle form submission
    public function store(Request $request, $shop)
    {
        Log::debug('Processing booking submission for shop ID: ' . $shop, [
            'user' => Auth::user() ? Auth::user()->toArray() : null
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'size_of_the_car' => 'required|string|in:HatchBack,Sedan,MPV,SUV,Pickup,Van,Motorcycle',
            'contact_no' => 'required|string|max:20',
            'time_of_booking' => 'required',
            'date_of_booking' => 'required|date',
            'slot_number' => 'required|integer|min:1',
            'services_offered' => 'nullable|string', // ✅ added line
        ]);

        // Ensure shop exists
        $shopData = DB::table('car_wash_shops')->where('id', $shop)->first();
        if (!$shopData) {
            Log::warning('Shop not found for ID: ' . $shop);
            return redirect()->route('dashboard')->with('error', 'Shop not found');
        }

        $tableName = 'bookings_shop_' . $shop;

        // Ensure bookings table exists
        if (!Schema::hasTable($tableName)) {
            Log::warning('Bookings table not found: ' . $tableName);
            return redirect()->route('dashboard')->with('error', 'Bookings table not found for this shop.');
        }

        // Prepare data to insert
        $bookingData = array_merge($validated, [
            'created_at' => now(),
            'services_offered' => $request->input('services_offered'), // ✅ this ensures value is stored
        ]);

        Log::debug('Booking data prepared for insertion:', $bookingData);

        try {
            DB::table($tableName)->insert($bookingData);
            Log::info('Booking saved for shop ID: ' . $shop);
            return redirect()->route('dashboard')->with('success', 'Booking saved!');
        } catch (QueryException $e) {
            Log::error('Failed to save booking for shop ID: ' . $shop, [
                'error_message' => $e->getMessage(),
                'booking_data' => $bookingData,
            ]);

            return redirect()->route('dashboard')->with('error', 'Failed to save booking. Please try again.');
        }
    }
}
