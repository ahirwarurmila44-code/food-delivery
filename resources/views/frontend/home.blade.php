@extends('frontend.layouts.app')

@section('content')
<div class="container py-5">
  <div class="text-center mb-5">
    <h2>Welcome, {{ auth()->user()->name }}! 👋</h2>
    <p class="text-muted">Explore restaurants, choose products, and place your order easily.</p>
  </div>

  <div class="row g-4">
    <div class="col-md-6">
      <div class="card p-4 text-center">
        <h4><i class="bi bi-shop-window text-primary"></i> Browse Restaurants</h4>
        <p class="text-muted">Find your favorite places to eat</p>
        <a href="{{ route('restaurants.list') }}" class="btn btn-outline-primary">View Restaurants</a>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card p-4 text-center">
        <h4><i class="bi bi-box-seam text-success"></i> Explore Products</h4>
        <p class="text-muted">Pick dishes and add to cart</p>
        <a href="{{ route('products.list') }}" class="btn btn-outline-success">View Products</a>
      </div>
    </div>
  </div>
</div>
@endsection
