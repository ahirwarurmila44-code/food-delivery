@extends('layouts.admin.layouts')

@section('title', 'Restaurants List')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4></h4>
  <button class="btn btn-primary" id="addNewBtn">Add Restaurant</button>
</div>

<table class="table table-bordered" id="restaurantTable">
  <thead>
    <tr>
      <th>#</th>
      <th>Name</th>
      <th>Image</th>
      <th>Email</th>
      <th>Phone</th>
      <th>Address</th>
      <th>Actions</th>
    </tr>
  </thead>
</table>

<!-- Modal -->
<div class="modal fade" id="restaurantModal" tabindex="-1" aria-labelledby="restaurantModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="restaurantForm">
      @csrf
      <input type="hidden" name="restaurant_id" id="restaurant_id">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="restaurantModalLabel">Add Restaurant</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label>Name</label>
            <input type="text" name="name" class="form-control" id="name">
          </div>
          <div class="mb-2">
            <label>Email</label>
            <input type="email" name="email" class="form-control" id="email">
          </div>
          <div class="mb-2">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" id="phone">
          </div>
          <div class="mb-2">
            <label>Address</label>
            <textarea name="address" class="form-control" id="address"></textarea>
          </div>
          <div class="mb-2">
            <label>Image</label>
            <input type="file" name="image" class="form-control" id="image"></input>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success" id="saveBtn">Save</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
  let table = $('#restaurantTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.restaurants.index') }}",
    columns: [
      { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false },
      { data: 'name', name: 'name' },
      { data: 'image', name: 'image' },
      { data: 'email', name: 'email' },
      { data: 'phone', name: 'phone' },
      { data: 'address', name: 'address' },
      { data: 'action', name: 'action', orderable: false, searchable: false },
    ],
  });

  $('#addNewBtn').click(function () {
    $('#restaurantForm')[0].reset();
    $('#restaurant_id').val('');
    $('#restaurantModalLabel').text('Add Restaurant');
    $('#restaurantModal').modal('show');
  });

  // Store/Update
  $('#restaurantForm').submit(function (e) {
    e.preventDefault();
    const id = $('#restaurant_id').val();
    let formData = new FormData(this);
    const url = id ? `/admin/restaurants/${id}` : `{{ route('admin.restaurants.store') }}`;
    let method = id ? 'POST' : 'POST'; 
    if (id) {
        formData.append('_method', 'PUT'); 
    }
    $.ajax({
      url: url,
      type: method,
      data: formData,
      processData: false, 
      contentType: false,
      success: function (res) {
        toastr.success(res.message);
        $('#restaurantModal').modal('hide');
        table.ajax.reload();
      },
      error: function (err) {
        if (err.responseJSON.errors) {
          $.each(err.responseJSON.errors, function (key, value) {
            toastr.error(value[0]);
          });
        } else {
          toastr.error('Something went wrong.');
        }
      }
    });
  });

  // Edit
  $('#restaurantTable').on('click', '.editBtn', function () {
    let id = $(this).data('id');
    $.get(`/admin/restaurants/${id}`, function (res) {
      $('#restaurantModalLabel').text('Edit Restaurant');
      $('#restaurant_id').val(res.id);
      $('#name').val(res.name);
      $('#email').val(res.email);
      $('#phone').val(res.phone);
      $('#address').val(res.address);
      $('#restaurantModal').modal('show');
    });
  });

  // Delete
  $(document).on('click', '.deleteBtn', function () {
    const id = $(this).data('id');
    Swal.fire({
      title: 'Are you sure?',
      text: 'This will delete the restaurant!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: `/admin/restaurants/delete/${id}`,
          type: 'DELETE',
          data: { _token: '{{ csrf_token() }}' },
          success: function (res) {
            toastr.success(res.message);
            table.ajax.reload();
          },
          error: function () {
            toastr.error('Failed to delete!');
          }
        });
      }
    });
  });
});
</script>
@endpush
