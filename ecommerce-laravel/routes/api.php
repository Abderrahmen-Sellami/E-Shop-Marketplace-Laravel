<?php

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ── Auth utilisateur connecté ──────────────────────────────────
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ── Produits ───────────────────────────────────────────────────
Route::apiResource('products', ProductController::class);

// ── Commandes ──────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders',                        [OrderController::class, 'index']);
    Route::get('/orders/{order}',                [OrderController::class, 'show']);
    Route::patch('/orders/{order}/status',       [OrderController::class, 'updateStatus']);
});
