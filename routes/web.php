<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/emailvcode', function () {
    return Inertia::render('EmailVerificationCode');
})->name('emailvcode');


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
