@extends('layouts.app')
@section('title', 'Mon Panier')

@section('content')
<h2 class="fw-bold mb-4"><i class="bi bi-cart3"></i> Mon Panier</h2>

@if(empty($items)) {{-- Changé $products par $items --}}
<div class="text-center py-5 text-muted">
    <i class="bi bi-cart-x fs-1"></i>
    <p class="mt-3">Votre panier est vide.</p>
    <a href="{{ route('catalog.index') }}" class="btn btn-primary">Explorer le catalogue</a>
</div>
@else
<div class="row g-4">
    <div class="col-lg-8">
        @foreach($items as $item) {{-- Changé $products par $items --}}
        <div class="card p-3 mb-3 border-0 shadow-sm">
            <div class="row align-items-center g-2">
                <div class="col-md-2 text-center">
                    @if($item['type'] == 'product')
                        <img src="{{ $item['product']->image_url }}" class="img-fluid rounded" style="height:70px;width:70px;object-fit:cover">
                    @else
                        {{-- Icône pour les services --}}
                        <div class="bg-light rounded d-flex align-items-center justify-content-center mx-auto" style="height:70px;width:70px">
                            <i class="bi bi-gear-wide-connected text-primary fs-2"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-4">
                    @if($item['type'] == 'product')
                        <a href="{{ route('products.show', $item['product']) }}" class="fw-semibold text-decoration-none text-dark">
                            {{ $item['name'] }}
                        </a>
                    @else
                        <span class="fw-semibold text-dark">{{ $item['name'] }}</span>
                    @endif
                    <div class="text-muted small">
                        <span class="badge {{ $item['type'] == 'product' ? 'bg-info' : 'bg-secondary' }} mb-1">
                            {{ $item['type'] == 'product' ? 'Produit' : 'Service' }}
                        </span>
                        <div>{{ number_format($item['price'], 2) }} DT / unité</div>
                    </div>
                </div>
                <div class="col-md-3">
                    {{-- L'ID est soit l'ID du produit, soit ser_ID du service --}}
                    <form action="{{ route('cart.update', $item['id']) }}" method="POST" class="d-flex gap-2">
                        @csrf @method('PATCH')
                        <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="99"
                               class="form-control form-control-sm" style="width:70px">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-repeat"></i>
                        </button>
                    </form>
                </div>
                <div class="col-md-2 fw-bold text-success text-end">
                    {{ number_format($item['subtotal'], 2) }} DT
                </div>
                <div class="col-md-1 text-end">
                    <form action="{{ route('cart.remove', $item['id']) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger border-0"><i class="bi bi-trash fs-5"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach

        <div class="d-flex justify-content-between align-items-center mt-3">
            <a href="{{ route('catalog.index') }}" class="btn btn-link text-decoration-none text-muted p-0">
                <i class="bi bi-arrow-left"></i> Continuer mes achats
            </a>
            <form action="{{ route('cart.clear') }}" method="POST">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Vider le panier ?')">
                    <i class="bi bi-trash"></i> Vider le panier
                </button>
            </form>
        </div>
    </div>

    {{-- Récapitulatif --}}
    <div class="col-lg-4">
        <div class="card p-4 border-0 shadow-sm">
            <h5 class="fw-bold mb-3">Récapitulatif</h5>
            <div class="d-flex justify-content-between mb-2">
                <span>Sous-total</span>
                <strong>{{ number_format($total, 2) }} DT</strong>
            </div>
            <div class="d-flex justify-content-between mb-3 text-muted small">
                <span>Livraison</span><span>Gratuite</span>
            </div>
            <hr>
            <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
                <span>Total</span>
                <span class="text-success">{{ number_format($total, 2) }} DT</span>
            </div>

            {{-- Formulaire de commande --}}
            <form action="{{ route('orders.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Adresse de livraison</label>
                    <textarea name="shipping_address" class="form-control" rows="2" required
                              placeholder="N° rue, ville, code postal..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Mode de paiement</label>
                    <select name="payment_method" class="form-select">
                        <option value="cash">Paiement à la livraison</option>
                        <option value="card">Carte bancaire</option>
                        <option value="paypal">PayPal</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-pill">
                    <i class="bi bi-check-circle"></i> Passer la commande
                </button>
            </form>
        </div>
    </div>
</div>
@endif
@endsection