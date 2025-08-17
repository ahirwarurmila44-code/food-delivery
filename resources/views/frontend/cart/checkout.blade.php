@extends('frontend.layouts.app')
@section('title', 'Checkout')

@section('content')
<div class="container py-3">
    <!-- <h2 class="mb-4">Checkout</h2> -->
    <form id="checkoutForm" >    
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
                            <textarea name="address" class="form-control" rows="3"  required></textarea>
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
                            @forelse ($cart as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $item->product->price }} x{{ $item->quantity }}</strong><br>
                                       
                                    </div>
                                    <span>₹{{ number_format($item->product->price * $item->quantity, 2) }}</span>
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
                            <span class="text-success fw-bold" id="order_total">₹{{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        Payment Method
                    </div>
                    <div class="card-body">
                        <!-- <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" value="cod" id="cod" checked>
                            <label class="form-check-label" for="cod">
                                Cash on Delivery
                            </label>
                        </div>
                        
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" value="razorpay" id="razorpay">
                            <label class="form-check-label" for="razorpay">
                                Razorpay (UPI / Cards / Netbanking)
                            </label>
                        </div>
                        
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" value="stripe" id="stripe">
                            <label class="form-check-label" for="stripe">
                                Stripe (Credit/Debit Card)
                            </label>
                        </div> -->

                        <select class="form-control" name="payment_method" id="payment_method">
                            <option value="">Choose Payment Method</option>
                            <option value="cod">Cash on delivery</option>
                            <option value="razorpay" id="razorpay">Razorpay (UPI / Cards / Netbanking)</option>
                            <option value="stripe">Stripe (Credit/Debit Card)</option>
                        </select>
                    </div>
                </div>

                <!-- Place Order Button -->
                <div class="d-grid">
                    <button type="submit" id="placeOrderBtn" class="btn btn-primary btn-lg">
                        Place Order
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
@push('scripts')
<script>
   
    $('#placeOrderBtn').click(function () {
   // let paymentMethod = $('select[name="payment_method"]:checked').val();
        let paymentMethod = $('#payment_method').val();
        let totalAmt = $('#order_total').text().replace(/[^\d.]/g, ''); // Removes ₹ and keeps numbers
    if (paymentMethod === 'razorpay') {
        $.ajax({
            url: '{{ route("cart.razorpay.order") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                amount: totalAmt
            },
            success: function (data) {
                var options = {
                    "key": "{{ env('RAZORPAY_KEY') }}",
                    "amount": data.amount,
                    "currency": "INR",
                    "name": "Food Delivery App",
                    "description": "Order Payment",
                    "order_id": data.order_id,
                    "handler": function (response) {
                        // on successful payment
                        $.post("{{ route('cart.placeOrder') }}", {
                            _token: '{{ csrf_token() }}',
                            address: $('#address').val(),
                            payment_method: 'razorpay',
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_order_id: response.razorpay_order_id,
                            razorpay_signature: response.razorpay_signature,
                            total: totalAmt
                        }, function (res) {
                            toastr.success(res.message);
                            window.location.href = '/orders';
                        });
                    },
                    "theme": {
                        "color": "#3399cc"
                    }
                };
                var rzp = new Razorpay(options);
                rzp.open();
            }
        });
    } else {
        $.ajax({
            url: "{{ route('cart.placeOrder') }}",
            type: "POST",
            headers: {
                'Content-Type': 'application/json'
            },
            data: JSON.stringify({
                _token: "{{ csrf_token() }}",
                address: $('#address').val(),
                payment_method: $('#payment_method').val(),
                total: $('#order_total').val(),
                items: {!! $cart->map(function($item) {
                    return [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->product->price,
                    ];
                })->values()->toJson() !!}
            }),
            success: function (res) {
                toastr.success(res.message);
                window.location.href = '/orders';
            },
            error: function (xhr) {
                toastr.error(xhr.responseJSON?.message || 'Something went wrong!');
            }
        });
    }
});

  
</script>
@endpush