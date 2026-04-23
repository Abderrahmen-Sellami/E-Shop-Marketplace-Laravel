<?php

use App\Http\Controllers\ServiceController;
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
    
    $services = \App\Models\Service::where('is_active', true)->latest()->take(3)->get(); 
    
    return view('home', compact('featuredProducts', 'categories', 'services'));
})->name('home');

// ── Auth (Breeze) ──────────────────────────────────────────────
require __DIR__.'/auth.php';

// ── Catalogue & Services Publics ────────────────────────────────
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/search', [CatalogController::class, 'search'])->name('catalog.search');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');

// ── Routes Authentifiées (Buyer & Seller) ──────────────────────
Route::middleware(['auth'])->group(function () {

    // Profil
    Route::get('/profile', fn() => view('profile.edit'))->name('profile.edit');
    Route::patch('/profile', [ProductController::class, 'updateProfile'])->name('profile.update'); // Assurez-vous du nom de la méthode

    // Produits Gestion (CRUD)
    // Placé ici pour que /products/create soit prioritaire sur /products/{product}
    Route::resource('products', ProductController::class)->except(['show']);

    // Services Gestion
    Route::get('/services/manage', [ServiceController::class, 'manage'])->name('services.manage');
    Route::resource('services', ServiceController::class)->except(['index']);

    // Panier
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/',                      [CartController::class, 'index'])->name('index');
        Route::post('/add/{product}',        [CartController::class, 'add'])->name('add');
        Route::post('/add-service/{service}', [CartController::class, 'addService'])->name('add_service');
        Route::patch('/update/{productId}',  [CartController::class, 'update'])->name('update');
        Route::delete('/remove/{productId}', [CartController::class, 'remove'])->name('remove');
        Route::delete('/clear',              [CartController::class, 'clear'])->name('clear');
        Route::get('/count',                 [CartController::class, 'count'])->name('count');
    });

    // Commandes Clients
    Route::get('/orders',                    [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders',                   [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}',            [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/cancel',   [OrderController::class, 'cancel'])->name('orders.cancel');

    // Avis & Wishlist
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}',         [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::get('/wishlist',                   [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});


Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// ── Admin (Accès réservé) ──────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard',                          [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users',                              [AdminController::class, 'users'])->name('users');
    Route::patch('/users/{user}/role',                [AdminController::class, 'updateRole'])->name('users.role');
    Route::delete('/users/{user}',                    [AdminController::class, 'deleteUser'])->name('users.delete');
    
    // Commandes Admin
    Route::get('/orders',                             [AdminController::class, 'orders'])->name('orders');
    Route::patch('/orders/{order}/status',            [AdminController::class, 'updateOrderStatus'])->name('orders.status');
    
    // Produits Admin
    Route::get('/products',                           [AdminController::class, 'products'])->name('products');
    Route::patch('/products/{product}/toggle-status', [AdminController::class, 'toggleProductStatus'])->name('products.toggle');
    
    // Services Admin
    Route::get('/services',                           [AdminController::class, 'services'])->name('services');
    Route::patch('/services/{service}/toggle',        [AdminController::class, 'toggleServiceStatus'])->name('services.toggle');
    
    Route::get('/stats',                              [AdminController::class, 'stats'])->name('stats');
});