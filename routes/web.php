<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PricingController;
use App\Models\Page;

// Landing Page
Route::get('/', function () {
    return view('landing');
});

// Pricing Page
Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');

// Payment Routes
Route::post('/payment/initiate', [PaymentController::class, 'initiatePayment'])->name('payment.initiate');
Route::get('/payment/callback', [PaymentController::class, 'handleCallback'])->name('payment.callback');
Route::post('/payment/webhook', [PaymentController::class, 'handleWebhook'])->name('payment.webhook');
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/failed', [PaymentController::class, 'failed'])->name('payment.failed');

// Pages Routes
Route::get('/{slug}', function (string $slug) {
    $page = Page::where('slug', $slug)
        ->where('is_published', true)
        ->firstOrFail();

    return view('page', compact('page'));
})->where('slug', '[a-zA-Z0-9\-]+');
