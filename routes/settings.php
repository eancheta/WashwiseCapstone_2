<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\TransactionController;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    // Settings redirect
    Route::redirect('settings', '/settings/profile')->name('settings');

    // Profile Routes
    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Password Routes
    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('settings/password', [PasswordController::class, 'update'])->name('password.update');




Route::middleware(['auth'])->group(function () {
    Route::get('/settings/appearance', [TransactionController::class, 'index'])
        ->name('settings.appearance');
});



});
