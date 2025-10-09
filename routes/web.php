<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

// Controllers
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
use App\Http\Controllers\OwnerAppointmentController;
use App\Http\Controllers\Owner\WalkinController;
use App\Http\Controllers\Customer\FeedbackController;
use App\Http\Controllers\Owner\ReviewController;
use App\Http\Controllers\Auth\RegisteredUserController;


Route::get('/debug-send-customer', function () {
    $controller = app(RegisteredUserController::class);
    // change the email to one you control for testing
    $resp = $controller->sendVerificationCode('cireancheta2003@gmail.com', 'Debug Test', rand(100000, 999999));
    return response()->json($resp);
});

/*
|--------------------------------------------------------------------------
| CUSTOMER AUTH (Register → Verify → Login)
|--------------------------------------------------------------------------
*/

// Register new customer
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

// Customer verification (code input)
Route::get('/emailvcode', [RegisteredUserController::class, 'showVerificationPage'])->name('emailvcode');
Route::post('/verify-code', [RegisteredUserController::class, 'verifyCode'])->name('verify.code');

// Login / Logout
Route::get('/login', [RegisteredUserController::class, 'showLogin'])->name('login');
Route::post('/login', [RegisteredUserController::class, 'login'])->name('login.submit');
Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| OWNER AUTH (Unchanged)
|--------------------------------------------------------------------------
*/
Route::prefix('owner')->group(function () {
    Route::get('/register', [OwnerAuthController::class, 'create'])->name('owner.register.show');
    Route::post('/register', [OwnerAuthController::class, 'store'])->name('owner.register');
    Route::get('/login', [OwnerAuthController::class, 'showLogin'])->name('owner.login.show');
    Route::post('/login', [OwnerAuthController::class, 'login'])->name('owner.login');
    Route::get('/verify', [OwnerVerificationController::class, 'show'])->name('owner.verify.show');
    Route::post('/verify', [OwnerVerificationController::class, 'verify'])->name('owner.verify.submit');
    Route::post('/verify/resend', [OwnerVerificationController::class, 'resend'])->name('owner.verify.resend');

    Route::middleware(['auth:carwashowner'])->group(function () {
        Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('carwashownerdashboard');
        Route::get('/appointments', [OwnerAppointmentController::class, 'index'])->name('owner.appointments');
        Route::post('/appointments/{id}/approve', [OwnerAppointmentController::class, 'approve'])->name('owner.appointments.approve');
        Route::post('/appointments/{id}/decline', [OwnerAppointmentController::class, 'decline'])->name('owner.appointments.decline');
        Route::get('/shop/create', [OwnerShopController::class, 'create'])->name('owner.shop.create');
        Route::post('/shop', [OwnerShopController::class, 'store'])->name('owner.shop.store');
        Route::get('/reviews', [ReviewController::class, 'index'])->name('owner.reviews');

        Route::post('/shop/{id}/close', [OwnerShopController::class, 'closeShop'])->name('owner.shop.close');
        Route::post('/shop/{id}/open', [OwnerShopController::class, 'openShop'])->name('owner.shop.open');
    });
});

/*
|--------------------------------------------------------------------------
| PUBLIC + CUSTOMER BOOKING
|--------------------------------------------------------------------------
*/
Route::get('/shops/{shop}/availability', [ShopBookingController::class, 'availability'])->name('shops.bookings.availability');
Route::post('/shops/{shop}/book', [ShopBookingController::class, 'store'])->name('shops.bookings.store');
Route::get('/customer/book/{shop}/payment', [ShopBookingController::class, 'showPaymentPage'])->name('customer.book.payment.show');
Route::post('/customer/book/{shop}/payment', [ShopBookingController::class, 'paymentPage'])->name('customer.book.payment');
Route::post('/customer/book/{shop}/confirm', [ShopBookingController::class, 'confirmBooking'])->name('customer.book.confirm');

Route::post('/shop/{shop}/book', [PublicBookingController::class, 'store'])->name('public.customer.book');
Route::get('/shop/{id}', [OwnerShopController::class, 'show'])->name('shop.show');
Route::get('/shop/{id}/book', [OwnerShopController::class, 'bookPage'])->name('shop.book');
Route::get('/public/shop/{id}/booking', [OwnerShopController::class, 'bookPage'])->name('public.shop.booking');
Route::post('/public/shop/{id}/submit-booking', [OwnerShopController::class, 'submitBooking'])->name('public.shop.submitBooking');

Route::middleware(['auth'])->group(function () {
    Route::get('/customer/book/{shop}', [CustomerBookingController::class, 'create'])->name('customer.book.create');
    Route::post('/customer/book/{shop}', [CustomerBookingController::class, 'store'])->name('customer.book.store');
});

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [CustomerDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/loginAdmin', [StaticLoginController::class, 'showLogin'])->name('loginAdmin');
Route::post('/loginAdmin', [StaticLoginController::class, 'login']);
Route::get('/admindashboard', [AdminController::class, 'index'])->name('admindashboard');
Route::post('/admin/owners/{id}/approve', [AdminController::class, 'approve'])->name('owners.approve');
Route::post('/admin/owners/{id}/decline', [AdminController::class, 'decline'])->name('owners.decline');

/*
|--------------------------------------------------------------------------
| STATIC PAGES
|--------------------------------------------------------------------------
*/
Route::get('/about', fn() => Inertia::render('AboutUs'))->name('about');
Route::get('/', fn() => Inertia::render('Welcome'))->name('home');

/*
|--------------------------------------------------------------------------
| DEBUG / TEST
|--------------------------------------------------------------------------
*/
Route::get('/test-auth', function () {
    return response()->json([
        'user' => Auth::user() ? Auth::user()->toArray() : null,
        'shops' => DB::table('car_wash_shops')->get()->toArray(),
        'owners' => DB::table('car_wash_owners')->where('status', 'approved')->get()->toArray(),
    ]);
})->middleware('auth');

Route::get('/debug-send-mail', function () {
    $payload = [
        'sender' => ['name' => env('MAIL_FROM_NAME', 'WashWise'), 'email' => env('MAIL_FROM_ADDRESS')],
        'to' => [['email' => 'Cireancheta2003@gmail.com', 'name' => 'Test']],
        'subject' => 'Debug: sendinblue test',
        'htmlContent' => '<p>Debug send from app</p>'
    ];

    $resp = Http::withHeaders([
        'api-key' => env('SENDINBLUE_API_KEY'),
        'Accept' => 'application/json',
    ])->post('https://api.sendinblue.com/v3/smtp/email', $payload);

    Log::info('debug-send-mail response', ['status' => $resp->status(), 'body' => $resp->body()]);
    return response()->json(['status' => $resp->status(), 'body' => $resp->body()]);
});

/*
|--------------------------------------------------------------------------
| OWNER WALK-IN + CUSTOMER FEEDBACK
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:carwashowner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/walkin', [WalkinController::class, 'create'])->name('walkin');
    Route::post('/walkin', [WalkinController::class, 'store'])->name('walkin.store');
});

Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/feedback/{shop}', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback/{shop}', [FeedbackController::class, 'store'])->name('feedback.store');
});

/*
|--------------------------------------------------------------------------
| ADDITIONAL ROUTE FILES
|--------------------------------------------------------------------------
*/
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
