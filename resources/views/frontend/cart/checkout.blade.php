@extends('frontend.layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Checkout</h2>
    <form id="checkoutForm" method="POST" action="{{ route('checkout.placeOrder') }}">
        @csrf
        <div class="row">
            <!-- Billing Details -->
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        Billing Details
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ auth()->user()->name ?? '' }}" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ auth()->user()->email ?? '' }}" required>
                        </div>
                        <div class="mb-3">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Address</label>
                            <textarea name="address" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label>City</label>
                            <input type="text" name="city" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Postal Code</label>
                            <input type="text" name="postal_code" class="form-control" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        Order Summary
                    </div>
                    <div class="card-body">
                        <ul class="list-group mb-3">
                            @forelse ($cartItems as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $item->name }}</strong><br>
                                        x{{ $item->quantity }}
                                    </div>
                                    <span>₹{{ number_format($item->price * $item->quantity, 2) }}</span>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted">Your cart is empty.</li>
                            @endforelse
                        </ul>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Subtotal</strong>
                            <span>₹{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <strong>Delivery Fee</strong>
                            <span>₹{{ number_format($deliveryFee, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <strong>Total</strong>
                            <span class="text-success fw-bold">₹{{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        Payment Method
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" value="cod" id="cod" checked>
                            <label class="form-check-label" for="cod">
                                Cash on Delivery
                            </label>
                        </div>
                        <!-- More payment options (Razorpay, Stripe) can be added here -->
                    </div>
                </div>

                <!-- Place Order Button -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        Place Order
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
