<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\CarWashShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalkinController extends Controller
{
    public function create()
    {
        return view('owner.walkin');
    }

    public function store(Request $request)
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
            'contact_no' => 'required|string|max:20',
            'size_of_the_car' => 'required|string',
            'date_of_booking' => 'required|date',
            'time_of_booking' => 'required',
            'slot_number' => 'required|integer|min:1|max:4',
        ]);

        $validated['status'] = 'approved';
        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        DB::table($tableName)->insert($validated);

        return redirect()->route('owner.appointments')->with('success', 'Walk-in appointment added successfully.');
    }
}

