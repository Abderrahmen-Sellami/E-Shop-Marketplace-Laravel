<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Product;
use App\Models\Order;
use App\Models\Review;
use App\Models\Service; // Ajouté
use App\Policies\ProductPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ReviewPolicy;
use App\Policies\ServicePolicy; // Ajouté

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Utiliser Bootstrap 5 pour la pagination
        Paginator::useBootstrapFive();

        // Enregistrement des Policies (Sécurité)
        Gate::policy(Product::class, ProductPolicy::class);
        //Gate::policy(Order::class,   OrderPolicy::class);
        Gate::policy(Review::class,  ReviewPolicy::class);
        Gate::policy(Service::class, ServicePolicy::class); 
    }
}