<?php

namespace App\Http\Controllers;

use App\Models\CarWashShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Inertia\Inertia;

class OwnerShopController extends Controller
{
    /**
     * Ensure the bookings table for a specific shop exists.
     */
    public static function ensureBookingTableExists($shopId)
    {
        $tableName = "bookings_shop_{$shopId}";

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('size_of_the_car');
                $table->string('contact_no');
                $table->string('email')->nullable(); // Added email
                $table->time('time_of_booking');
                $table->date('date_of_booking');
                $table->integer('slot_number');
                $table->string('payment_proof')->nullable();
                $table->decimal('payment_amount', 8, 2)->nullable();
                $table->string('status')->default('pending');
                $table->timestamps();
            });
        }
    }

    /**
     * Display the form to create a new shop.
     */
    public function create()
    {
        return Inertia::render('Owner/ShopSetup', [
            'pageTitle' => 'Create Car Wash Shop',
        ]);
    }

    /**
     * Store a new shop for the authenticated owner.
     */
    public function store(Request $request)
    {
        $ownerId = Auth::guard('carwashowner')->id();
        if (!$ownerId) {
            return redirect()->route('owner.login.show');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'district' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'services_offered' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'qr_code' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $shopData = [
            'owner_id' => $ownerId,
            'name' => $validated['name'],
            'address' => $validated['address'],
            'district' => $validated['district'],
            'description' => $validated['description'],
            'services_offered' => $validated['services_offered'],
        ];

        if ($request->hasFile('logo')) {
            $shopData['logo'] = $request->file('logo')->store('logos', 'public');
        }

        if ($request->hasFile('qr_code')) {
            $shopData['qr_code'] = $request->file('qr_code')->store('qr_codes', 'public');
        }

        $shop = CarWashShop::create($shopData);

        // Create bookings table for the new shop
        self::ensureBookingTableExists($shop->id);

                return redirect()->route('carwashownerdashboard')
            ->with('success', 'Shop created successfully!');
    }

    /**
     * Display the owner's shop details.
     */
    public function index()
    {
        $ownerId = Auth::guard('carwashowner')->id();
        if (!$ownerId) {
            return redirect()->route('owner.login.show');
        }

        $shop = CarWashShop::where('owner_id', $ownerId)->first();

        return Inertia::render('Public/ShopBooking', [
            'shop' => $shop,
            'pageTitle' => "Book at {$shop->name}",
        ]);
    }
}
