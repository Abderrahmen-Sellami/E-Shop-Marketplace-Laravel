@extends('layouts.app')
@section('title', 'Mes Commandes')

@section('content')
<h2 class="fw-bold mb-4"><i class="bi bi-bag"></i> Mes Commandes</h2>

@forelse($orders as $order)
<div class="card mb-3 p-4 border-0 shadow-sm">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-1">Commande #{{ $order->id }}</h5>
            <span class="text-muted small">{{ $order->created_at->format('d/m/Y à H:i') }}</span>
        </div>
        <div class="text-end">
            <span class="badge fs-6 bg-{{ ['pending'=>'warning','validated'=>'success','cancelled'=>'danger'][$order->status] }}">
                {{ $order->status_label }}
            </span>
            <div class="fw-bold text-success mt-1">{{ number_format($order->total, 2) }} DT</div>
        </div>
    </div>

    <hr class="my-3">

    <div class="row g-2">
        @foreach($order->items as $item)
        <div class="col-md-6">
            <div class="d-flex gap-2 align-items-center">
                
                {{-- Vérification : Produit ou Service ? --}}
                @if($item->product)
                    {{-- Si c'est un produit, on affiche la photo --}}
                    <img src="{{ $item->product->image_url }}" width="50" height="50"
                         style="object-fit:cover;border-radius:8px" alt="">
                    <div>
                        <div class="small fw-semibold">{{ $item->product->title }}</div>
                @elseif($item->service)
                    {{-- Si c'est un service, on affiche une icône (puisqu'il n'y a pas de photo produit) --}}
                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:50px;height:50px;border-radius:8px">
                        <i class="bi bi-tools text-primary"></i>
                    </div>
                    <div>
                        <div class="small fw-semibold">{{ $item->service->name }}</div>
                @else
                    {{-- Sécurité si l'article n'existe plus --}}
                    <div class="bg-light rounded" style="width:50px;height:50px"></div>
                    <div>
                        <div class="small fw-semibold text-muted">Article non disponible</div>
                @endif
                
                        <div class="text-muted" style="font-size:.78rem">
                            {{ $item->quantity }} × {{ number_format($item->unit_price, 2) }} DT
                            = <strong>{{ number_format($item->subtotal, 2) }} DT</strong>
                        </div>
                    </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="d-flex gap-2 mt-3 flex-wrap">
        <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
            <i class="bi bi-eye"></i> Détails
        </a>
        @if($order->status === 'pending')
        <form action="{{ route('orders.cancel', $order) }}" method="POST"
              onsubmit="return confirm('Annuler cette commande ?')">
            @csrf @method('PATCH')
            <button class="btn btn-outline-danger btn-sm rounded-pill px-3"><i class="bi bi-x-circle"></i> Annuler</button>
        </form>
        @endif
    </div>
</div>
@empty
<div class="text-center py-5 text-muted">
    <i class="bi bi-bag-x fs-1"></i>
    <p class="mt-3">Vous n'avez pas encore de commandes.</p>
    <a href="{{ route('catalog.index') }}" class="btn btn-primary rounded-pill">Commencer vos achats</a>
</div>
@endforelse

<div class="d-flex justify-content-center mt-3">
    {{ $orders->links() }}
</div>
@endsection