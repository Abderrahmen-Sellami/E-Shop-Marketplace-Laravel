@extends('layouts.app')
@section('title', 'Commande #' . $order->id)

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
        <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Commandes</a></li>
        <li class="breadcrumb-item active">Commande #{{ $order->id }}</li>
    </ol>
</nav>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-4 mb-4 border-0 shadow-sm">
            <h4 class="fw-bold mb-4">Articles commandés</h4>
            @foreach($order->items as $item)
            <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                
                {{-- GESTION PHOTO PRODUIT OU ICÔNE SERVICE --}}
                @if($item->product)
                    {{-- Si c'est un produit, on affiche la photo --}}
                    <img src="{{ $item->product->image_url }}" width="70" height="70"
                         style="object-fit:cover;border-radius:10px" alt="">
                    <div class="flex-grow-1">
                        <div class="fw-semibold text-dark">{{ $item->product->title }}</div>
                        <div class="text-muted small">{{ $item->product->category->name ?? 'Produit' }}</div>
                @elseif($item->service)
                    {{-- Si c'est un service, on affiche une icône --}}
                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:70px;height:70px;border-radius:10px">
                        <i class="bi bi-gear-wide-connected text-primary fs-2"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold text-dark">{{ $item->service->name }}</div>
                        <div class="text-muted small text-uppercase">Service</div>
                @else
                    {{-- Sécurité --}}
                    <div class="bg-light rounded" style="width:70px;height:70px;border-radius:10px"></div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold text-muted">Article non disponible</div>
                        <div class="text-muted small">-</div>
                @endif
                        <div class="text-muted small mt-1">
                            {{ $item->quantity }} × {{ number_format($item->unit_price, 2) }} DT
                        </div>
                    </div>
                <div class="fw-bold text-success">{{ number_format($item->subtotal, 2) }} DT</div>
            </div>
            @endforeach

            <div class="d-flex justify-content-between fw-bold fs-5 mt-2">
                <span>Total</span>
                <span class="text-success">{{ number_format($order->total, 2) }} DT</span>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card p-4 mb-3 border-0 shadow-sm">
            <h5 class="fw-bold mb-3">Informations</h5>
            <dl class="row small mb-0">
                <dt class="col-5 text-muted">Commande</dt>
                <dd class="col-7 fw-bold">#{{ $order->id }}</dd>
                
                <dt class="col-5 text-muted">Date</dt>
                <dd class="col-7">{{ $order->created_at->format('d/m/Y H:i') }}</dd>
                
                <dt class="col-5 text-muted">Statut</dt>
                <dd class="col-7">
                    <span class="badge bg-{{ ['pending'=>'warning','validated'=>'success','cancelled'=>'danger'][$order->status] }}">
                        {{ $order->status_label ?? ucfirst($order->status) }}
                    </span>
                </dd>
                
                <dt class="col-5 text-muted">Paiement</dt>
                <dd class="col-7 text-uppercase">{{ $order->payment_method }}</dd>
                
                <dt class="col-5 text-muted">Livraison</dt>
                <dd class="col-7">{{ $order->shipping_address }}</dd>
            </dl>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
            
            @if($order->status === 'pending')
            <form action="{{ route('orders.cancel', $order) }}" method="POST"
                  onsubmit="return confirm('Voulez-vous vraiment annuler cette commande ?')">
                @csrf @method('PATCH')
                <button class="btn btn-outline-danger btn-sm rounded-pill px-3">
                    <i class="bi bi-x-circle"></i> Annuler
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection