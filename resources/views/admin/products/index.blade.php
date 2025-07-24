@extends('layouts.admin.layouts')
@section('title', 'Products List')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4></h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" id="createBtn">
        <i class="bi bi-plus-circle me-1"></i> Add Product
    </button>
</div>

<table class="table table-bordered" id="productTable">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Image</th>
            <th>Category</th>
            <th>Restaurant</th>
            <th>Price</th>
            <th>Available</th>
            <th>Action</th>
        </tr>
    </thead>
</table>

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="productForm">
            @csrf
            <input type="hidden" name="product_id" id="product_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Price</label>
                        <input type="number" name="price" id="price" class="form-control" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label>Restaurants</label>
                        <select name="restaurant_id" id="restaurant_id" class="form-select" required>
                            <option value="">-- Select Restaurant --</option>
                            @foreach(\App\Models\Admin\Restaurant::all() as $restaurant)
                                <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Category</label>
                        <select name="category_id" id="category_id" class="form-select" required>
                            <option value="">-- Select Category --</option>
                            @foreach(\App\Models\Admin\Category::all() as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Image</label>
                        <input type="file" class="form-control" id="image" name="image" >
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="available" name="available" value="1">
                        <label class="form-check-label" for="available">Available</label>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>



@endsection

@push('scripts')
<script>
$(document).ready(function () {
    let table = $('#productTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.products.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'name' },
            { data: 'image' },
            { data: 'category' },
            { data: 'restaurant' },
            { data: 'price' },
            { data: 'available', className: 'text-center'  },
            { data: 'action', className: 'text-center' , orderable: false, searchable: false }
        ]
    });
    $('#createBtn').click(() => {
        $('#productForm')[0].reset();
        $('#product_id').val('');
        $('#productModal .modal-title').text('Add Product');
    });

    $('#productForm').submit(function (e) {
    e.preventDefault();

    let id = $('#product_id').val();
    let formData = new FormData(this);
    let url = id ? `/admin/products/${id}` : `{{ route('admin.products.store') }}`;
    let type = id ? 'POST' : 'POST'; 

    if (id) {
        formData.append('_method', 'PUT'); 
    }

    $.ajax({
        url: url,
        type: type,
        data: formData,
        processData: false, 
        contentType: false, 
        success: function (res) {
            $('#productModal').modal('hide');
            $('#productForm')[0].reset(); 
            $('#product_id').val(''); 
            table.ajax.reload(); 
            toastr.success(res.success || 'Saved successfully.');
        },
        error: function (err) {
            if (err.responseJSON && err.responseJSON.errors) {
                let errors = err.responseJSON.errors;
                Object.keys(errors).forEach(function (key) {
                    toastr.error(errors[key][0]);
                });
            } else {
                toastr.error('Something went wrong. Please try again.');
            }
        }
    });
});


    // Edit
    $('#productTable').on('click', '.edit-btn', function () {
        let id = $(this).data('id');
        $.get(`/admin/products/${id}`, function (data) {
            $('#product_id').val(data.id);
            $('#name').val(data.name);
            //$('#image').val(data.image);
            $('#category_id').val(data.category_id);
            $('#restaurant_id').val(data.restaurant_id);
            $('#price').val(data.price);
            $('#available').prop('checked', data.available);
            $('#productModal .modal-title').text('Edit Product');
            $('#productModal').modal('show');
        });
    });

    // Delete
    $('#productTable').on('click', '.delete-btn', function () {
        let id = $(this).data('id');
        alert(id);
        Swal.fire({
            title: 'Delete?',
            text: 'This action is irreversible!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/products/${id}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        table.ajax.reload();
                        toastr.success(res.success);
                    }
                });
            }
        });
    });

    //Status Toggle
    $("#productTable").on('click', '.toggle-status', function () {
        let button = $(this);
        let id = button.data('id');

        $.ajax({
            url: "{{ route('admin.products.toggle.available') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: id
            },
            success: function (response) {
                $('#productTable').DataTable().ajax.reload(null, false); // reload row
                toastr.success(response.message);
            },
            error: function () {
                toastr.error('Status change failed.');
            }
        });
    });
    // Add To Cart
   $('#productTable').on('click','.add-to-cart', function () {
        const id = $(this).data('id');
        $.post('/cart/add', {
            product_id: id,
            _token: '{{ csrf_token() }}'
        }, function (res) {
            toastr.success(res.success);
        });
    });


});
</script>
@endpush
