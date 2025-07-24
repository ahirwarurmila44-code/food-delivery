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
            <button class="btn btn-sm btn-outline-secondary btn-decrement" data-id="{{ $product->id }}">−</button>

            <input type="text" class="form-control text-center quantity-input" data-id="{{ $product->id }}" value="1" readonly style="width: 50px;">

            <button class="btn btn-sm btn-outline-secondary btn-increment" data-id="{{ $product->id }}">+</button>

            <button class="btn btn-sm btn-outline-primary add-to-cart-btn flex-grow-1" data-id="{{ $product->id }}">
                Add to Cart
            </button>
        </div>
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
document.addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.dataset.id;
            const quantity = document.querySelector(`.quantity-input[data-id="${productId}"]`).value;
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

                let token = document.querySelector('meta[name="csrf-token"]');
                if (token) {
                    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
                } else {
                    console.error('CSRF token not found!');
                }
                /////add API
           axios.post("{{ route('cart.add') }}", {
                product_id: productId,
                 quant_num: quantity
            })
            .then(res => {
                toastr.success(res.data.message);
                updateCartUI(res.data.cart_items);
                if (cartCount && res.data.cart_count !== undefined) {
                    cartCount.textContent = res.data.cart_count;
                    cartCount.classList.add('cart-animate');
                    setTimeout(() => cartCount.classList.remove('cart-animate'), 300);
                }
            })
            .catch(err => {
                if (err.response?.status === 401) {
                    toastr.error("Please log in to add to your cart.");
                } else {
                    //toastr.error(err.response?.data?.message || 'Something went wrong.');
                }
            });

        });
    });

     // ✅ Handle Increment
    document.querySelectorAll('.btn-increment').forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.dataset.id;
            const input = document.querySelector(`.quantity-input[data-id="${productId}"]`);
            let currentVal = parseInt(input.value);
            if (!isNaN(currentVal)) {
                input.value = currentVal + 1;
            }
        });
    });

     // ✅ Handle Decrement
    document.querySelectorAll('.btn-decrement').forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.dataset.id;
            const input = document.querySelector(`.quantity-input[data-id="${productId}"]`);
            let currentVal = parseInt(input.value);
            if (!isNaN(currentVal) && currentVal > 1) {
                input.value = currentVal - 1;
            }
        });
    });
    /////

    function updateCartUI(items) {
        const cartList = document.getElementById('cart-dropdown-list');
        const cartCount = document.getElementById('cart-count');

        if (!items || items.length === 0) {
            cartList.innerHTML = `<li class="text-muted text-center border-1">Cart is empty</li>`;
            cartCount.textContent = '0';
            return;
        }

        cartList.innerHTML = '';
        items.forEach(item => {
            cartList.innerHTML += `
                <li class="d-flex justify-content-between align-items-center mb-1 border border-primary px-2">
                    <span>${item.name} x ${item.qty}</span>
                    <small class="text-muted">₹${item.total}</small>
                </li>
            `;
        });

        cartList.innerHTML += `<li><hr class="dropdown-divider"></li>
                            <li><a href="/cart" class="dropdown-item text-center">View Cart</a></li>`;

        cartCount.textContent = items.length;
    }
///////////////////////////pplace orderd
    $('#placeOrderBtn').click(function () {
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
    });
    ////end
});
</script>
@endpush