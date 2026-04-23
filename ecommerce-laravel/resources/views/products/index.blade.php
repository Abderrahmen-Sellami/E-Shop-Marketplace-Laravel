@extends('layouts.app')
@section('title', 'Mes Produits')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0"><i class="bi bi-box"></i> Mes Produits</h2>
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nouveau produit
    </a>
</div>

@forelse($products as $product)
<div class="card mb-3 p-3">
    <div class="row align-items-center g-3">
        <div class="col-md-1">
            <img src="{{ $product->image_url }}" width="60" height="60"
                 style="object-fit:cover;border-radius:8px" alt="">
        </div>
        <div class="col-md-4">
            <div class="fw-semibold">{{ $product->title }}</div>
            <div class="text-muted small">{{ $product->category->name }}</div>
        </div>
        <div class="col-md-2 fw-bold text-success">{{ number_format($product->price, 2) }} DT</div>
        <div class="col-md-2">
            <span class="badge {{ $product->stock > 0 ? 'bg-success' : 'bg-danger' }}">
                Stock: {{ $product->stock }}
            </span>
        </div>
        <div class="col-md-2">
            <span class="badge bg-{{ ['active'=>'success','inactive'=>'secondary','pending'=>'warning'][$product->status] }}">
                {{ ucfirst($product->status) }}
            </span>
        </div>
        <div class="col-md-1 d-flex gap-1">
            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-pencil"></i>
            </a>
            <form action="{{ route('products.destroy', $product) }}" method="POST"
                  onsubmit="return confirm('Supprimer ?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
            </form>
        </div>
    </div>
</div>
@empty
<div class="text-center py-5 text-muted">
    <i class="bi bi-box fs-1"></i>
    <p class="mt-3">Vous n'avez pas encore de produits.</p>
    <a href="{{ route('products.create') }}" class="btn btn-primary">Créer mon premier produit</a>
</div>
@endforelse

<div class="mt-3">{{ $products->links() }}</div>
@endsection
