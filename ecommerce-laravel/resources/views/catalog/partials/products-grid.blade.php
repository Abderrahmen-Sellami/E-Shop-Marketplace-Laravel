@forelse($products as $product)
<div class="col-md-6 col-lg-4 mb-3 d-inline-block w-100" style="max-width: 33.33%">
    @include('catalog.partials.product-card', ['product' => $product])
</div>
@empty
<div class="col-12">
    <div class="text-center py-5 text-muted">
        <i class="bi bi-search fs-1"></i>
        <p class="mt-2">Aucun produit trouvé.</p>
        <a href="{{ route('catalog.index') }}" class="btn btn-outline-primary btn-sm">Réinitialiser</a>
    </div>
</div>
@endforelse
