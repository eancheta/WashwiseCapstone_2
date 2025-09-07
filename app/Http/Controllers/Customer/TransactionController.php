<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CarWashShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;      // <- imported Log facade
use Inertia\Inertia;

class TransactionController extends Controller
{
    /**
     * Show authenticated customer's bookings across all bookings_shop_{id} tables.
     * Renders resources/js/Pages/settings/Appearance.vue via Inertia.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $userId = $user->id;
        $userEmail = $user->email;

        // Find all tables matching bookings_shop_%
        // Note: double-escaped backslash so PHP string contains \_
        $rows = DB::select("SHOW TABLES LIKE 'bookings_shop\\_%'");

        $tables = array_map(function ($row) {
            $vals = array_values((array) $row);
            return $vals[0] ?? null;
        }, $rows);

        $bookings = collect();

        foreach ($tables as $table) {
            if (! $table) {
                continue;
            }

            try {
                // Try by user_id first
                $found = DB::table($table)
                    ->where('user_id', $userId)
                    ->orderByDesc('created_at')
                    ->get();

                // Fallback to email if nothing found
                if ($found->isEmpty() && $userEmail) {
                    $found = DB::table($table)
                        ->where('email', $userEmail)
                        ->orderByDesc('created_at')
                        ->get();
                }

                if ($found->isEmpty()) {
                    continue;
                }

                // get shop id from table name e.g. bookings_shop_3 -> 3
                preg_match('/bookings_shop_(\d+)/', $table, $m);
                $shopId = $m[1] ?? null;
                $shop = $shopId ? CarWashShop::find($shopId) : null;

                foreach ($found as $f) {
                    $bookings->push([
                        'id' => $f->id,
                        'name' => $f->name,
                        'size_of_the_car' => $f->size_of_the_car,
                        'contact_no' => $f->contact_no,
                        'date_of_booking' => $f->date_of_booking,
                        'time_of_booking' => $f->time_of_booking,
                        'slot_number' => $f->slot_number,
                        'payment_amount' => $f->payment_amount ?? null,
                        'payment_status' => $f->status ?? 'pending',
                        'shop' => $shop ? [
                            'id' => $shop->id,
                            'name' => $shop->name,
                            'address' => $shop->address ?? null,
                        ] : [
                            'id' => $shopId,
                            'name' => 'Unknown shop',
                            'address' => null,
                        ],
                        'created_at' => $f->created_at ?? null,
                    ]);
                }
            } catch (\Exception $e) {
                // log and continue (keeps diagnostics for you)
                Log::warning("Error reading table {$table}: " . $e->getMessage());
                continue;
            }
        }

        $bookings = $bookings->sortByDesc('created_at')->values()->all();

        return Inertia::render('settings/Appearance', [
            'bookings' => $bookings,
            'pageTitle' => 'Transactions',
        ]);
    }
}
