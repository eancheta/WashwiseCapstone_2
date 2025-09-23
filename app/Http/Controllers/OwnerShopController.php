<?php

namespace App\Http\Controllers;

use App\Models\CarWashShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Inertia\Inertia;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

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
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('name');
                $table->string('size_of_the_car');
                $table->string('contact_no');
                $table->string('email')->nullable();
                $table->time('time_of_booking');
                $table->date('date_of_booking');
                $table->integer('slot_number');
                $table->decimal('payment_amount', 8, 2)->nullable();
                $table->string('status')->default('pending');
                $table->string('payment_proof')->nullable();
                $table->timestamps();
            });
        } else {
            $columns = Schema::getColumnListing($tableName);

            Schema::table($tableName, function (Blueprint $table) use ($columns) {
                if (!in_array('user_id', $columns)) {
                    $table->unsignedBigInteger('user_id')->nullable();
                }
                if (!in_array('email', $columns)) {
                    $table->string('email')->nullable();
                }
                if (!in_array('payment_amount', $columns)) {
                    $table->decimal('payment_amount', 8, 2)->default(50);
                }
                if (!in_array('payment_status', $columns)) {
                    $table->enum('payment_status', ['pending', 'paid'])->default('pending');
                }
                if (!in_array('payment_proof', $columns)) {
                    $table->string('payment_proof')->nullable();
                }
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

        // Upload logo to Cloudinary if provided
        if ($request->hasFile('logo')) {
            $uploadedLogo = Cloudinary::upload($request->file('logo')->getRealPath(), [
                'folder' => 'washwise/logos'
            ])->getSecurePath();

            $shopData['logo'] = $uploadedLogo;
        }

        // Upload QR code to Cloudinary if provided
        if ($request->hasFile('qr_code')) {
            $uploadedQr = Cloudinary::upload($request->file('qr_code')->getRealPath(), [
                'folder' => 'washwise/qr_codes'
            ])->getSecurePath();

            $shopData['qr_code'] = $uploadedQr;
        }

        $shop = CarWashShop::create($shopData);

        // Create or update bookings table for the new shop
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
