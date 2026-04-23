@extends('layouts.app')
@section('title', 'Gestion Commandes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0"><i class="bi bi-bag"></i> Commandes</h2>
</div>

<div class="card p-3 mb-4">
    <form action="{{ route('admin.orders') }}" method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <select name="status" class="form-select">
                <option value="">Tous les statuts</option>
                <option value="pending"   {{ request('status')=='pending'   ? 'selected':'' }}>En attente</option>
                <option value="validated" {{ request('status')=='validated' ? 'selected':'' }}>Validée</option>
                <option value="cancelled" {{ request('status')=='cancelled' ? 'selected':'' }}>Annulée</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filtrer</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.orders') }}" class="btn btn-outline-secondary w-100">Reset</a>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Client</th><th>Total</th><th>Statut</th><th>Paiement</th><th>Date</th><th>Action</th></tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td><a href="{{ route('orders.show', $order) }}" class="fw-semibold">#{{ $order->id }}</a></td>
                    <td>{{ $order->user->name }}</td>
                    <td class="fw-bold text-success">{{ number_format($order->total, 2) }} DT</td>
                    <td>
                        <span class="badge bg-{{ ['pending'=>'warning','validated'=>'success','cancelled'=>'danger'][$order->status] }}">
                            {{ $order->status_label }}
                        </span>
                    </td>
                    <td class="text-muted small">{{ ucfirst($order->payment_method) }}</td>
                    <td class="text-muted small">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="d-flex gap-1">
                            @csrf @method('PATCH')
                            <select name="status" class="form-select form-select-sm" style="width:120px">
                                <option value="pending"   {{ $order->status=='pending'   ? 'selected':'' }}>En attente</option>
                                <option value="validated" {{ $order->status=='validated' ? 'selected':'' }}>Validée</option>
                                <option value="cancelled" {{ $order->status=='cancelled' ? 'selected':'' }}>Annulée</option>
                            </select>
                            <button class="btn btn-sm btn-outline-primary"><i class="bi bi-check"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $orders->withQueryString()->links() }}</div>
</div>
@endsection
