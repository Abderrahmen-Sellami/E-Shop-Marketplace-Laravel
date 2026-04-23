<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Product;
use App\Models\Order;
use App\Models\Review;
use App\Policies\ProductPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ReviewPolicy;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Policies
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Order::class,   OrderPolicy::class);
        Gate::policy(Review::class,  ReviewPolicy::class);
    }
}
