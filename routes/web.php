<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AngebotController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QuickOrderController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

// Redirect guests to login
Route::get('/', function () {
    return redirect()->route('home');
});

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/kategorie/{slug}', [CategoryController::class, 'show'])->name('category.show');
    Route::get('/kategorie/{slug}/products', [CategoryController::class, 'showPartial'])->name('category.products');
    Route::get('/produkt/{id}', [ProductController::class, 'show'])->name('product.show');
    Route::get('/suche', [SearchController::class, 'index'])->name('search');
    Route::get('/api/search/suggest', [SearchController::class, 'suggest'])->name('search.suggest');

    // Cart
    Route::get('/warenkorb', [CartController::class, 'index'])->name('cart.index');

    // Cart API
    Route::post('/api/cart/items', [CartController::class, 'addItem'])->name('cart.add');
    Route::patch('/api/cart/items/{id}', [CartController::class, 'updateItem'])->name('cart.update');
    Route::delete('/api/cart/items/{id}', [CartController::class, 'removeItem'])->name('cart.remove');
    Route::get('/api/cart/summary', [CartController::class, 'summary'])->name('cart.summary');

    // Angebot (quote)
    Route::get('/angebot', [AngebotController::class, 'generate'])->name('angebot.generate');

    // Quick Order
    Route::get('/schnellbestellung', [QuickOrderController::class, 'index'])->name('quick-order.index');
    Route::get('/api/products/lookup', [QuickOrderController::class, 'lookupProduct'])->name('products.lookup');

    // Checkout
    Route::get('/kasse', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/kasse', [CheckoutController::class, 'place'])->name('checkout.place');
    Route::get('/bestellung/bestaetigung/{order_number}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
    Route::get('/bestellung/{order_number}/pdf', [CheckoutController::class, 'pdf'])->name('orders.pdf');

    // Orders
    Route::get('/bestellungen', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/bestellung/{order_number}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/bestellung/{order_number}/nachbestellen', [OrderController::class, 'reorder'])->name('orders.reorder');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Admin / Seller Dashboard
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'cockpit'])->name('cockpit');
    Route::get('/auftraege', [AdminController::class, 'auftraege'])->name('auftraege');
    Route::get('/auftraege/{order_number}', [AdminController::class, 'auftragDetail'])->name('auftrag');
    Route::get('/lager', [AdminController::class, 'lager'])->name('lager');
    Route::get('/produkte', [AdminController::class, 'produkte'])->name('produkte');
    Route::get('/produkte/{id}', [AdminController::class, 'produktDetail'])->name('produkt');
    Route::post('/produkte/{id}', [AdminController::class, 'produktUpdate'])->name('produkt.update');
    Route::get('/kunden', [AdminController::class, 'kunden'])->name('kunden');
    Route::get('/kunden/{id}', [AdminController::class, 'kundeDetail'])->name('kunde');
    Route::get('/statistik', [AdminController::class, 'statistik'])->name('statistik');
});

require __DIR__.'/auth.php';
