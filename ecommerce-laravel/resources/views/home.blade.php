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

{{-- Catégories --}}
<h2 class="h4 fw-bold mb-3">Catégories</h2>
<div class="row g-3 mb-5">
    @foreach($categories as $cat)
    <div class="col-6 col-md-4 col-lg-2">
        <a href="{{ route('catalog.index', ['category' => $cat->id]) }}" class="card text-decoration-none text-center p-3 product-card h-100">
            <div class="fs-2 mb-1">
                @switch($cat->slug)
                    @case('electronique') 💻 @break
                    @case('vetements')    👔 @break
                    @case('livres')       📚 @break
                    @case('maison')       🏠 @break
                    @case('sport')        ⚽ @break
                    @default              🛍️
                @endswitch
            </div>
            <div class="small fw-semibold text-dark">{{ $cat->name }}</div>
            <div class="text-muted" style="font-size:.75rem">{{ $cat->products_count }} produits</div>
        </a>
    </div>
    @endforeach
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
@endsection
