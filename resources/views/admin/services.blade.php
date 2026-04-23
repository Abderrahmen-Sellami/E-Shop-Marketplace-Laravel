@extends('layouts.app')

@section('title', 'Gestion des Services - Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0"><i class="bi bi-gear-wide-connected text-secondary"></i> Gestion des Services</h2>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Retour au Dashboard
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <form action="{{ route('admin.services') }}" method="GET" class="row g-2">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0" 
                           placeholder="Rechercher un service..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filtrer</button>
            </div>
        </form>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Service</th>
                    <th>Prestataire</th>
                    <th>Prix</th>
                    <th>Date d'ajout</th>
                    <th>Statut</th>
                    <th class="text-end pe-4">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold text-dark">{{ $service->name }}</div>
                        <small class="text-muted">{{ Str::limit($service->description, 50) }}</small>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            <i class="bi bi-person"></i> {{ $service->user->name }}
                        </span>
                    </td>
                    <td class="fw-bold text-primary">
                        {{ number_format($service->price, 2) }} DT
                    </td>
                    <td class="text-muted small">
                        {{ $service->created_at->format('d/m/Y') }}
                    </td>
                    <td>
                        @if($service->is_active)
                            <span class="badge bg-success-soft text-success">
                                <i class="bi bi-check-circle"></i> Actif
                            </span>
                        @else
                            <span class="badge bg-danger-soft text-danger">
                                <i class="bi bi-x-circle"></i> Inactif
                            </span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <form action="{{ route('admin.services.toggle', $service) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $service->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} rounded-pill px-3">
                                {{ $service->is_active ? 'Désactiver' : 'Activer' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-search fs-1 d-block mb-2"></i>
                        Aucun service trouvé.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($services->hasPages())
    <div class="card-footer bg-white">
        {{ $services->withQueryString()->links() }}
    </div>
    @endif
</div>

<style>
    /* Styles pour des badges plus doux */
    .bg-success-soft { background-color: #e8fadf; color: #3b7d23; }
    .bg-danger-soft { background-color: #fceaea; color: #b02a37; }
</style>
@endsection