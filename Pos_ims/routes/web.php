<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PosController;

// Redirect root to login or dashboard
Route::get('/', function () {
    return auth()->check() ? redirect()->route('pos.index') : redirect()->route('login');
})->name('home');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// POS routes - require authentication
Route::middleware(['auth'])->group(function () {
    // Dashboard & Products
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');

    // Cart operations
    Route::post('/pos/cart/add', [PosController::class, 'addToCart'])->name('pos.cart.add');
    Route::post('/pos/cart/update', [PosController::class, 'updateCart'])->name('pos.cart.update');
    Route::post('/pos/cart/remove', [PosController::class, 'removeFromCart'])->name('pos.cart.remove');
    Route::post('/pos/cart/clear', [PosController::class, 'clearCart'])->name('pos.cart.clear');

    // Checkout & Orders
    Route::get('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
    Route::post('/pos/checkout', [PosController::class, 'processCheckout'])->name('pos.checkout.process');
    Route::get('/pos/receipt/{sale}', [PosController::class, 'receipt'])->name('pos.receipt');

    // Inventory
    Route::get('/inventory', [PosController::class, 'inventory'])->name('inventory.index');
});
