@extends('frontend.layouts.app')

@section('content')
<div class="container py-5">
    <div class="row g-4 align-items-center">
        <!-- Product Image -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded">
                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top rounded" alt="{{ $product->name }}">
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-md-7">
            <h2 class="fw-bold">{{ $product->name }}</h2>
            <p class="text-muted">Category: <span class="badge bg-secondary">{{ $product->category->name }}</span></p>
            <p class="text-muted">Restaurant: <strong>{{ $product->restaurant->name }}</strong></p>
            
            <h4 class="text-success mt-3">₹ {{ number_format($product->price, 2) }}</h4>
            
            <p class="mt-3">{{ $product->description }}</p>
            
            @if ($product->available)
                <span class="badge bg-success">Available</span>
            @else
                <span class="badge bg-danger">Currently Unavailable</span>
            @endif

            <div class="mt-4">
                @if ($product->available)
                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                    @csrf
                    <div class="d-flex align-items-center gap-3">
                        <input type="number" name="quantity" value="1" min="1" max="10" class="form-control w-25" />
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                            <i class="bi bi-cart-plus me-1"></i> Add to Cart
                        </button>
                    </div>
                </form>
                @else
                    <button class="btn btn-secondary btn-lg mt-3" disabled>Out of Stock</button>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$('.add-to-cart').click(function () {
    const id = $(this).data('id');

    $.post('/cart/add', {
        product_id: id,
        _token: '{{ csrf_token() }}'
    }, function (res) {
        alert(res.success);
    });
});
</script>
@endpush
