@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Nos Services</h1>
    <div class="row">
        @forelse($services as $service)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ $service->name }}</h5>
                        <p class="card-text">{{ $service->description }}</p>
                        <h4 class="text-primary">{{ $service->price }} DT</h4>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 text-center">
                        <form action="{{ route('cart.add_service', $service) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">Réserver</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p>Aucun service disponible pour le moment.</p>
        @endforelse
    </div>
</div>
@endsection