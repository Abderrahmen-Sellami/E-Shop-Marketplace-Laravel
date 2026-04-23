@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0"><i class="bi bi-speedometer2"></i> Dashboard Admin</h2>
    <span class="badge bg-danger px-3 py-2">Administrateur</span>
</div>

{{-- Stats cards --}}
<div class="row g-3 mb-4">
    @foreach([
        ['icon'=>'bi-people','label'=>'Utilisateurs','value'=>$stats['users'],'color'=>'primary'],
        ['icon'=>'bi-shop','label'=>'Vendeurs','value'=>$stats['sellers'],'color'=>'success'],
        ['icon'=>'bi-box','label'=>'Produits','value'=>$stats['products'],'color'=>'info'],
        ['icon'=>'bi-bag','label'=>'Commandes','value'=>$stats['orders'],'color'=>'warning'],
        ['icon'=>'bi-currency-dollar','label'=>'Revenu validé','value'=>number_format($stats['revenue'],2).' DT','color'=>'success'],
        ['icon'=>'bi-hourglass','label'=>'En attente','value'=>$stats['pending'],'color'=>'danger'],
    ] as $s)
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card p-3 text-center h-100">
            <i class="bi {{ $s['icon'] }} fs-2 text-{{ $s['color'] }}"></i>
            <div class="fw-bold fs-4">{{ $s['value'] }}</div>
            <div class="text-muted small">{{ $s['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4">
    {{-- Dernières commandes --}}
    <div class="col-lg-7">
        <div class="card p-4">
            <div class="d-flex justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Dernières commandes</h5>
                <a href="{{ route('admin.orders') }}" class="btn btn-sm btn-outline-primary">Tout voir</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle small">
                    <thead class="table-light">
                        <tr><th>#</th><th>Client</th><th>Total</th><th>Statut</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                        <tr>
                            <td><a href="{{ route('orders.show', $order) }}">#{{ $order->id }}</a></td>
                            <td>{{ $order->user->name }}</td>
                            <td class="fw-semibold">{{ number_format($order->total, 2) }} DT</td>
                            <td>
                                <span class="badge bg-{{ ['pending'=>'warning','validated'=>'success','cancelled'=>'danger'][$order->status] }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $order->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Top produits --}}
    <div class="col-lg-5">
        <div class="card p-4">
            <h5 class="fw-bold mb-3">Top produits</h5>
            @foreach($topProducts as $i => $product)
            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="badge bg-primary rounded-circle" style="width:28px;height:28px;line-height:20px">{{ $i+1 }}</span>
                <div class="flex-grow-1">
                    <div class="small fw-semibold">{{ Str::limit($product->title, 35) }}</div>
                    <div class="text-muted" style="font-size:.75rem">{{ $product->order_items_count }} vendus</div>
                </div>
                <span class="fw-bold text-success">{{ number_format($product->price, 2) }} DT</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Navigation rapide --}}
<div class="row g-3 mt-2">
    @foreach([
        ['route'=>'admin.users','icon'=>'bi-people','label'=>'Gérer utilisateurs','color'=>'primary'],
        ['route'=>'admin.orders','icon'=>'bi-bag','label'=>'Gérer commandes','color'=>'warning'],
        ['route'=>'admin.products','icon'=>'bi-box','label'=>'Gérer produits','color'=>'info'],
    ] as $link)
    <div class="col-md-4">
        <a href="{{ route($link['route']) }}" class="card p-3 text-decoration-none text-center d-block product-card">
            <i class="bi {{ $link['icon'] }} fs-3 text-{{ $link['color'] }}"></i>
            <div class="fw-semibold mt-1">{{ $link['label'] }}</div>
        </a>
    </div>
    @endforeach
</div>
@endsection
