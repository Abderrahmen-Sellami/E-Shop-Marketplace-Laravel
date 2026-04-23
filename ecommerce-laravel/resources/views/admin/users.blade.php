@extends('layouts.app')
@section('title', 'Gestion Utilisateurs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0"><i class="bi bi-people"></i> Utilisateurs</h2>
</div>

{{-- Filtres --}}
<div class="card p-3 mb-4">
    <form action="{{ route('admin.users') }}" method="GET" class="row g-2 align-items-end">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Nom ou email..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="role" class="form-select">
                <option value="">Tous les rôles</option>
                <option value="admin"  {{ request('role')=='admin'  ? 'selected':'' }}>Admin</option>
                <option value="seller" {{ request('role')=='seller' ? 'selected':'' }}>Vendeur</option>
                <option value="buyer"  {{ request('role')=='buyer'  ? 'selected':'' }}>Acheteur</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filtrer</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary w-100">Reset</a>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th><th>Nom</th><th>Email</th><th>Rôle</th>
                    <th>Produits</th><th>Commandes</th><th>Inscrit le</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td class="text-muted small">{{ $user->id }}</td>
                    <td class="fw-semibold">{{ $user->name }}</td>
                    <td class="small">{{ $user->email }}
                        @if($user->email_verified_at)
                            <i class="bi bi-patch-check-fill text-success ms-1" title="Email vérifié"></i>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
                    </td>
                    <td class="text-center">{{ $user->products()->count() }}</td>
                    <td class="text-center">{{ $user->orders()->count() }}</td>
                    <td class="text-muted small">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            {{-- Changer le rôle --}}
                            <form action="{{ route('admin.users.role', $user) }}" method="POST" class="d-flex gap-1">
                                @csrf @method('PATCH')
                                <select name="role" class="form-select form-select-sm" style="width:110px">
                                    <option value="buyer"  {{ $user->role=='buyer'  ? 'selected':'' }}>Acheteur</option>
                                    <option value="seller" {{ $user->role=='seller' ? 'selected':'' }}>Vendeur</option>
                                    <option value="admin"  {{ $user->role=='admin'  ? 'selected':'' }}>Admin</option>
                                </select>
                                <button class="btn btn-sm btn-outline-primary" title="Mettre à jour">
                                    <i class="bi bi-check"></i>
                                </button>
                            </form>
                            {{-- Supprimer --}}
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.delete', $user) }}" method="POST"
                                  onsubmit="return confirm('Supprimer {{ $user->name }} ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $users->withQueryString()->links() }}</div>
</div>
@endsection
