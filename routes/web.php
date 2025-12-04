<?php

use App\Http\Controllers\GuestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/{weddingSlug}/invite/{guestSlug}', [GuestController::class, 'show'])->name('guests.show');
Route::post('/{weddingSlug}/invite/{guestSlug}', [GuestController::class, 'submitNote'])->name('guests.submitNote');
