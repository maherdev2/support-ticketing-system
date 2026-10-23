<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('tickets.index');
});

// Authentication Routes
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('quick-login/{user}', [AuthController::class, 'quickLogin'])->middleware('guest')->name('quick-login');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Tickets
    Route::resource('tickets', TicketController::class);
    Route::post('tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');

    // Attachments (secure download)
    Route::get('attachments/{attachment}/download', [TicketController::class, 'downloadAttachment'])
        ->name('attachments.download');

    // Settings - User Management (Admin only)
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/users', [SettingsController::class, 'store'])->name('settings.users.store');
    Route::put('settings/users/{user}', [SettingsController::class, 'update'])->name('settings.users.update');
    Route::delete('settings/users/{user}', [SettingsController::class, 'destroy'])->name('settings.users.destroy');
    Route::patch('settings/users/{user}/toggle-active', [SettingsController::class, 'toggleActive'])->name('settings.users.toggle-active');
});
