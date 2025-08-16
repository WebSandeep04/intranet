@extends('layouts.app')

@section('title', 'Customer Management')
@section('page_title', 'Customer Management')

@section('content')
<div class="container mt-4">
    <div id="alertBox"></div>
    
    <button class="btn button" data-bs-toggle="modal" data-bs-target="#createCustomerModal">
        <i class="bi bi-plus-lg"></i> Create Customer
    </button>
        
    <div class="table-responsive mt-3">
        <table class="table table-hover table-bordered align-middle text-center border shadow-sm rounded" id="customerTable">
            <thead class="table-secondary">
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Phone</th>
                    <th scope="col">Company</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows will be loaded via jQuery -->
            </tbody>
        </table>
    </div>
</div>

<!-- Create Customer Modal -->
<div class="modal fade" id="createCustomerModal" tabindex="-1" aria-labelledby="createCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="createCustomerForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createCustomerModalLabel">Create Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Customer Name *</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="company_name" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="company_name" name="company_name">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn button w-100">Create Customer</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Customer Modal -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editCustomerForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="edit_customer_id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Customer Name *</label>
                        <input type="text" class="form-control" id="edit_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email">
                    </div>
                    <div class="mb-3">
                        <label for="edit_phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="edit_phone">
                    </div>
                    <div class="mb-3">
                        <label for="edit_company_name" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="edit_company_name">
                    </div>
                    <div class="mb-3">
                        <label for="edit_address" class="form-label">Address</label>
                        <textarea class="form-control" id="edit_address" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn button w-100">Update Customer</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showAlert(type, message) {
    let colorClass = 'custom-alert-' + type;
    $('#alertBox').html(`
        <div class="custom-alert ${colorClass}">
            ${message}
            <button class="custom-alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
    `);
    setTimeout(() => $('.custom-alert').fadeOut(500, function() { $(this).remove(); }), 3000);
}

$(function () {
    loadCustomers();

    function loadCustomers() {
        $.get("{{ route('customer.fetch') }}", function (data) {
            let rows = '';
            $.each(data, function (i, customer) {
                rows += `<tr>
                    <td>${customer.name}</td>
                    <td>${customer.email || '-'}</td>
                    <td>${customer.phone || '-'}</td>
                    <td>${customer.company_name || '-'}</td>
                    <td>
                        <button class="btn btn-sm btn-primary editBtn" data-id="${customer.id}" 
                                data-name="${customer.name}" data-email="${customer.email || ''}" 
                                data-phone="${customer.phone || ''}" data-company="${customer.company_name || ''}" 
                                data-address="${customer.address || ''}">
                            <i class="bi bi-pencil-square me-1"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="${customer.id}">
                            <i class="bi bi-trash3-fill me-1"></i> Delete
                        </button>
                    </td>
                </tr>`;
            });
            $('#customerTable tbody').html(rows);
        });
    }

    $('#createCustomerForm').submit(function (e) {
        e.preventDefault();
        $.post("{{ route('customer.store') }}", {
            name: $('#name').val(),
            email: $('#email').val(),
            phone: $('#phone').val(),
            company_name: $('#company_name').val(),
            address: $('#address').val(),
            _token: '{{ csrf_token() }}'
        }, function (response) {
            if (response.success) {
                $('#createCustomerModal').modal('hide');
                $('#createCustomerForm')[0].reset();
                loadCustomers();
                showAlert('success', 'Customer created successfully.');
            }
        }).fail(function (xhr) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                alert(Object.values(xhr.responseJSON.errors).join("\n"));
            } else {
                alert('Error creating customer.');
            }
        });
    });

    $(document).on('click', '.editBtn', function () {
        $('#edit_customer_id').val($(this).data('id'));
        $('#edit_name').val($(this).data('name'));
        $('#edit_email').val($(this).data('email'));
        $('#edit_phone').val($(this).data('phone'));
        $('#edit_company_name').val($(this).data('company'));
        $('#edit_address').val($(this).data('address'));
        $('#editCustomerModal').modal('show');
    });

    $('#editCustomerForm').submit(function (e) {
        e.preventDefault();
        let id = $('#edit_customer_id').val();
        $.ajax({
            url: `/customer/${id}`,
            type: 'PUT',
            data: {
                name: $('#edit_name').val(),
                email: $('#edit_email').val(),
                phone: $('#edit_phone').val(),
                company_name: $('#edit_company_name').val(),
                address: $('#edit_address').val(),
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                if (response.success) {
                    $('#editCustomerModal').modal('hide');
                    loadCustomers();
                    showAlert('success', 'Customer updated successfully.');
                }
            },
            error: function (xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    alert(Object.values(xhr.responseJSON.errors).join("\n"));
                } else {
                    alert('Error updating customer.');
                }
            }
        });
    });

    $(document).on('click', '.deleteBtn', function () {
        if (confirm('Are you sure you want to delete this customer?')) {
            $.ajax({
                url: `/customer/${$(this).data('id')}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function (response) {
                    if (response.success) {
                        loadCustomers();
                        showAlert('success', 'Customer deleted successfully.');
                    }
                },
                error: function () {
                    alert('Error deleting customer.');
                }
            });
        }
    });
});
</script>
@endpush
