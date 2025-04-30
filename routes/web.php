<?php

use Herd\SupportTicket\Http\Controllers\AdminTicketController;
use Herd\SupportTicket\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Support Ticket Routes
|--------------------------------------------------------------------------
|
| Here is where you can register support ticket routes for your application.
|
*/

// User Routes
Route::group([
    'prefix' => config('supportticket.routes.prefix', 'support'),
    'middleware' => config('supportticket.routes.middleware', ['web', 'auth']),
    'as' => config('supportticket.routes.name', 'supportticket.'),
], function () {
    // Tickets
    Route::get('/', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');
    Route::post('/tickets/{ticket}/close', [TicketController::class, 'close'])->name('tickets.close');
});

// Admin Routes
Route::group([
    'prefix' => config('supportticket.admin_routes.prefix', 'admin/support'),
    'middleware' => config('supportticket.admin_routes.middleware', ['web', 'auth']),
    'as' => config('supportticket.admin_routes.name', 'supportticket.admin.'),
], function () {
    // Tickets
    Route::get('/', [AdminTicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [AdminTicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply', [AdminTicketController::class, 'reply'])->name('tickets.reply');
    Route::post('/tickets/{ticket}/status', [AdminTicketController::class, 'updateStatus'])->name('tickets.status');
}); 