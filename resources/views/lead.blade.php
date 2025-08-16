@extends('layouts.app')

@section('title', 'Sales Product')
@section('page_title', 'Sales Product')

@push('styles')
<style>
    .gradient-jumbotron {
        background: linear-gradient(to right, #6a11cb, #2575fc);
        padding: 1.3rem;
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        color: white;
    }
    .gradient-jumbotron .form-control,
    .gradient-jumbotron .form-select {
        background-color: white;
        color: #000;
    }
    .gradient-jumbotron label {
        color: #fff;
        font-weight: 500;
    }
</style>
@endpush

@section('content')
<div class="container mt-4">
 <div class="gradient-jumbotron">
  <!-- <h4 class="mb-4 text-center">Lead Information Form</h4> -->

  <form method="POST" action="" id="leadForm">
    <div class="row g-4">
      <!-- Column 1 -->
      <div class="col-md-4">
        <div class="mb-2">
          <label for="prospectus" class="form-label">Prospectus</label>
          <select class="form-select" id="prospectus" name="prospectus" required>
            <option value="">Select Prospectus</option>
          </select>
            <a href="#" class="small text-light text-decoration-none mt-1 d-inline-block" data-bs-toggle="modal" data-bs-target="#addProspectusModal">
            + Add New Prospectus
            </a>
        </div>

        <div class="mb-2">
          <label for="leadsName" class="form-label">Lead Name</label>
          <input type="text" class="form-control" id="leadsName" name="leads_name" placeholder="Enter Lead Name" readonly>
        </div>

        <div class="mb-2">
          <label for="contactPerson" class="form-label">Contact Person</label>
          <input type="text" class="form-control" id="contactPerson" name="contact_person" placeholder="Enter Contact Person Name" readonly>
        </div>

        <div class="mb-2">
          <label for="contactNumber" class="form-label">Contact Number</label>
          <input type="tel" class="form-control" id="contactNumber" name="contact_number" placeholder="Enter Contact Number" readonly>
        </div>

 <div class="mb-2">
  <label for="sales_status" class="form-label">Status</label>
  <select class="form-select" id="sales_status" name="sales_status" required>
    <option value="">Loading...</option>
  </select>
</div>
</div>
      

      <!-- Column 2 -->
      <div class="col-md-4">
        <div class="mb-2">
          <label for="address" class="form-label">Address</label>
          <input type="text" class="form-control" id="address" name="address" placeholder="Enter Address" readonly>
        </div>

     <div class="mb-2">
  <label for="state" class="form-label">State</label>
  <select class="form-select" id="state" name="state" required>
    <option value="">Select State</option>
  </select>
</div>


    <div class="mb-2">
  <label for="city" class="form-label">City</label>
  <select class="form-select" id="city" name="city">
    <option value="">Select City</option>
  </select>
</div>


        <div class="mb-2">
          <label for="email" class="form-label">Email ID</label>
          <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email ID" readonly>
        </div>

        <div class="mb-2">
          <label for="next_follow_up" class="form-label">Next Follow-up Date</label>
          <input type="date" class="form-control" id="next_follow_up" name="next_follow_up_date">
        </div>
      </div>

      <!-- Column 3 -->
      <div class="col-md-4">
        <div class="mb-2">
  <label for="business_type" class="form-label">Business Type</label>
  <select class="form-select" id="business_type" name="business_type" required>
    <option value="">Loading...</option>
  </select>
</div>

        

        

         <div class="mb-2">
  <label for="lead_source" class="form-label">Lead Sources</label>
  <select class="form-select" id="lead_source" name="lead_source" required>
    <option value="">Loading...</option>
  </select>
</div>

           <div class="mb-2">
  <label for="product_type" class="form-label">Product Type</label>
  <select class="form-select" id="product_type" name="product_type" required>
    <option value="">Loading...</option>
  </select>
</div>


        <div class="mb-2">
          <label for="remark" class="form-label">Remark</label>
          <textarea class="form-control" id="remark" name="remark" placeholder="Enter Remark" rows="4"></textarea>
        </div>
      </div>
    </div>

    <!-- Submit button centered -->
    <div class="row mt-4">
      <div class="col-12 d-flex justify-content-center">
        <button type="submit" onclick="submitLead(event)" class="btn button  fw-bold px-5 w-50 shadow">Submit</button>
      </div>
    </div>
  </form>
</div>


<!-- prospect modal -->
 <!-- Add Prospectus Modal -->
<div class="modal fade" id="addProspectusModal" tabindex="-1" aria-labelledby="addProspectusModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="addProspectusModalLabel">Add New Prospectus</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Body -->
      <div class="modal-body">
        <form id="addProspectusForm">
          <div class="row g-3">

            <div class="col-md-6">
              <label for="modalnewProspectusName" class="form-label">Prospect Name</label>
              <input type="text" class="form-control" id="modalnewProspectusName" name="modal_new_prospectus_name" placeholder="Enter Prospectus Name" required>
            </div>

            <div class="col-md-6">
              <label for="modal_contact_person" class="form-label">Contact Person</label>
              <input type="text" class="form-control" id="modal_contact_person" name="modal_contact_person" placeholder="Enter Contact Person" required>
            </div>

            <div class="col-md-6">
              <label for="modal_contact_number" class="form-label">Contact Number</label>
              <input type="text" class="form-control" id="modal_contact_number" name="modal_contact_number" placeholder="Enter Contact Number" required>
            </div>

            <div class="col-md-6">
              <label for="modal_address" class="form-label">Address</label>
              <input type="text" class="form-control" id="modal_address" name="modal_address" placeholder="Enter Address" required>
            </div>

            <div class="col-md-6">
              <label for="modal_state" class="form-label">State</label>
              <select class="form-select" id="modal_state" name="modal_state" required>
                <option value="">Select State</option>
              </select>
            </div>

            <div class="col-md-6">
              <label for="modal_city" class="form-label">City</label>
              <select class="form-select" id="modal_city" name="modal_city" required>
                <option value="">Select City</option>
              </select>
            </div>

            <div class="col-md-6">
              <label for="modal_email" class="form-label">Email</label>
              <input type="email" class="form-control" id="modal_email" name="modal_email" placeholder="Enter Email" required>
            </div>

            

                  <div class="col-md-6">
  <label for="modal_business_type" class="form-label">Business Type</label>
  <select class="form-select" id="modal_business_type" name="modal_business_type" required>
    <option value="">Loading...</option>
  </select>
</div>

          </div>
        </form>
      </div>

      <!-- Footer -->
      <div class="modal-footer justify-content-center" style="background: linear-gradient(135deg, #1e3c72, #2a5298)">
        <button type="submit" onclick="submitProspect(event)" class="btn button fw-bold" form="addProspectusForm">Save</button>
      </div>

    </div>
  </div>
</div>

  </div>
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
    setTimeout(() => $('.custom-alert').fadeOut(500, function() { $(this).remove(); }), 2000);
}

// get prospect
$(document).ready(function(){
  prospect();
})
 function prospect(){
  $.ajax({
        url: "{{ route('getProspectus') }}",
        type: 'GET',
        success: function (data) {
            $('#prospectus').empty().append('<option value="">Select Status</option>');
            $.each(data, function (key, status) {
                $('#prospectus').append(`<option value="${status.id}">${status.prospectus_name}</option>`);
            });
        },
        error: function () {
            alert('Failed to load Prospect.');
        }
    });
 }
// get state

$(document).ready(function () {
    $.ajax({
        url: "{{ route('state') }}",
        type: "GET",
        dataType: "json",
        success: function (states) {
            let $stateDropdown = $('#state');
            $stateDropdown.empty();
            $stateDropdown.append('<option value="">Select State</option>');
            
            $.each(states, function (id, name) {
                $stateDropdown.append(`<option value="${id}">${name}</option>`);
            });
        },
        error: function () {
            alert("Failed to load states.");
        }
    });
});

// get cities
 $('#state').on('change', function () {
        var stateId = $(this).val();

        if (stateId) {
            $.ajax({
                url: "/city/" + stateId,
                type: "GET",
                dataType: "json",
                success: function (cities) {
                    $('#city').empty().append('<option value="">Select City</option>');
                    $.each(cities, function (id, name) {
                        $('#city').append(`<option value="${id}">${name}</option>`);
                    });
                },
                error: function () {
                    alert('Could not fetch cities.');
                }
            });
        } else {
            $('#city').empty().append('<option value="">Select City</option>');
        }
    });

    // get all cities
      $.ajax({
        url: "{{ route('allcity') }}", // Define this route in your web.php
        type: "GET",
        success: function (data) {
            $('#city').empty().append('<option value="">Select City</option>');
            $.each(data, function (index, type) {
                $('#city').append(`<option value="${type.id}">${type.city_name}</option>`);
            });
        },
        error: function () {
            $('#city').html('<option value="">Unable to load types</option>');
        }
    });

    // get status
     $.ajax({
        url: "{{ route('getStatuses') }}",
        type: 'GET',
        success: function (data) {
            $('#sales_status').empty().append('<option value="">Select Status</option>');
            $.each(data, function (key, status) {
                $('#sales_status').append(`<option value="${status.id}">${status.status_name}</option>`);
            });
        },
        error: function () {
            alert('Failed to load sales statuses.');
        }
    });

    // business type 

      $.ajax({
        url: "{{ route('getbusiness') }}", // Define this route in your web.php
        type: "GET",
        success: function (data) {
            $('#business_type').empty().append('<option value="">Select Business Type</option>');
            $.each(data, function (index, type) {
                $('#business_type').append(`<option value="${type.id}">${type.business_name}</option>`);
            });
        },
        error: function () {
            $('#business_type').html('<option value="">Unable to load types</option>');
        }
    });

    // get sources

      $.ajax({
        url: "{{ route('getsource') }}", // Define this route in your web.php
        type: "GET",
        success: function (data) {
            $('#lead_source').empty().append('<option value="">Select Source</option>');
            $.each(data, function (index, type) {
                $('#lead_source').append(`<option value="${type.id}">${type.source_name}</option>`);
            });
        },
        error: function () {
            $('#lead_source').html('<option value="">Unable to load types</option>');
        }
    });

// get product

      $.ajax({
        url: "{{ route('getproduct') }}", // Define this route in your web.php
        type: "GET",
        success: function (data) {
            $('#product_type').empty().append('<option value="">Select Product Type</option>');
            $.each(data, function (index, type) {
                $('#product_type').append(`<option value="${type.id}">${type.product_name}</option>`);
            });
        },
        error: function () {
            $('#product_type').html('<option value="">Unable to load types</option>');
        }
    });


    // modal state

    $(document).ready(function () {
    $.ajax({
        url: "{{ route('state') }}",
        type: "GET",
        dataType: "json",
        success: function (states) {
            let $stateDropdown = $('#modal_state');
            $stateDropdown.empty();
            $stateDropdown.append('<option value="">Select State</option>');
            
            $.each(states, function (id, name) {
                $stateDropdown.append(`<option value="${id}">${name}</option>`);
            });
        },
        error: function () {
            alert("Failed to load states.");
        }
    });
});

// modal cities
 $('#modal_state').on('change', function () {
        var stateId = $(this).val();

        if (stateId) {
            $.ajax({
                url: "/city/" + stateId,
                type: "GET",
                dataType: "json",
                success: function (cities) {
                    $('#modal_city').empty().append('<option value="">Select City</option>');
                    $.each(cities, function (id, name) {
                        $('#modal_city').append(`<option value="${id}">${name}</option>`);
                    });
                },
                error: function () {
                    alert('Could not fetch cities.');
                }
            });
        } else {
            $('#modal_city').empty().append('<option value="">Select City</option>');
        }
    });

    // modal business type
      $.ajax({
        url: "{{ route('getbusiness') }}", // Define this route in your web.php
        type: "GET",
        success: function (data) {
            $('#modal_business_type').empty().append('<option value="">Select Business Type</option>');
            $.each(data, function (index, type) {
                $('#modal_business_type').append(`<option value="${type.id}">${type.business_name}</option>`);
            });
        },
        error: function () {
            $('#modal_business_type').html('<option value="">Unable to load types</option>');
        }
    });


function submitProspect(e) {
    e.preventDefault();

    let newProspectusName = $('#modalnewProspectusName').val();
    let contact_person = $('#modal_contact_person').val();
    let contact_number = $('#modal_contact_number').val();
    let address = $('#modal_address').val();
    let state = $('#modal_state').val();
    let city = $('#modal_city').val();
    let email = $('#modal_email').val();
    let business_type = $('#modal_business_type').val();

    $.ajax({
        url: '/prospectus',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            prospectus_name: newProspectusName,
            contact_person: contact_person,
            contact_number: contact_number,
            address: address,
            state_id: state,
            city_id: city,
            email: email,
            business_type_id: business_type
        },
        success: function(response) {
            $('#addProspectusModal').modal('hide');
            $('#addProspectusForm')[0].reset();
            showAlert('success', 'Prospect added successfully.');
            prospect();
        },
        error: function(xhr) {
            alert('Something went wrong!');
            console.log(xhr.responseText);
        }
    });
}

//prospect change
    $(document).ready(function () {
        $('#prospectus').on('change', function () {
            let id = $(this).val();

            if (id !== "") {
                // Optional: Show the ID
                // alert("Selected Prospectus ID: " + id);

                $.ajax({
                    url: '/fillprospectus/' + id, // Laravel route
                    type: 'GET',
                    success: function (data) {
                        // Do something with the response
                        // console.log(data);
                          $('#contactPerson').val(data.contact_person);
                          $('#contactNumber').val(data.contact_number);
                          $('#address').val(data.address);
                          $('#leadsName').val(data.prospectus_name);
                          $('#email').val(data.email);
                          $('#business_type').val(data.business_type_id);
                          $('#state').val(data.state_id);
                          $('#city').val(data.city_id);
                    },
                    error: function (xhr) {
                        alert("Something went wrong!");
                        console.log(xhr.responseText);
                    }
                });
            }
        });
    });

 function submitLead(e) {
  e.preventDefault();

  let prospectus_id = $('#prospectus').val();
  let leads_name = $('#leadsName').val();
  let contact_person = $('#contactPerson').val();
  let contact_number = $('#contactNumber').val();
  let status_id = $('#sales_status').val();
  let address = $('#address').val();
  let state_id = $('#state').val();
  let city_id = $('#city').val();
  let email = $('#email').val();
  let next_follow_up_date = $('#next_follow_up').val();
  let business_type_id = $('#business_type').val();
  let remark = $('#remark').val();
  let lead_source_id = $('#lead_source').val();
  let products_id = $('#product_type').val();

  $.ajax({
    url: '/savelead',
    method: 'POST',
    data: {
      _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token
      prospectus_id,
      leads_name,
      contact_person,
      contact_number,
      status_id,
      address,
      state_id,
      city_id,
      email,
      next_follow_up_date,
      business_type_id,
      remark,
      lead_source_id,
      products_id
    },
    success: function (response) {
      alert('Sales record submitted successfully!');
      console.log(response);
      // Optionally clear the form
      $('#leadForm')[0].reset();
    },
    error: function (xhr) {
      console.error(xhr.responseText);
      alert('Failed to submit sales record!');
    }
  });
}


</script>
@endpush
