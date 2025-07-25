<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Auth\EmailVerificationController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::get('/emailvcode', function () {
    return Inertia::render('EmailVerificationCode');
})->middleware(['auth'])->name('emailvcode');


Route::get('/login', function () {
    return Inertia::render('auth/Login');
})->name('login');


Route::get('/register', function () {
    return Inertia::render('auth/Register');
})->name('register');


Route::post('/verify-code', [EmailVerificationController::class, 'verify'])
    ->middleware(['auth'])
    ->name('verify.code');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
