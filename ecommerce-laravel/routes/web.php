<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

// ── Accueil ────────────────────────────────────────────────────
Route::get('/', function () {
    $featuredProducts = \App\Models\Product::active()
        ->with(['category', 'reviews'])
        ->latest()->take(8)->get();
    $categories = \App\Models\Category::withCount('products')->get();
    return view('home', compact('featuredProducts', 'categories'));
})->name('home');

// ── Auth (Breeze) ──────────────────────────────────────────────
require __DIR__.'/auth.php';

// ── Catalogue public ───────────────────────────────────────────
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/search', [CatalogController::class, 'search'])->name('catalog.search');

// ── Produits ───────────────────────────────────────────────────
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('products', ProductController::class)->except(['show']);

    // ── Panier ─────────────────────────────────────────────────
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/',                     [CartController::class, 'index'])->name('index');
        Route::post('/add/{product}',       [CartController::class, 'add'])->name('add');
        Route::patch('/update/{productId}', [CartController::class, 'update'])->name('update');
        Route::delete('/remove/{productId}',[CartController::class, 'remove'])->name('remove');
        Route::delete('/clear',             [CartController::class, 'clear'])->name('clear');
        Route::get('/count',                [CartController::class, 'count'])->name('count');
    });

    // ── Commandes ──────────────────────────────────────────────
    Route::post('/orders',              [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders',               [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}',       [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // ── Avis ───────────────────────────────────────────────────
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}',         [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // ── Wishlist ───────────────────────────────────────────────
    Route::get('/wishlist',                    [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}/toggle',  [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});

// ── Admin ──────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard',                           [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users',                               [AdminController::class, 'users'])->name('users');
    Route::patch('/users/{user}/role',                 [AdminController::class, 'updateRole'])->name('users.role');
    Route::delete('/users/{user}',                     [AdminController::class, 'deleteUser'])->name('users.delete');
    Route::get('/orders',                              [AdminController::class, 'orders'])->name('orders');
    Route::patch('/orders/{order}/status',             [AdminController::class, 'updateOrderStatus'])->name('orders.status');
    Route::get('/products',                            [AdminController::class, 'products'])->name('products');
    Route::patch('/products/{product}/toggle-status',  [AdminController::class, 'toggleProductStatus'])->name('products.toggle');
    Route::get('/stats',                               [AdminController::class, 'stats'])->name('stats');
});
