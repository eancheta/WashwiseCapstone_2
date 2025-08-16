<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\StaticLoginController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\OwnerAuthController;
use App\Http\Controllers\Auth\OwnerVerificationController;
use App\Http\Controllers\Auth\OwnerDashboardController;
use App\Http\Controllers\AdminOwnerTable;
use App\Http\Controllers\OwnerShopController;
use App\Http\Controllers\ShopBookingController;
use App\Http\Controllers\CustomerBookingController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\PublicBookingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Test route for debugging authentication and shop data
Route::get('/test-auth', function () {
    return response()->json([
        'user' => Auth::user() ? Auth::user()->toArray() : null,
        'shops' => DB::table('car_wash_shops')->get()->toArray(),
        'owners' => DB::table('car_wash_owners')->where('status', 'approved')->get()->toArray(),
    ]);
})->middleware('auth');

Route::post('/shop/{shop}/book', [PublicBookingController::class, 'store'])
    ->name('public.customer.book');
// Booking page for a shop
Route::middleware(['auth'])->group(function () {
    // Inertia page for booking
    Route::get('/customer/book/{shop}', [CustomerBookingController::class, 'create'])
        ->name('customer.book.create');

    // POST form submission handled by Inertia
    Route::post('/customer/book/{shop}', [CustomerBookingController::class, 'store'])
        ->name('customer.book.store');
});
Route::prefix('owner')->group(function () {
    // Show the shop setup form
    Route::get('/shop/create', [OwnerShopController::class, 'create'])->name('owner.shop.create');

    // Store the new shop
    Route::post('/shop', [OwnerShopController::class, 'store'])->name('owner.shop.store');
});

// Public viewing routes
Route::get('/shop/{id}', [OwnerShopController::class, 'show'])->name('shop.show');
Route::get('/shop/{id}/book', [OwnerShopController::class, 'bookPage'])->name('shop.book');

// Availability + booking actions (public endpoints – add rate limits/captcha if needed)
Route::get('/shops/{shop}/availability', [ShopBookingController::class, 'availability'])->name('shops.bookings.availability');
Route::post('/shops/{shop}/book', [ShopBookingController::class, 'store'])->name('shops.bookings.store');

/*
|--------------------------------------------------------------------------
| Added routes to match controller calls
|--------------------------------------------------------------------------
*/
Route::get('/public/shop/{id}/booking', [OwnerShopController::class, 'bookPage'])->name('public.shop.booking');
Route::post('/public/shop/{id}/submit-booking', [OwnerShopController::class, 'submitBooking'])->name('public.shop.submitBooking');

Route::get('/owner/register', [OwnerAuthController::class, 'create'])->name('owner.register.show');
Route::post('/owner/register', [OwnerAuthController::class, 'store'])->name('owner.register');

Route::get('/owner/verify', [OwnerVerificationController::class, 'show'])->name('owner.verify.show');
Route::post('/owner/verify', [OwnerVerificationController::class, 'verify'])->name('owner.verify.submit');
Route::post('/owner/verify/resend', [OwnerVerificationController::class, 'resend'])->name('owner.verify.resend');

Route::get('/owner/login', [OwnerAuthController::class, 'showLogin'])->name('owner.login.show');
Route::post('/owner/login', [OwnerAuthController::class, 'login'])->name('owner.login');

Route::get('/admindashboard', [AdminController::class, 'index'])->name('admindashboard');
Route::post('/admin/owners/{id}/approve', [AdminController::class, 'approve']);
Route::post('/admin/owners/{id}/decline', [AdminController::class, 'decline']);

Route::middleware(['auth:carwashowner'])->group(function () {
    Route::get('/carwashowner/dashboard', [OwnerDashboardController::class, 'index'])->name('carwashownerdashboard');
});

Route::get('/about', function () {
    return Inertia::render('AboutUs');
});

Route::get('/', fn() => Inertia::render('Welcome'))->name('home');

Route::get('/dashboard', [CustomerDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [CustomerAuthController::class, 'login'])->name('login.submit');

Route::get('/register', function () {
    return Inertia::render('auth/Register');
})->name('register');

Route::get('/emailvcode', fn() => Inertia::render('EmailVerificationCode'))
    ->middleware('auth')
    ->name('emailvcode');

Route::post('/verify-code', [EmailVerificationController::class, 'verify'])
    ->middleware('auth')
    ->name('verify.code');

Route::get('/loginAdmin', [StaticLoginController::class, 'showLogin'])
    ->name('loginAdmin');

Route::post('/loginAdmin', [StaticLoginController::class, 'login']);
Route::post('/logout', [CustomerAuthController::class, 'logout'])
    ->name('logout');

Route::get('/about-us', function () {
    return Inertia::render('AboutUs');
})->name('about');

Route::post('/owners/{id}/approve', [AdminController::class, 'approve'])->name('owners.approve');
Route::post('/owners/{id}/decline', [AdminController::class, 'decline'])->name('owners.decline');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
