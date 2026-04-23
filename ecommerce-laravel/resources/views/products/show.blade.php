@extends('layouts.app')
@section('title', $product->title)

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
        <li class="breadcrumb-item"><a href="{{ route('catalog.index') }}">Catalogue</a></li>
        <li class="breadcrumb-item active">{{ $product->title }}</li>
    </ol>
</nav>

<div class="row g-4">
    {{-- Image --}}
    <div class="col-md-5">
        <img src="{{ $product->image_url }}" class="img-fluid rounded-3 w-100"
             style="max-height:420px;object-fit:cover" alt="{{ $product->title }}">
    </div>

    {{-- Infos --}}
    <div class="col-md-7">
        <span class="badge bg-light text-secondary mb-2">{{ $product->category->name }}</span>
        <h1 class="h2 fw-bold">{{ $product->title }}</h1>

        {{-- Vendeur --}}
        <p class="text-muted small mb-2">
            <i class="bi bi-person-circle"></i> Vendu par <strong>{{ $product->user->name }}</strong>
        </p>

        {{-- Note --}}
        <div class="mb-3" style="color:#f4a261">
            @for($i = 1; $i <= 5; $i++){{ $i <= round($product->average_rating) ? '★' : '☆' }}@endfor
            <span class="text-muted small ms-1">{{ $product->average_rating }}/5 ({{ $product->reviews->count() }} avis)</span>
        </div>

        <div class="display-6 fw-bold text-success mb-3">{{ number_format($product->price, 2) }} DT</div>

        {{-- Stock --}}
        @if($product->stock > 0)
            <span class="badge bg-success mb-3"><i class="bi bi-check-circle"></i> En stock ({{ $product->stock }})</span>
        @else
            <span class="badge bg-danger mb-3"><i class="bi bi-x-circle"></i> Rupture de stock</span>
        @endif

        <p class="text-muted mb-4">{{ $product->description }}</p>

        @auth
        <div class="d-flex gap-2 flex-wrap">
            {{-- Ajouter au panier --}}
            @if($product->stock > 0)
            <form action="{{ route('cart.add', $product) }}" method="POST" class="d-flex gap-2">
                @csrf
                <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}"
                       class="form-control" style="width:80px">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-cart-plus"></i> Ajouter au panier
                </button>
            </form>
            @endif

            {{-- Wishlist --}}
            <button id="wishlist-btn" class="btn {{ $inWishlist ? 'btn-danger' : 'btn-outline-danger' }}"
                    data-product="{{ $product->id }}" data-url="{{ route('wishlist.toggle', $product) }}">
                <i class="bi bi-heart{{ $inWishlist ? '-fill' : '' }}"></i>
            </button>
        </div>
        @else
            <a href="{{ route('login') }}" class="btn btn-primary">Se connecter pour acheter</a>
        @endauth

        @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->id === $product->user_id))
        <div class="mt-3 d-flex gap-2">
            <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-pencil"></i> Modifier
            </a>
            <form action="{{ route('products.destroy', $product) }}" method="POST"
                  onsubmit="return confirm('Supprimer ce produit ?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i> Supprimer</button>
            </form>
        </div>
        @endif
    </div>
</div>

{{-- Produits similaires --}}
@if($relatedProducts->count())
<div class="mt-5">
    <h4 class="fw-bold mb-3">Produits similaires</h4>
    <div class="row g-3">
        @foreach($relatedProducts as $related)
        <div class="col-md-3">@include('catalog.partials.product-card', ['product' => $related])</div>
        @endforeach
    </div>
</div>
@endif

{{-- Avis --}}
<div class="mt-5">
    <h4 class="fw-bold mb-3">Avis clients ({{ $product->reviews->count() }})</h4>

    @auth
    @if(!$alreadyReviewed)
    <div class="card p-4 mb-4">
        <h5 class="mb-3">Laisser un avis</h5>
        <form action="{{ route('reviews.store', $product) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Note</label>
                <select name="rating" class="form-select" required>
                    <option value="">-- Choisir --</option>
                    @for($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}">{{ $i }} ★ &mdash; {{ ['','Très mauvais','Mauvais','Moyen','Bien','Excellent'][$i] }}</option>
                    @endfor
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Commentaire</label>
                <textarea name="comment" class="form-control" rows="3" placeholder="Votre avis..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Publier l'avis</button>
        </form>
    </div>
    @endif
    @endauth

    @forelse($product->reviews as $review)
    <div class="card p-3 mb-3">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <strong>{{ $review->user->name }}</strong>
                <span class="ms-2" style="color:#f4a261">
                    @for($i = 1; $i <= 5; $i++){{ $i <= $review->rating ? '★' : '☆' }}@endfor
                </span>
                <span class="text-muted small ms-2">{{ $review->created_at->diffForHumans() }}</span>
            </div>
            @auth
            @can('delete', $review)
            <form action="{{ route('reviews.destroy', $review) }}" method="POST">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
            </form>
            @endcan
            @endauth
        </div>
        @if($review->comment)
        <p class="mt-2 mb-0 text-muted">{{ $review->comment }}</p>
        @endif
    </div>
    @empty
    <p class="text-muted">Aucun avis pour ce produit.</p>
    @endforelse
</div>
@endsection

@push('scripts')
<script>
// Wishlist AJAX toggle
const wishlistBtn = document.getElementById('wishlist-btn');
if (wishlistBtn) {
    wishlistBtn.addEventListener('click', async function () {
        const res = await fetch(this.dataset.url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        const data = await res.json();
        const icon = this.querySelector('i');
        if (data.inWishlist) {
            this.classList.replace('btn-outline-danger', 'btn-danger');
            icon.className = 'bi bi-heart-fill';
        } else {
            this.classList.replace('btn-danger', 'btn-outline-danger');
            icon.className = 'bi bi-heart';
        }
    });
}
</script>
@endpush
