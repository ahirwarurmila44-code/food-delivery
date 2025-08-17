@extends('frontend.layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container mt-4">
    <h4 class="mb-3">🛒 Your Cart</h4>

    <div id="cart-items-container">
        @forelse($cartItems as $item)
            <div class="row align-items-center border-bottom py-2 cart-item" data-id="{{ $item->id }}">
                <div class="col-md-2">
                    <img src="{{ asset('storage/' . $item->product->image) }}" class="img-fluid rounded" alt="{{ $item->product->name }}">
                </div>

                <div class="col-md-4">
                    <h6 class="mb-1">{{ $item->product->name }}</h6>
                    <p class="text-muted mb-0">₹{{ number_format($item->product->price, 2) }}</p>
                </div>

                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <button class="btn btn-outline-secondary btn-decrement" data-id="{{ $item->id }}">−</button>
                        <input type="text" class="form-control text-center quantity-input" data-id="{{ $item->id }}" value="{{ $item->quantity }}" readonly>
                        <button class="btn btn-outline-secondary btn-increment" data-id="{{ $item->id }}">+</button>
                    </div>
                </div>
                @php
                   $total = $item->product->price * $item->product->quantity;
                @endphp
                <div class="col-md-2 text-end">
                    <strong>₹{{ number_format($item->product->price * $item->quantity, 2) }}</strong>
                </div>

                <div class="col-md-1 text-end">
                    <button class="btn btn-sm btn-danger btn-delete-item" data-id="{{ $item->id }}">
                        <i class="fas fa-trash">Remove</i>
                    </button>
                </div>
            </div>
        @empty
            <p class="text-muted">Your cart is empty.</p>
        @endforelse
    </div>

    @if(count($cartItems))
        <div class="mt-4 d-flex justify-content-between align-items-center">
            <h5>Total: ₹<span id="cart-total">{{ number_format($totalAmount, 2) }}</span></h5>
            <a href="{{ route('cart.checkout') }}" class="btn btn-primary btn-lg">Proceed to Checkout</a>
        </div>
    @endif
</div>

@endsection
@push('scripts')
<script>

     $(document).on('click', '.btn-increment', function (e) {
        e.preventDefault();
        let $qtyInput = $(this).siblings('.quantity-input');
        let currentQty = parseInt($qtyInput.val());
        $qtyInput.val(currentQty + 1);
    });

    $(document).on('click', '.btn-decrement', function (e) {
        e.preventDefault();
        let $qtyInput = $(this).siblings('.quantity-input');
        let currentQty = parseInt($qtyInput.val());
        if (currentQty > 1) {
            $qtyInput.val(currentQty - 1);
        }
    });

   $(document).on('click', '.btn-delete-item', function (e) {
    e.preventDefault();
    const itemId = $(this).data('id');

    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    const token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    } else {
        console.error('CSRF token not found!');
    }

    const deleteUrl = "{{ route('cart.remove', ':id') }}".replace(':id', itemId);

    axios.delete(deleteUrl, {
        product_id: itemId
    })
    .then(res => {
        toastr.success(res.data.message);
        // optionally reload cart UI
        updateCartUI(res.data.cartItems);
    })
    .catch(err => {
        const msg = err.response?.data?.message || 'Something went wrong.';
        toastr.error(msg);
    });
});

</script>
@endpush