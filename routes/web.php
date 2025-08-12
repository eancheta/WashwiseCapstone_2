<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\StaticLoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\OwnerAuthController;
use App\Http\Controllers\Auth\OwnerVerificationController;
use App\Http\Controllers\Auth\OwnerDashboardController;
use App\Http\Controllers\AdminOwnerTable;


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

Route::get('/dashboard', fn() => Inertia::render('Dashboard'))
    ->middleware(['auth','verified'])
    ->name('dashboard');

Route::get('/login', function () {
    return Inertia::render('auth/Login');
})->name('login');

Route::get('/register', function () {
    return Inertia::render('auth/Register');
})->name('register');

Route::get('/emailvcode', fn() => Inertia::render('EmailVerificationCode'))
    ->middleware('auth')
    ->name('emailvcode');

Route::post('/verify-code', [EmailVerificationController::class,'verify'])
    ->middleware('auth')
    ->name('verify.code');

Route::get('/loginAdmin', [StaticLoginController::class,'showLogin'])
    ->name('loginAdmin');

Route::post('/loginAdmin', [StaticLoginController::class,'login']);
Route::post('/logout',     [StaticLoginController::class,'logout'])
    ->name('logout');


Route::get('/about-us', function () {
    return Inertia::render('AboutUs');
})->name('about');


Route::post('/owners/{id}/approve', [AdminController::class, 'approve'])->name('owners.approve');
Route::post('/owners/{id}/decline', [AdminController::class, 'decline'])->name('owners.decline');


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
