@extends('layouts.app')
@section('title', 'Modifier le produit')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="card p-4">
    <h2 class="fw-bold mb-4"><i class="bi bi-pencil"></i> Modifier le produit</h2>

    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('products.partials.form')
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary px-5">
                <i class="bi bi-check-circle"></i> Enregistrer les modifications
            </button>
            <a href="{{ route('products.show', $product) }}" class="btn btn-outline-secondary">Annuler</a>
        </div>
    </form>
</div>
</div>
</div>
@endsection
