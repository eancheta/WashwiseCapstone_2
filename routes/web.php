<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Auth\EmailVerificationController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// 🛡 Dashboard - Requires authentication and verified email
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ✉ Email Verification Code Page (Vue Component)
Route::get('/emailvcode', function () {
    return Inertia::render('EmailVerificationCode');
})->middleware(['auth'])->name('emailvcode');

// 🔐 Login Page
Route::get('/login', function () {
    return Inertia::render('auth/Login');
})->name('login');

// 📝 Register Page
Route::get('/register', function () {
    return Inertia::render('auth/Register');
})->name('register');

// ✅ Submit email verification code (POST)
Route::post('/verify-code', [EmailVerificationController::class, 'verify'])
    ->middleware(['auth']) // Secure the route
    ->name('verify.code');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
