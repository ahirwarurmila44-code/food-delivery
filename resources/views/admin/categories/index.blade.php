@extends('layouts.admin.layouts')
@section('title', 'Categories list')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4></h4>
    <button class="btn btn-primary" id="createBtn" data-bs-toggle="modal" data-bs-target="#categoryModal">
        <i class="bi bi-plus-circle me-1"></i> Add Category
    </button>
</div>

<table class="table table-bordered" id="categoryTable">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Action</th>
        </tr>
    </thead>
</table>

<!-- Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="categoryForm">
            @csrf
            <input type="hidden" id="category_id" name="category_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label>Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
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
$(function () {
    let table = $('#categoryTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.categories.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'name' },
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    $('#createBtn').click(function () {
        $('#categoryForm')[0].reset();
        $('#category_id').val('');
        $('#categoryModal .modal-title').text('Add Category');
    });

    $('#categoryForm').submit(function (e) {
        e.preventDefault();
        let id = $('#category_id').val();
        let url = id ? `/admin/categories/${id}` : `{{ route('admin.categories.store') }}`;
        let type = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: type,
            data: $(this).serialize(),
            success: function (res) {
                $('#categoryModal').modal('hide');
                table.ajax.reload();
                toastr.success(res.success);
            },
            error: function () {
                toastr.error('Validation failed.');
            }
        });
    });

    $('#categoryTable').on('click', '.edit-btn', function () {
        let id = $(this).data('id');
        $.get(`/admin/categories/${id}`, function (data) {
            $('#category_id').val(data.id);
            $('#name').val(data.name);
            $('#categoryModal .modal-title').text('Edit Category');
            $('#categoryModal').modal('show');
        });
    });

    $('#categoryTable').on('click', '.delete-btn', function () {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This action is permanent!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/categories/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
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
