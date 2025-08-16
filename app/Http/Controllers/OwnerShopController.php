<?php

namespace App\Http\Controllers;

use App\Models\CarWashShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Inertia\Inertia;

class OwnerShopController extends Controller
{
    // Show the create shop page
    public function create()
    {
        return Inertia::render('Owner/ShopSetup', [
            'pageTitle' => 'Create Car Wash Shop',
        ]);
    }

    // Store new shop
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'district' => 'required|integer|min:1|max:6',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'description' => 'nullable|string',
            'services_offered' => 'nullable|string',
            'qr_code' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('carwash_logos', 'public');
        }

        if ($request->hasFile('qr_code')) {
            $data['qr_code'] = $request->file('qr_code')->store('carwash_qrcodes', 'public');
        }

        $data['owner_id'] = Auth::guard('carwashowner')->id();

        $shop = CarWashShop::create($data);

        self::ensureBookingTableExists($shop->id);

        return redirect()->route('carwashownerdashboard')
            ->with('success', 'Shop created successfully!');
    }

    // Show shop details
    public function show(int $id)
    {
        $shop = CarWashShop::findOrFail($id);
        return Inertia::render('Public/ShopView', [
            'shop' => $shop,
            'pageTitle' => $shop->name,
        ]);
    }

    // Show booking page
    public function bookPage(int $id)
    {
        $shop = CarWashShop::findOrFail($id);
        return Inertia::render('Public/ShopBooking', [
            'shop' => $shop,
            'pageTitle' => "Book at {$shop->name}",
        ]);
    }

    // Ensure each shop has its own booking table
    public static function ensureBookingTableExists(int $shopId): void
    {
        $table = "bookings_shop_{$shopId}";
        if (!Schema::hasTable($table)) {
            Schema::create($table, function (Blueprint $t) {
                $t->bigIncrements('id');
                $t->unsignedBigInteger('customer_id')->nullable();
                $t->string('name');
                $t->enum('size_of_the_car', [
                    'HatchBack', 'Sedan', 'MPV', 'SUV', 'Pickup', 'Van', 'Motorcycle'
                ]);
                $t->string('contact_no', 20);
                $t->time('time_of_booking');
                $t->date('date_of_booking');
                $t->unsignedTinyInteger('slot_number');
                $t->timestamp('created_at')->useCurrent();
                $t->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
                $t->foreign('customer_id')->references('id')->on('users')->onDelete('set null');
            });
        }
    }
}
