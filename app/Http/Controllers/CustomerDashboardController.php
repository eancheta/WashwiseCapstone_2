<?php

namespace App\Http\Controllers;

use App\Models\CarWashShop;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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
                'car_wash_shops.qr_code',
                'car_wash_shops.status'
            )
            ->get();
        // Normalize logos/qr and status
        $shops->transform(function ($shop) {
            if ($shop->logo) {
                $shop->logo = str_replace('http://127.0.0.1:8000/storage/', '', $shop->logo);
            }
            if ($shop->qr_code) {
                $shop->qr_code = str_replace('http://127.0.0.1:8000/storage/', '', $shop->qr_code);
            }

            // normalize status (lowercase, trimmed)
            $shop->status = strtolower(trim((string) $shop->status));

            return $shop;
        });

        $districts = $shops->pluck('district')->unique()->values();

        return Inertia::render('Dashboard', [
            'shops' => $shops,
            'districts' => $districts,
            'auth' => [
                'user' => Auth::user(),
            ],
        ]);
    }
}
