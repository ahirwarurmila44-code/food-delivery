@extends('frontend.layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Your Cart</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th><th>Price</th><th>Qty</th><th>Subtotal</th><th>Action</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @forelse ($cart as $id => $item)
                @php $subtotal = $item['price'] * $item['quantity']; $total += $subtotal; @endphp
                <tr data-id="{{ $id }}">
                    <td>{{ $item['name'] }}</td>
                    <td>₹{{ $item['price'] }}</td>
                    <td><input type="number" class="form-control quantity" value="{{ $item['quantity'] }}" min="1"></td>
                    <td>₹{{ $subtotal }}</td>
                    <td><button class="btn btn-danger btn-sm remove-btn">Remove</button></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">Your cart is empty</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">Total</th>
                <th colspan="2">₹{{ $total }}</th>
            </tr>
        </tfoot>
    </table>
</div>
@endsection

@push('scripts')
<script>
$('.quantity').on('change', function () {
    const row = $(this).closest('tr');
    const id = row.data('id');
    const qty = $(this).val();

    $.post('/cart/update', {
        product_id: id,
        quantity: qty,
        _token: '{{ csrf_token() }}'
    }, res => location.reload());
});

$('.remove-btn').on('click', function () {
    const row = $(this).closest('tr');
    const id = row.data('id');

    $.post('/cart/remove', {
        product_id: id,
        _token: '{{ csrf_token() }}'
    }, res => location.reload());
});
</script>
@endpush
