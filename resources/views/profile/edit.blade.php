@extends('layouts.app')
@section('title', 'Mon Profil')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card p-4">
    <h3 class="fw-bold mb-4"><i class="bi bi-person-circle"></i> Mon Profil</h3>

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf @method('PATCH')

        <div class="mb-3">
            <label class="form-label fw-semibold">Nom</label>
            <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" class="form-control" value="{{ auth()->user()->email }}" disabled>
            <div class="form-text">L'email ne peut pas être modifié.</div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Rôle</label>
            <input type="text" class="form-control" value="{{ ucfirst(auth()->user()->role) }}" disabled>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle"></i> Enregistrer
        </button>
    </form>
</div>
</div>
</div>
@endsection