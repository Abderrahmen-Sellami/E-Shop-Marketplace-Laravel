@extends('layouts.app')
@section('title', 'Mon Panier')

@section('content')
<h2 class="fw-bold mb-4"><i class="bi bi-cart3"></i> Mon Panier</h2>

@if(empty($products))
<div class="text-center py-5 text-muted">
    <i class="bi bi-cart-x fs-1"></i>
    <p class="mt-3">Votre panier est vide.</p>
    <a href="{{ route('catalog.index') }}" class="btn btn-primary">Explorer le catalogue</a>
</div>
@else
<div class="row g-4">
    <div class="col-lg-8">
        @foreach($products as $item)
        <div class="card p-3 mb-3">
            <div class="row align-items-center g-2">
                <div class="col-md-2">
                    <img src="{{ $item['product']->image_url }}" class="img-fluid rounded" style="height:70px;object-fit:cover">
                </div>
                <div class="col-md-4">
                    <a href="{{ route('products.show', $item['product']) }}" class="fw-semibold text-decoration-none text-dark">
                        {{ $item['product']->title }}
                    </a>
                    <div class="text-muted small">{{ number_format($item['product']->price, 2) }} DT / unité</div>
                </div>
                <div class="col-md-3">
                    <form action="{{ route('cart.update', $item['product']->id) }}" method="POST" class="d-flex gap-2">
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
                    <form action="{{ route('cart.remove', $item['product']->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach

        <form action="{{ route('cart.clear') }}" method="POST">
            @csrf @method('DELETE')
            <button class="btn btn-outline-secondary btn-sm" onclick="return confirm('Vider le panier ?')">
                <i class="bi bi-x-circle"></i> Vider le panier
            </button>
        </form>
    </div>

    {{-- Récapitulatif --}}
    <div class="col-lg-4">
        <div class="card p-4">
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
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-check-circle"></i> Passer la commande
                </button>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
