@extends('layouts.app')

@section('title', 'Modifier le service : ' . $service->name)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0 fw-bold text-primary"><i class="bi bi-pencil-square"></i> Modifier le Service</h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('services.update', $service) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') {{-- Indispensable pour la mise à jour --}}

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nom du service</label>
                            <input type="text" name="name" id="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $service->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description détaillée</label>
                            <textarea name="description" id="description" rows="5" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      required>{{ old('description', $service->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="price" class="form-label fw-semibold">Prix (DT)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="price" id="price" 
                                           class="form-control @error('price') is-invalid @enderror" 
                                           value="{{ old('price', $service->price) }}" required>
                                    <span class="input-group-text">DT</span>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="is_active" class="form-label fw-semibold">Statut</label>
                                <select name="is_active" id="is_active" class="form-select">
                                    <option value="1" {{ $service->is_active ? 'selected' : '' }}>Actif (Visible)</option>
                                    <option value="0" {{ !$service->is_active ? 'selected' : '' }}>Inactif (Masqué)</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-lg"></i> Enregistrer les modifications
                            </button>
                            <a href="{{ route('services.manage') }}" class="btn btn-outline-secondary px-4">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection