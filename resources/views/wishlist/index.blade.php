@extends('layouts.app')
@section('title', 'Ma Wishlist')

@section('content')
<h2 class="fw-bold mb-4"><i class="bi bi-heart"></i> Ma Wishlist</h2>

@if($items->count())
<div class="row g-3">
    @foreach($items as $product)
    <div class="col-md-6 col-lg-3">
        @include('catalog.partials.product-card', ['product' => $product])
    </div>
    @endforeach
</div>
<div class="mt-4">{{ $items->links() }}</div>
@else
<div class="text-center py-5 text-muted">
    <i class="bi bi-heart fs-1"></i>
    <p class="mt-3">Votre wishlist est vide.</p>
    <a href="{{ route('catalog.index') }}" class="btn btn-primary">Explorer le catalogue</a>
</div>
@endif
@endsection
