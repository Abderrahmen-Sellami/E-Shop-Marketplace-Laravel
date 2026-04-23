@extends('layouts.app')
@section('title', 'Nouveau Produit')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="card p-4">
    <h2 class="fw-bold mb-4"><i class="bi bi-plus-circle"></i> Nouveau produit</h2>

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('products.partials.form')
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary px-5">
                <i class="bi bi-check-circle"></i> Publier le produit
            </button>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Annuler</a>
        </div>
    </form>
</div>
</div>
</div>
@endsection
