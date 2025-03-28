<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('index');
})->name('index');

Route::post('/', [\App\Http\Controllers\HousingController::class, 'uploadDocument'])->name('housing.document.upload');

//Route::middleware(['auth', 'verified'])->group(function () {
//    Route::get('dashboard', function () {
//        return Inertia::render('dashboard');
//    })->name('dashboard');
//});

//
//require __DIR__.'/settings.php';
//require __DIR__.'/auth.php';
