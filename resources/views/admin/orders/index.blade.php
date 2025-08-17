@extends('layouts.admin.layouts')
@section('title', 'Orders List')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4></h4>
    <button class="btn btn-primary" id="createBtn" data-bs-toggle="modal" data-bs-target="#orderModal">
        <i class="bi bi-plus-circle me-1"></i> Add Order
    </button>
</div>

<table class="table table-bordered" id="orderTable">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
             <th>Tracking</th>
            <th>Action</th>
        </tr>
    </thead>
</table>

<!-- Order Modal -->
<div class="modal fade" id="orderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="orderForm">
            @csrf
            <input type="hidden" name="order_id" id="order_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-6 mb-3">
                        <label>Customer</label>
                        <select name="customer_id" id="customer_id" class="form-select" required>
                            <option value="">-- Select --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <h6 class="mb-2">Products</h6>
                        <div id="productContainer">
                            @foreach($products as $product)
                            <div class="row align-items-center mb-2 product-row">
                                <div class="col-md-6">
                                    <input type="checkbox" class="form-check-input product-checkbox" value="{{ $product->id }}" data-price="{{ $product->price }}">
                                    {{ $product->name }} - ₹{{ $product->price }}
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control qty-input" placeholder="Qty" min="1" value="1" disabled>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="text-end mt-3">
                            <strong>Total: ₹<span id="totalAmount">0.00</span></strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Place Order</button>
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- order view modal -->
 <!-- View Order Modal -->
<div class="modal fade" id="viewOrderModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Order Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="orderDetails">
        <!-- Filled by JS -->
        
      </div>
    
  <input type="hidden" id="viewOrderId">


      <div class="modal-footer">
        <!--  -->
      <p><strong>Delivery Status:</strong>
  <select id="deliveryStatusSelect" class="form-select form-select-sm w-auto d-inline-block">
    <option value="pending">Pending</option>
    <option value="shipped">Shipped</option>
    <option value="in_transit">In Transit</option>
    <option value="delivered">Delivered</option>
    <option value="cancelled">Cancelled</option>
  </select>
</p>

<p><strong>Tracking Number:</strong>
  <input type="text" id="trackingNumberInput" class="form-control form-control-sm w-50">
</p>

<p><strong>Estimated Delivery:</strong>
  <input type="date" id="estimatedDeliveryInput" class="form-control form-control-sm w-50">
</p>

        <!--  -->
           <p>
        <strong>Status:</strong>
  <select id="orderStatusSelect" class="form-select form-select-sm w-auto d-inline-block">
    <option value="pending">Pending</option>
    <option value="completed">Completed</option>
    <option value="cancelled">Cancelled</option>
  </select>
  <button id="updateOrderStatusBtn" class="btn btn-sm btn-success ms-2">Update</button>
</p>
        <a href="#" id="pdfDownloadBtn" target="_blank" class="btn btn-dark">
            <i class="bi bi-download me-1"></i> Download PDF
        </a>
        <a href="#" id="whatsappLink" class="btn btn-success" target="_blank">
            <i class="bi bi-whatsapp"></i> WhatsApp
        </a>
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(function () {
    const table = $('#orderTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.orders.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'customer' },
            { data: 'total_price' },
            { data: 'status' },
            { data: 'delivery', name: 'delivery' },
            { data: 'action', orderable: false, searchable: false }
        ],
        dom: 'Bfrtip',
            buttons: [
                { extend: 'csv', text: 'Export CSV', className: 'btn btn-secondary btn-sm' },
                { extend: 'excel', text: 'Export Excel', className: 'btn btn-success btn-sm' },
                { extend: 'pdf', text: 'Export PDF', className: 'btn btn-danger btn-sm' },
                { extend: 'print', text: 'Print', className: 'btn btn-info btn-sm' }
            ],
            order: [[1, 'asc']]
    });

    //////////////////////////////////////////////////////////////////////////
        // let table = $('#customersTable').DataTable({
        //     processing: true,
        //     serverSide: true,
        //     ajax: "",
        //     columns: [
        //         {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false},
        //         {data: 'name', name: 'name'},
        //         {data: 'email', name: 'email'},
        //         {data: 'phone', name: 'phone'},
        //         {data: 'actions', name: 'actions', searchable: false, orderable: false},
        //     ],
        //     dom: 'Bfrtip',
        //     buttons: [
        //         { extend: 'csv', text: 'Export CSV', className: 'btn btn-secondary btn-sm' },
        //         { extend: 'excel', text: 'Export Excel', className: 'btn btn-success btn-sm' },
        //         { extend: 'pdf', text: 'Export PDF', className: 'btn btn-danger btn-sm' },
        //         { extend: 'print', text: 'Print', className: 'btn btn-info btn-sm' }
        //     ],
        //     order: [[1, 'asc']]
        // });

    //////////////////////////////////////////////////////////////////////////

    $('#createBtn').click(function () {
        $('#orderForm')[0].reset();
        $('#order_id').val('');
        $('#orderModal .modal-title').text('Add Order');
        $('#productContainer input:checkbox').prop('checked', false);
        $('#productContainer .qty-input').val(1).prop('disabled', true);
        $('#totalAmount').text('0.00');
    });

    // Enable quantity input only when checkbox is checked
    $(document).on('change', '.product-checkbox', function () {
        const qtyInput = $(this).closest('.product-row').find('.qty-input');
        qtyInput.prop('disabled', !this.checked);
        calculateTotal();
    });

    $(document).on('input', '.qty-input', function () {
        calculateTotal();
    });

    function calculateTotal() {
        let total = 0;
        $('#productContainer .product-row').each(function () {
            const checkbox = $(this).find('.product-checkbox');
            const qty = $(this).find('.qty-input').val();
            if (checkbox.is(':checked')) {
                total += checkbox.data('price') * qty;
            }
        });
        $('#totalAmount').text(total.toFixed(2));
    }

    $('#orderForm').submit(function (e) {
        e.preventDefault();
        let selected = [];
        let quantities = [];
        $('#productContainer .product-row').each(function () {
            const checkbox = $(this).find('.product-checkbox');
            const qty = $(this).find('.qty-input').val();
            if (checkbox.is(':checked')) {
                selected.push(checkbox.val());
                quantities.push(qty);
            }
        });

        const formData = $(this).serialize() + '&product_ids[]=' + selected.join('&product_ids[]=') + '&quantities[]=' + quantities.join('&quantities[]=');
        
        $.ajax({
            url: `{{ route('admin.orders.store') }}`,
            type: 'POST',
            data: formData,
            success: function (res) {
                $('#orderModal').modal('hide');
                table.ajax.reload();
                toastr.success(res.success);
            },
            error: function () {
                toastr.error('Error submitting order');
            }
        });
    });

    // Delete
    $('#orderTable').on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Delete Order?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/orders/${id}`,
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

     //order view and pdf 
    $('#orderTable').on('click', '.view-btn', function () {
        const id = $(this).data('id');
        $.get(`/admin/orders/${id}/view`, function (data) {
             $('#viewCustomerName').text(data.customer.name);
            $('#orderStatusSelect').val(data.status); // set current status
            $('#viewOrderTotal').text(`₹${data.total_price}`);
            $('#viewOrderId').val(data.id); // store order ID
            $('#deliveryStatusSelect').val(data.delivery_status);
            $('#trackingNumberInput').val(data.tracking_number ?? '');
            $('#estimatedDeliveryInput').val(data.estimated_delivery ?? '');

            let html = `
                <h6>Customer: ${data.customer.name} (${data.customer.email})</h6>
                <p>Status: <strong>${data.status.toUpperCase()}</strong></p>
                <table class="table table-sm mt-3">
                    <thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
                    <tbody>`;

            let total = 0;
            data.items.forEach(item => {
                let itemTotal = item.quantity * item.price;
                total += itemTotal;
                html += `
                    <tr>
                        <td>${item.product.name}</td>
                        <td>${item.quantity}</td>
                        <td>₹${item.price}</td>
                        <td>₹${itemTotal}</td>
                    </tr>`;
            });

            html += `
                    <tr><td colspan="3" class="text-end"><strong>Total</strong></td><td><strong>₹${total}</strong></td></tr>
                    </tbody>
                </table>`;

            $('#orderDetails').html(html);
            $('#pdfDownloadBtn').attr('href', `/admin/orders/${id}/invoice`);
            $('#viewOrderModal').modal('show');
        });
    });


    /////

    $('#updateOrderStatusBtn').click(function () {
    const id = $('#viewOrderId').val();
    const status = $('#orderStatusSelect').val();
    const delivery_status = $('#deliveryStatusSelect').val();
    const tracking_number = $('#trackingNumberInput').val();
    const estimated_delivery = $('#estimatedDeliveryInput').val();
    $.ajax({
        url: `/admin/orders/${id}/status`,
        method: 'POST',
        data: {status: status,
            delivery_status,
            tracking_number,
            estimated_delivery
        },
        success: function (res) {
            $('#orderViewModal').modal('hide');
            toastr.success(res.success);
            $('#orderTable').DataTable().ajax.reload(null, false); // reload table
        },
        error: function () {
            toastr.error("Failed to update order status.");
        }
    });
});

    const whatsappMessage = `Hi ${data.customer.name}, your invoice for Order #${data.id} is ready. Download it here: {{ url('/admin/orders') }}/${data.id}/invoice`;
    $('#whatsappLink').attr('href', `https://wa.me/${data.customer.phone}?text=${encodeURIComponent(whatsappMessage)}`);



//     Add timestamps for status change

// 📨 Notify customer via email on status change

// 📦 Add delivery tracking (if applicable)

// 🔍 Filter orders by status
// Add "Send tracking update email" to customer?



});
</script>
@endpush
