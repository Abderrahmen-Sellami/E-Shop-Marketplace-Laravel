@extends('layouts.app')
@section('title', 'Gestion Produits')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0"><i class="bi bi-box"></i> Produits</h2>
</div>

<div class="card p-3 mb-4">
    <form action="{{ route('admin.products') }}" method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <select name="status" class="form-select">
                <option value="">Tous les statuts</option>
                <option value="active"   {{ request('status')=='active'   ? 'selected':'' }}>Actif</option>
                <option value="inactive" {{ request('status')=='inactive' ? 'selected':'' }}>Inactif</option>
                <option value="pending"  {{ request('status')=='pending'  ? 'selected':'' }}>En attente</option>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-primary w-100">Filtrer</button></div>
        <div class="col-md-2"><a href="{{ route('admin.products') }}" class="btn btn-outline-secondary w-100">Reset</a></div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Produit</th><th>Vendeur</th><th>Catégorie</th><th>Prix</th><th>Stock</th><th>Statut</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $product->image_url }}" width="40" height="40"
                                 style="object-fit:cover;border-radius:6px" alt="">
                            <div>
                                <div class="small fw-semibold">{{ Str::limit($product->title, 40) }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="small">{{ $product->user->name }}</td>
                    <td class="small text-muted">{{ $product->category->name }}</td>
                    <td class="fw-semibold text-success">{{ number_format($product->price, 2) }} DT</td>
                    <td>
                        <span class="badge {{ $product->stock > 0 ? 'bg-success' : 'bg-danger' }}">
                            {{ $product->stock }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-{{ ['active'=>'success','inactive'=>'secondary','pending'=>'warning'][$product->status] }}">
                            {{ ucfirst($product->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            <form action="{{ route('admin.products.toggle', $product) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-outline-{{ $product->status === 'active' ? 'warning' : 'success' }}"
                                        title="{{ $product->status === 'active' ? 'Désactiver' : 'Activer' }}">
                                    <i class="bi bi-{{ $product->status === 'active' ? 'pause' : 'play' }}-circle"></i>
                                </button>
                            </form>
                            <form action="{{ route('products.destroy', $product) }}" method="POST"
                                  onsubmit="return confirm('Supprimer ce produit ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $products->withQueryString()->links() }}</div>
</div>
@endsection
