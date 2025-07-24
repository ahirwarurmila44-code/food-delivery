@extends('layouts.admin.layouts')
@section('title', 'Customers')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4>Customer List</h4>
    <button class="btn btn-primary" id="createBtn" data-bs-toggle="modal" data-bs-target="#customerModal">
        <i class="bi bi-plus-circle me-1"></i> Add Customer
    </button>
</div>

<table class="table table-bordered" id="customerTable">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Action</th>
        </tr>
    </thead>
</table>

<!-- Modal -->
<div class="modal fade" id="customerModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="customerForm">
            @csrf
            <input type="hidden" id="customer_id" name="customer_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Phone</label>
                        <input type="text" name="phone" id="phone" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Save</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    let table = $('#customerTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.customers.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'name' },
            { data: 'email' },
            { data: 'phone' },
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    $('#createBtn').click(function () {
        $('#customerForm')[0].reset();
        $('#customer_id').val('');
        $('#customerModal .modal-title').text('Add Customer');
    });

    $('#customerForm').submit(function (e) {
        e.preventDefault();
        let id = $('#customer_id').val();
        let url = id ? `/admin/customers/${id}` : `{{ route('admin.customers.store') }}`;
        let method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            success: function (res) {
                $('#customerModal').modal('hide');
                table.ajax.reload();
                toastr.success(res.success);
            },
            error: function (err) {
                toastr.error('Please check input fields');
            }
        });
    });

    $('#customerTable').on('click', '.edit-btn', function () {
        let id = $(this).data('id');
        $.get(`/admin/customers/${id}`, function (data) {
            $('#customer_id').val(data.id);
            $('#name').val(data.name);
            $('#email').val(data.email);
            $('#phone').val(data.phone);
            $('#customerModal .modal-title').text('Edit Customer');
            $('#customerModal').modal('show');
        });
    });

    $('#customerTable').on('click', '.delete-btn', function () {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Delete?',
            text: 'Are you sure to delete this customer?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/customers/${id}`,
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
});
</script>
@endpush
