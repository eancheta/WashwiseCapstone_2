<?php

namespace App\Http\Controllers;

use App\Models\CarWashShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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

        // ---------- File upload closure ----------
        $uploadFile = function ($file, $folder) {
            // Check Cloudinary config
            $cloudinaryConfigured = config('cloudinary.cloud.cloud_name') && config('cloudinary.cloud.api_key');

            if ($cloudinaryConfigured && class_exists(Cloudinary::class)) {
                try {
                    $result = Cloudinary::upload($file->getRealPath(), [
                        'folder' => $folder,
                        'overwrite' => true,
                        'resource_type' => 'image',
                    ]);

                    $secureUrl = $result->getSecurePath();
                    if ($secureUrl) {
                        return $secureUrl;
                    }
                } catch (\Throwable $e) {
                    Log::error('Cloudinary upload failed: ' . $e->getMessage());
                }
            }

            // Fallback: store locally
            try {
                return $file->store($folder, 'public'); // returns e.g., carwash_logos/abc.jpg
            } catch (\Throwable $e) {
                Log::error('Local storage upload failed: ' . $e->getMessage());
                return null;
            }
        };

        // Upload logo
        if ($request->hasFile('logo')) {
            $shopData['logo'] = $uploadFile($request->file('logo'), 'carwash_logos');
        }

        // Upload QR code
        if ($request->hasFile('qr_code')) {
            $shopData['qr_code'] = $uploadFile($request->file('qr_code'), 'carwash_qrcodes');
        }

        $shop = CarWashShop::create($shopData);

        // Create or update bookings table
        self::ensureBookingTableExists($shop->id);

        return redirect()->route('carwashownerdashboard')
            ->with('success', 'Shop created successfully!');
    }

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
