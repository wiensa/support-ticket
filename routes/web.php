<?php

use Illuminate\Support\Facades\Route;
use Wiensa\SupportTicket\Http\Controllers\TicketController;
use Wiensa\SupportTicket\Http\Controllers\TicketReplyController;
use Wiensa\SupportTicket\Http\Controllers\CategoryController;
use Wiensa\SupportTicket\Http\Controllers\AttachmentController;
use Wiensa\SupportTicket\Http\Controllers\SettingController;

/*
|--------------------------------------------------------------------------
| Support Ticket Routes
|--------------------------------------------------------------------------
|
| Here is where you can register support ticket routes for your application.
|
*/

// Grup rotaları
Route::middleware(config('supportticket.middleware', ['web', 'auth']))
    ->prefix(config('supportticket.route_prefix', 'support'))
    ->name('supportticket.')
    ->group(function () {
        // Talep rotaları
        Route::resource('tickets', TicketController::class);
        Route::post('tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');
        Route::post('tickets/{ticket}/close', [TicketController::class, 'close'])->name('tickets.close');
        Route::post('tickets/{ticket}/reopen', [TicketController::class, 'reopen'])->name('tickets.reopen');
        
        // Yanıt rotaları
        Route::post('replies/{ticket}', [TicketReplyController::class, 'store'])->name('replies.store');
        Route::put('replies/{ticket}/{reply}', [TicketReplyController::class, 'update'])->name('replies.update');
        Route::delete('replies/{ticket}/{reply}', [TicketReplyController::class, 'destroy'])->name('replies.destroy');
        
        // Kategori rotaları (sadece admin)
        Route::middleware(config('supportticket.admin_middleware', ['web', 'auth', 'admin']))
            ->group(function () {
                Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
                Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
                Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
                Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
            });
        
        // Dosya eki rotaları
        Route::post('attachments', [AttachmentController::class, 'store'])->name('attachments.store');
        Route::get('attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');
        Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');
        
        // Ayar rotaları (sadece admin)
        Route::middleware(config('supportticket.admin_middleware', ['web', 'auth', 'admin']))
            ->group(function () {
                Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
                Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
            });
    });
    
// API rotaları
Route::middleware(config('supportticket.api_middleware', ['api', 'auth:api']))
    ->prefix(config('supportticket.api_prefix', 'api/support'))
    ->name('api.supportticket.')
    ->group(function () {
        // Ayar API rotaları
        Route::get('settings/{key}', [SettingController::class, 'getSetting'])->name('settings.get');
        Route::get('settings/group/{group}', [SettingController::class, 'getSettingsByGroup'])->name('settings.group');
    }); 