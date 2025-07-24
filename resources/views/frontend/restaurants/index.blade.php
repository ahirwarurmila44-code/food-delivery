@extends('frontend.layouts.app')
@section('content')
<h2 class="mb-4">Explore Restaurants</h2>
<div class="row">
    @forelse($restaurants as $restaurant)
        <div class="col-md-4 mb-4">
            <div class="card restaurant-card shadow-sm h-100">
                <img src="{{ asset('storage/' . $restaurant->image) }}" class="card-img-top" alt="{{ $restaurant->name }}">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title">{{ $restaurant->name }}</h5>
                        <p class="card-text text-muted">{{ $restaurant->address }}</p>
                    </div>
                    <a href="{{ route('restaurant.products', $restaurant->id) }}" class="btn btn-sm btn-primary mt-3">
                        View Menu
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <p>No restaurants found.</p>
        </div>
    @endforelse
</div>
@endsection