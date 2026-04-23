@extends('layouts.app')
@section('title', 'Mes Produits')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0"><i class="bi bi-box"></i> Gestion de mes articles</h2>
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nouveau produit
    </a>
</div>

{{-- Système d'onglets pour naviguer entre Produits et Services --}}
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link active fw-bold" href="{{ route('products.index') }}">
            <i class="bi bi-box-seam"></i> Mes Produits
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-muted" href="{{ route('services.manage') }}">
            <i class="bi bi-tools"></i> Mes Services
        </a>
    </li>
</ul>

@forelse($products as $product)
<div class="card border-0 shadow-sm mb-3 p-3 product-card">
    <div class="row align-items-center g-3">
        <div class="col-md-1 text-center">
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" width="60" height="60"
                     style="object-fit:cover;border-radius:8px" alt="">
            @else
                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:60px;height:60px">
                    <i class="bi bi-image text-muted"></i>
                </div>
            @endif
        </div>
        <div class="col-md-4">
            <div class="fw-bold text-dark">{{ $product->title }}</div>
            <div class="text-muted small">
                <i class="bi bi-tag"></i> {{ $product->category->name ?? 'Sans catégorie' }}
            </div>
        </div>
        <div class="col-md-2 fw-bold text-primary">{{ number_format($product->price, 2) }} DT</div>
        <div class="col-md-2">
            <span class="badge {{ $product->stock > 0 ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }} w-100 py-2">
                Stock: {{ $product->stock }}
            </span>
        </div>
        <div class="col-md-2 text-center">
            @php
                $statusColors = ['active'=>'success', 'inactive'=>'secondary', 'pending'=>'warning'];
                $color = $statusColors[$product->status] ?? 'secondary';
            @endphp
            <span class="badge bg-{{ $color }} px-3">
                {{ ucfirst($product->status) }}
            </span>
        </div>
        <div class="col-md-1 d-flex gap-2 justify-content-end">
            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary" title="Modifier">
                <i class="bi bi-pencil"></i>
            </a>
            <form action="{{ route('products.destroy', $product) }}" method="POST"
                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@empty
<div class="card border-0 shadow-sm py-5 text-center text-muted">
    <div class="card-body">
        <i class="bi bi-box fs-1 d-block mb-3 opacity-25"></i>
        <p class="mb-3">Vous n'avez pas encore de produits en vente.</p>
        <a href="{{ route('products.create') }}" class="btn btn-primary px-4">
            Créer mon premier produit
        </a>
    </div>
</div>
@endforelse

<div class="d-flex justify-content-center mt-4">
    {{ $products->links() }}
</div>

<style>
    .bg-success-soft { background-color: #e8fadf; }
    .bg-danger-soft { background-color: #fceaea; }
    .nav-tabs .nav-link.active { border-bottom: 3px solid var(--primary) !important; color: var(--primary) !important; border-top: none; border-left: none; border-right: none; }
</style>
@endsection