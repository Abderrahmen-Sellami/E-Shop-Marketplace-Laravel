<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --primary: #2d6a4f; --primary-dark: #1b4332; --accent: #52b788; }
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; }
        .navbar-brand { font-weight: 700; color: var(--primary) !important; }
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }
        .card { border: none; box-shadow: 0 2px 10px rgba(0,0,0,.07); border-radius: 12px; }
        .product-card:hover { transform: translateY(-3px); transition: .2s; box-shadow: 0 8px 24px rgba(0,0,0,.12); }
        .badge-admin { background: #dc3545; }
        .badge-seller { background: var(--primary); }
        .badge-buyer { background: #6c757d; }
        #search-results { position: absolute; top: 100%; left: 0; right: 0; z-index: 1000; max-height: 400px; overflow-y: auto; }
        .notification-dot { width: 8px; height: 8px; background: #dc3545; border-radius: 50%; position: absolute; top: 4px; right: 4px; }
        footer { background: var(--primary-dark); color: #ccc; margin-top: 60px; }
    </style>
    @stack('styles')
</head>
<body>

{{-- NAVBAR --}}
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="bi bi-shop"></i> E-Shop
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">

            {{-- Barre de recherche AJAX --}}
            <div class="mx-auto position-relative" style="width:380px">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" id="search-input" class="form-control border-start-0"
                           placeholder="Rechercher un produit..." autocomplete="off">
                </div>
                <div id="search-results" class="card d-none"></div>
            </div>

            <ul class="navbar-nav ms-auto align-items-center gap-1">
                {{-- Dropdown Catalogue --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-grid"></i> Catalogue
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('catalog.index') }}"><i class="bi bi-collection"></i> Tous les produits</a></li>
                        <li><hr class="dropdown-divider"></li>
                        @foreach(\App\Models\Category::withCount('products')->having('products_count', '>', 0)->get() as $cat)
                            <li>
                                <a class="dropdown-item d-flex justify-content-between align-items-center"
                                   href="{{ route('catalog.index', ['category' => $cat->id]) }}">
                                    {{ $cat->name }}
                                    <span class="badge bg-secondary ms-2">{{ $cat->products_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>

                {{-- LIEN SERVICES --}}
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('services.index') }}">
                        <i class="bi bi-tools"></i> Services
                    </a>
                </li>

                @auth
                    {{-- Panier --}}
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="{{ route('cart.index') }}">
                            <i class="bi bi-cart3"></i>
                            <span id="cart-count" class="badge bg-danger rounded-pill" style="font-size:.65rem"></span>
                        </a>
                    </li>

                    {{-- Notifications --}}
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-bell"></i>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="notification-dot"></span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" style="min-width:300px">
                            @forelse(auth()->user()->unreadNotifications->take(5) as $notif)
                                <li>
                                    <a class="dropdown-item small" href="{{ route('orders.show', $notif->data['order_id']) }}">
                                        {{ $notif->data['message'] }}
                                    </a>
                                </li>
                            @empty
                                <li><span class="dropdown-item text-muted small">Aucune notification</span></li>
                            @endforelse
                        </ul>
                    </li>

                    {{-- Profil --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-1" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            {{ auth()->user()->name }}
                            <span class="badge badge-{{ auth()->user()->role }} ms-1" style="font-size:.65rem">
                                {{ ucfirst(auth()->user()->role) }}
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            {{-- LIEN PROFIL CORRIGÉ ICI --}}
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person"></i> Profil</a></li>
                            
                            <li><a class="dropdown-item" href="{{ route('products.index') }}"><i class="bi bi-box"></i> Mes produits</a></li>
                            <li><a class="dropdown-item" href="{{ route('services.manage') }}"><i class="bi bi-gear"></i> Mes services</a></li>
                            
                            <li><a class="dropdown-item" href="{{ route('orders.index') }}"><i class="bi bi-bag"></i> Mes commandes</a></li>
                            <li><a class="dropdown-item" href="{{ route('wishlist.index') }}"><i class="bi bi-heart"></i> Wishlist</a></li>
                            
                            @if(auth()->user()->isAdmin())
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Admin</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i> Déconnexion</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Connexion</a></li>
                    <li class="nav-item"><a class="btn btn-primary btn-sm ms-2" href="{{ route('register') }}">S'inscrire</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

{{-- ALERTS --}}
<div class="container mt-3">
    @foreach(['success','error','warning','info'] as $type)
        @if(session($type))
            <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show" role="alert">
                {{ session($type) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>

{{-- CONTENT --}}
<main class="container my-4">
    @yield('content')
</main>

{{-- FOOTER --}}
<footer class="py-4">
    <div class="container text-center">
        <p class="mb-0">&copy; {{ date('Y') }} E-Shop &mdash; Projet Laravel &mdash; PW2 2025/2026</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Mise à jour compteur panier
async function updateCartCount() {
    try {
        const res = await fetch('{{ route("cart.count") }}');
        const data = await res.json();
        const badge = document.getElementById('cart-count');
        if (badge) badge.textContent = data.count > 0 ? data.count : '';
    } catch(e) {}
}
updateCartCount();

// Recherche AJAX live
const searchInput = document.getElementById('search-input');
const searchResults = document.getElementById('search-results');
let searchTimer;

if (searchInput) {
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        const q = this.value.trim();
        if (q.length < 2) { searchResults.classList.add('d-none'); return; }
        searchTimer = setTimeout(async () => {
            try {
                const res = await fetch(`{{ route('catalog.search') }}?q=${encodeURIComponent(q)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const products = await res.json();
                if (!products.length) { searchResults.classList.add('d-none'); return; }
                searchResults.innerHTML = products.map(p => `
                    <a href="${p.url}" class="d-flex align-items-center gap-2 p-2 text-decoration-none text-dark border-bottom">
                        <img src="${p.image}" alt="${p.title}" width="40" height="40" style="object-fit:cover;border-radius:6px">
                        <div>
                            <div class="small fw-semibold">${p.title}</div>
                            <div class="small text-muted">${p.category} &mdash; ${p.price} DT</div>
                        </div>
                    </a>`).join('');
                searchResults.classList.remove('d-none');
            } catch(e) {}
        }, 300);
    });

    document.addEventListener('click', e => {
        if (!searchInput.contains(e.target)) searchResults.classList.add('d-none');
    });
}
</script>
@stack('scripts')
</body>
</html>