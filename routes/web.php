<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::resource('contacts', ContactController::class);
    Route::resource('companies', CompanyController::class);

    Route::post('exports/contacts', [ExportController::class, 'contacts'])->name('exports.contacts');
    Route::get('exports/{export}/download', [ExportController::class, 'download'])->name('exports.download');
    Route::get('exports/{export}/status', [ExportController::class, 'status'])->name('exports.status');

    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class)->except('show');
    });
});

require __DIR__.'/settings.php';
