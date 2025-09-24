<?php

namespace App\Http\Controllers;

use App\Models\CarWashShop;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Str; // ✅ New: Import the Str facade

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $shops = CarWashShop::join('car_wash_owners', 'car_wash_shops.owner_id', '=', 'car_wash_owners.id')
            ->where('car_wash_owners.status', 'approved')
            ->select(
                'car_wash_shops.id',
                'car_wash_shops.name',
                'car_wash_shops.address',
                'car_wash_shops.district',
                'car_wash_shops.logo',
                'car_wash_shops.qr_code'
            )
            ->get();

        // ✅ IMPORTANT: Manually clean the URLs before passing to the view
        $shops->transform(function ($shop) {
            // Fix the logo URL
            if ($shop->logo && !str_starts_with($shop->logo, 'http')) {
                // If it's a relative path, prepend the storage URL
                $shop->logo = url('storage/' . $shop->logo);
            }

            // Fix the QR code URL
            if ($shop->qr_code && !str_starts_with($shop->qr_code, 'http')) {
                $shop->qr_code = url('storage/' . $shop->qr_code);
            }

            // If the URL contains the local storage prefix + https, correct it
            // ✅ Replaced str_after with Str::after
            if ($shop->logo && Str::contains($shop->logo, 'storage/https')) {
                $shop->logo = Str::after($shop->logo, 'storage/');
            }
            if ($shop->qr_code && Str::contains($shop->qr_code, 'storage/https')) {
                $shop->qr_code = Str::after($shop->qr_code, 'storage/');
            }

            return $shop;
        });

        $districts = $shops->pluck('district')->unique()->values();

        return Inertia::render('Dashboard', [
            'shops' => $shops,
            'districts' => $districts,
            'auth' => [
                'user' => Auth::user(),
            ]
        ]);
    }
}
