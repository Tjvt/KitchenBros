<?php

use App\Http\Controllers\Admin\LunchRegistrationController as AdminLunchRegistrationController;
use App\Http\Controllers\LunchRegistrationController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome', [
    'lunchRegistered' => fn (): bool => session()->has('lunch_registration_id'),
])->name('home');

Route::post('/mitessen', [LunchRegistrationController::class, 'store'])
    ->middleware('throttle:6,1')->name('lunch.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', AdminLunchRegistrationController::class)
        ->middleware('can:access-admin')->name('dashboard');
});

require __DIR__.'/settings.php';
