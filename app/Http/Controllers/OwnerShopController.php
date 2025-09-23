<?php

namespace App\Http\Controllers;

use App\Models\CarWashShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Cloudinary\Cloudinary;

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

        // Initialize Cloudinary SDK if env values exist
        $cloudinary = null;
        if (env('CLOUDINARY_CLOUD_NAME') && env('CLOUDINARY_API_KEY') && env('CLOUDINARY_API_SECRET')) {
            try {
                $cloudinary = new Cloudinary([
                    'cloud' => [
                        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                        'api_key'    => env('CLOUDINARY_API_KEY'),
                        'api_secret' => env('CLOUDINARY_API_SECRET'),
                    ],
                    'url' => ['secure' => true],
                ]);
            } catch (\Throwable $e) {
                Log::warning('Cloudinary init failed: '.$e->getMessage());
                $cloudinary = null;
            }
        }

        // helper closure: upload to Cloudinary (preferred) else local public disk
        $uploadFile = function ($file, $folder) use ($cloudinary) {
            // Cloudinary path (preferred)
            if ($cloudinary) {
                try {
                    $result = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                        'folder' => $folder,
                        'resource_type' => 'image',
                    ]);
                    if (!empty($result['secure_url'])) {
                        // store full https URL in DB
                        return $result['secure_url'];
                    } elseif (!empty($result['url'])) {
                        return $result['url'];
                    }
                } catch (\Throwable $e) {
                    Log::error('Cloudinary upload failed: '.$e->getMessage());
                    // fall through to local storage
                }
            }

            // Local fallback: store on public disk
            try {
                $path = $file->store($folder, 'public'); // returns e.g. carwash_logos/abc.jpg
                // return Storage::url($path) => "/storage/carwash_logos/abc.jpg"
                return Storage::url($path);
            } catch (\Throwable $e) {
                Log::error('Local upload failed: '.$e->getMessage());
                return null;
            }
        };

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $uploaded = $uploadFile($logo, 'carwash_logos');
            if ($uploaded) $shopData['logo'] = $uploaded;
        }

        if ($request->hasFile('qr_code')) {
            $qr = $request->file('qr_code');
            $uploaded = $uploadFile($qr, 'carwash_qrcodes');
            if ($uploaded) $shopData['qr_code'] = $uploaded;
        }

        $shop = CarWashShop::create($shopData);
        self::ensureBookingTableExists($shop->id);

        return redirect()->route('carwashownerdashboard')->with('success', 'Shop created successfully!');
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
