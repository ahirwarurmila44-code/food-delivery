@extends('frontend.layouts.app')

@section('content')
<h2>All Products</h2>
<div class="row">
    @foreach ($products as $product)
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h5>{{ $product->name }}</h5>
                    <p>₹{{ $product->price }}</p>
                    <div class="mb-3">
                    <img src="{{ asset('storage/' . $product->image) }}" width="100" height="100" class="img-thumbnail" />
                </div>
                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-primary btn-sm">View</a>
                </div>
            </div>
        </div>
    @endforeach
</div>
{{ $products->links() }}
@endsection
