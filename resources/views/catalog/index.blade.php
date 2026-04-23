@extends('layouts.app')
@section('title', 'Catalogue')

@section('content')
<div class="row g-4">

    {{-- Sidebar filtres --}}
    <div class="col-lg-3">
        <div class="card p-3 sticky-top" style="top:80px">
            <h5 class="fw-bold mb-3"><i class="bi bi-funnel"></i> Filtres</h5>
            <form id="filter-form" action="{{ route('catalog.index') }}" method="GET">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Catégorie</label>
                    @foreach($categories as $cat)
                    <div class="form-check">
                        <input class="form-check-input filter-input" type="radio" name="category"
                               value="{{ $cat->id }}" id="cat{{ $cat->id }}"
                               {{ request('category') == $cat->id ? 'checked' : '' }}>
                        <label class="form-check-label" for="cat{{ $cat->id }}">
                            {{ $cat->name }} <span class="text-muted small">({{ $cat->products_count }})</span>
                        </label>
                    </div>
                    @endforeach
                    <div class="form-check mt-1">
                        <input class="form-check-input filter-input" type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }}>
                        <label class="form-check-label">Toutes</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Prix (DT)</label>
                    <div class="d-flex gap-2">
                        <input type="number" name="min_price" class="form-control form-control-sm filter-input"
                               placeholder="Min" value="{{ request('min_price') }}">
                        <input type="number" name="max_price" class="form-control form-control-sm filter-input"
                               placeholder="Max" value="{{ request('max_price') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Trier par</label>
                    <select name="sort" class="form-select form-select-sm filter-input">
                        <option value="latest"     {{ request('sort') == 'latest'     ? 'selected' : '' }}>Plus récents</option>
                        <option value="price_asc"  {{ request('sort') == 'price_asc'  ? 'selected' : '' }}>Prix croissant</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                        <option value="oldest"     {{ request('sort') == 'oldest'     ? 'selected' : '' }}>Plus anciens</option>
                    </select>
                </div>

                <input type="hidden" name="search" value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary w-100 btn-sm">Appliquer</button>
                <a href="{{ route('catalog.index') }}" class="btn btn-outline-secondary w-100 btn-sm mt-2">Réinitialiser</a>
            </form>
        </div>
    </div>

    {{-- Grille produits --}}
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0">Catalogue
                @if(request('search'))
                    <span class="text-muted fs-6">— "{{ request('search') }}"</span>
                @endif
            </h4>
            <span class="text-muted small">{{ $products->total() }} produit(s)</span>
        </div>

        <div id="products-grid">
            @include('catalog.partials.products-grid', ['products' => $products])
        </div>

        <div class="mt-4">{{ $products->withQueryString()->links() }}</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// AJAX filtering
document.querySelectorAll('.filter-input').forEach(input => {
    input.addEventListener('change', () => document.getElementById('filter-form').submit());
});
</script>
@endpush
