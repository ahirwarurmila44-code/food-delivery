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
                <form >
                    @csrf
                    <div class="d-flex align-items-center gap-3">        
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-decrement" data-id="{{ $product->id }}">−</button>
                        <input type="text" class="form-control text-center quantity-input" value="1" readonly style="width: 50px;">
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-increment" data-id="{{ $product->id }}">+</button>
                        <button type="button"
                                class="btn btn-primary btn-lg shadow-sm add-to-cart-btn"
                                data-id="{{ $product->id }}">
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
$(document).ready(function(){
    $('.add-to-cart-btn').on('click', function (e) {
        e.preventDefault();

        const productId = $(this).data('id');
        const quantity = $(this).closest('form').find('.quantity-input').val();

        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
        } else {
            console.error('CSRF token not found!');
        }

        axios.post("{{ route('cart.add') }}", {
            product_id: productId,
            quant_num: quantity
        })
        .then(res => {
            toastr.success(res.data.message);
            updateCartUI(res.data.cart_items);
            const cartCount = document.getElementById('cart-count');

            if (cartCount && res.data.cart_count !== undefined) {
                cartCount.textContent = res.data.cart_count;
                cartCount.classList.add('cart-animate');
                setTimeout(() => cartCount.classList.remove('cart-animate'), 300);
            }
        })
        .catch(err => {
            console.error(err.response || err);
            if (err.response?.status === 401) {
                toastr.error("Please log in to add to your cart.");
            } else {
                const msg = err.response?.data?.message || 'Something went wrong.';
                        toastr.error(msg);
            }
        });

    });

    // });

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


     
});

</script>
@endpush
