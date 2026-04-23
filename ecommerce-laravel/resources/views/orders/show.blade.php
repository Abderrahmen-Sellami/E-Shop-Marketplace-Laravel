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
        <div class="card p-4 mb-4">
            <h4 class="fw-bold mb-4">Articles commandés</h4>
            @foreach($order->items as $item)
            <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                <img src="{{ $item->product->image_url }}" width="70" height="70"
                     style="object-fit:cover;border-radius:10px" alt="">
                <div class="flex-grow-1">
                    <div class="fw-semibold">{{ $item->product->title }}</div>
                    <div class="text-muted small">{{ $item->product->category->name }}</div>
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
        <div class="card p-4 mb-3">
            <h5 class="fw-bold mb-3">Informations</h5>
            <dl class="row small mb-0">
                <dt class="col-5">Commande</dt>
                <dd class="col-7">#{{ $order->id }}</dd>
                <dt class="col-5">Date</dt>
                <dd class="col-7">{{ $order->created_at->format('d/m/Y H:i') }}</dd>
                <dt class="col-5">Statut</dt>
                <dd class="col-7">
                    <span class="badge bg-{{ ['pending'=>'warning','validated'=>'success','cancelled'=>'danger'][$order->status] }}">
                        {{ $order->status_label }}
                    </span>
                </dd>
                <dt class="col-5">Paiement</dt>
                <dd class="col-7">{{ ucfirst($order->payment_method) }}</dd>
                <dt class="col-5">Livraison</dt>
                <dd class="col-7">{{ $order->shipping_address }}</dd>
            </dl>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
            @if($order->isPending())
            <form action="{{ route('orders.cancel', $order) }}" method="POST"
                  onsubmit="return confirm('Annuler ?')">
                @csrf @method('PATCH')
                <button class="btn btn-outline-danger btn-sm"><i class="bi bi-x-circle"></i> Annuler</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
