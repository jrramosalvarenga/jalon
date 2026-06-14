<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RideRequestController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', fn () => redirect()->route('trips.index'))->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('trips', TripController::class);
    Route::post('/trips/{trip}/ride-requests', [RideRequestController::class, 'store'])->name('trips.ride-requests.store');

    Route::get('/ride-requests', [RideRequestController::class, 'index'])->name('ride-requests.index');
    Route::patch('/ride-requests/{rideRequest}', [RideRequestController::class, 'update'])->name('ride-requests.update');

    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
    Route::post('/contacts/match-phone', [ContactController::class, 'matchPhoneContacts'])->name('contacts.match-phone');
    Route::post('/contacts/request/{user}', [ContactController::class, 'requestUser'])->name('contacts.request-user');
    Route::get('/contacts/facebook-friends', [ContactController::class, 'facebookFriends'])->name('contacts.facebook-friends');
    Route::patch('/contacts/{contact}/accept', [ContactController::class, 'accept'])->name('contacts.accept');
    Route::delete('/contacts/{contact}/reject', [ContactController::class, 'reject'])->name('contacts.reject');
    Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');
});

require __DIR__.'/auth.php';
