<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\HousingController;

Route::get('/', [HousingController::class, 'index'])->name('index');
Route::post('/', [HousingController::class, 'uploadDocument'])->name('housing.document.upload');

//Route::middleware(['auth', 'verified'])->group(function () {
//    Route::get('dashboard', function () {
//        return Inertia::render('dashboard');
//    })->name('dashboard');
//});

//
//require __DIR__.'/settings.php';
//require __DIR__.'/auth.php';
