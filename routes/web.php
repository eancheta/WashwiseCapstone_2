<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\StaticLoginController;


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

Route::get('/admindashboard', function(){
    if( ! Session::get('authenticated') ){
        return redirect()->route('loginAdmin');
    }
    return Inertia::render('settings/AdminDashboard');
})->name('admindashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
