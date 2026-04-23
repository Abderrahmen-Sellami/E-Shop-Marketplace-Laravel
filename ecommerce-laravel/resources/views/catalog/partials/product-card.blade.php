<div class="card product-card h-100">
    <a href="{{ route('products.show', $product) }}">
        <img src="{{ $product->image_url }}" class="card-img-top"
             style="height:180px;object-fit:cover;border-radius:12px 12px 0 0" alt="{{ $product->title }}">
    </a>
    <div class="card-body d-flex flex-column">
        <span class="badge bg-light text-secondary mb-1" style="font-size:.7rem">{{ $product->category->name ?? '' }}</span>
        <h6 class="card-title mb-1">
            <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark">
                {{ Str::limit($product->title, 50) }}
            </a>
        </h6>

        {{-- Stars --}}
        <div class="mb-1" style="font-size:.8rem; color:#f4a261">
            @for($i = 1; $i <= 5; $i++)
                {{ $i <= round($product->average_rating) ? '★' : '☆' }}
            @endfor
            <span class="text-muted">({{ $product->reviews->count() }})</span>
        </div>

        <div class="mt-auto d-flex justify-content-between align-items-center">
            <span class="fw-bold text-success fs-5">{{ number_format($product->price, 2) }} DT</span>
            @auth
            <form action="{{ route('cart.add', $product) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-cart-plus"></i>
                </button>
            </form>
            @else
            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-cart-plus"></i>
            </a>
            @endauth
        </div>
    </div>
</div>
