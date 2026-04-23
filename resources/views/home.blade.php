@extends('layouts.app')
@section('title', 'Accueil - E-Shop')

@section('content')
{{-- Hero --}}
<div class="rounded-4 p-5 mb-5 text-white" style="background: linear-gradient(135deg, #2d6a4f, #52b788)">
    <div class="row align-items-center">
        <div class="col-md-7">
            <h1 class="display-5 fw-bold">Bienvenue sur E-Shop</h1>
            <p class="lead">Découvrez des milliers de produits de vendeurs de confiance.</p>
            <a href="{{ route('catalog.index') }}" class="btn btn-light btn-lg px-4">
                <i class="bi bi-grid"></i> Explorer le catalogue
            </a>
        </div>
        <div class="col-md-5 text-center fs-1 d-none d-md-block">🛒</div>
    </div>
</div>


{{-- Produits récents --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h4 fw-bold mb-0">Nouveautés</h2>
    <a href="{{ route('catalog.index') }}" class="btn btn-outline-secondary btn-sm">Voir tout</a>
</div>
<div class="row g-3">
    @foreach($featuredProducts as $product)
    <div class="col-md-6 col-lg-3">
        @include('catalog.partials.product-card', ['product' => $product])
    </div>
    @endforeach
</div>
<section class="py-5 bg-light">
    <div class="container px-4 px-lg-5 mt-5">
        <h2 class="fw-bolder mb-4">Nos Services Professionnels</h2>
        <div class="row gx-4 gx-lg-5 row-cols-1 row-cols-md-3 justify-content-center">
            @foreach($services as $service)
            <div class="col mb-5">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3">
                            <i class="bi bi-gear-fill text-primary" style="font-size: 2rem;"></i>
                        </div>
                        <h5 class="fw-bolder">{{ $service->name }}</h5>
                        <p class="text-muted small">{{ $service->description }}</p>
                        <div class="fs-5 fw-bold text-primary">{{ $service->price }} €</div>
                    </div>
                    <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                        <div class="text-center">
                            <a class="btn btn-primary mt-auto" href="#">Réserver / Ajouter</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
