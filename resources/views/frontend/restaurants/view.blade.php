@extends('frontend.layouts.app')

@section('content')
<h2 class="mb-4">{{ $restaurant->name }} - Menu</h2>

<div class="row">
    @forelse($restaurant->products as $product)
    <div class="col-md-3 mb-4">
        <div class="card product-card shadow-sm h-100">
    <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 150px; object-fit: cover;">
    <div class="card-body">
        <h6 class="card-title">{{ $product->name }}</h6>
        <p class="text-success fw-bold mb-2">₹{{ number_format($product->price, 2) }}</p>

        <!-- Merged Controls -->
        <div class="d-flex align-items-center justify-content-between gap-1">
            <!-- <button class="btn btn-sm btn-outline-secondary btn-decrement" data-id="{{ $product->id }}">−</button>

            <input type="text" class="form-control text-center quantity-input" data-id="{{ $product->id }}" value="1" readonly style="width: 50px;">

            <button class="btn btn-sm btn-outline-secondary btn-increment" data-id="{{ $product->id }}">+</button>

            <button class="btn btn-sm btn-outline-primary add-to-cart-btn flex-grow-1" data-id="{{ $product->id }}">
                Add to Cart
            </button> -->
        </div>
         <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-primary btn-sm">View</a>
    </div>
</div>


    </div>
@empty
    <div class="col-12">
        <p class="text-muted">No menu items available for this restaurant.</p>
    </div>
@endforelse

</div>
@endsection
@push('scripts')
<script>

</script>
@endpush