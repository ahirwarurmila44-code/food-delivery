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

                <div class="col-md-2 text-end">
                    <strong>₹{{ number_format($item->total, 2) }}</strong>
                </div>

                <div class="col-md-1 text-end">
                    <button class="btn btn-sm btn-danger btn-delete-item" data-id="{{ $item->id }}">
                        <i class="fas fa-trash"></i>
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
            <a href="{{ route('checkout') }}" class="btn btn-primary btn-lg">Proceed to Checkout</a>
        </div>
    @endif
</div>

@endsection