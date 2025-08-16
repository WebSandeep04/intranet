@extends('layouts.app')

@section('title', 'Sales Product')
@section('page_title', 'Sales Product')

@section('content')
<div class="container mt-3">
  <div class="row g-3 align-items-stretch">
    <!-- Lead Details -->
    <div class="col-md-3 d-flex">
      <div class="card shadow-sm w-100 h-100">
        <div class="card-header text-white py-2 px-3" style="background: linear-gradient(to right, #6a11cb, #2575fc);">
          <strong>Lead Details</strong>
        </div>
        @if($record)
        @php $first = $record; @endphp
        <div class="card-body p-3 small text-light" style="background: linear-gradient(to right, #6a11cb, #2575fc);">
          <p><strong>Lead:</strong> {{ $first->leads_name ?? '--' }}</p>
          <p><strong>Contact Person:</strong> {{ $first->contact_person ?? '--' }}</p>
          <p><strong>Contact No:</strong> {{ $first->contact_number ?? '--' }}</p>
          <p><strong>Email:</strong> {{ $first->email ?? '--' }}</p>
          <p><strong>State:</strong> {{ $first->state->state_name ?? '--' }}</p>
          <p><strong>City:</strong> {{ $first->city->city_name ?? '--' }}</p>
          <p><strong>Product:</strong> {{ $first->product->product_name ?? '--' }}</p>
          <p><strong>Business Type:</strong> {{ $first->businessType->business_name ?? '--' }}</p>
        </div>
        @endif
        <div class="text-center pb-3 mt-auto" style="background: linear-gradient(to right, #6a11cb, #2575fc);">
          <a href="#" class="btn btn-sm btn-warning w-75">Edit</a>
        </div>
      </div>
    </div>

    <!-- Remark Form -->
    <div class="col-md-4 d-flex">
      <div class="card shadow-sm w-100 h-100">
        <div class="card-header text-white py-2 px-3" style="background: linear-gradient(to right, #6a11cb, #2575fc);">
          <strong>Add/Edit Remark</strong>
        </div>
        <div class="card-body p-3 d-flex flex-column text-white" style="background: linear-gradient(to right, #6a11cb, #2575fc);">
          <form id="remarkForm" class="flex-grow-1 d-flex flex-column">
            <input type="hidden" name="sales_record_id" id="sales_record_id" value="{{ $record->id }}">
            <input type="hidden" name="remark_id" id="remark_id" value="">

            <div class="mb-2">
              <label class="form-label">Date</label>
              <input type="text" name="remark_date" id="remark_date" class="form-control form-control-sm" placeholder="dd/mm/yyyy">
            </div>

            <div class="mb-2">
              <label class="form-label">Remark</label>
              <textarea name="remark" id="remark" class="form-control form-control-sm" rows="3" required></textarea>
            </div>

            <div class="mb-2">
              <label class="form-label">Estimated Ticket Value</label>
              <input type="text" name="ticket_value" id="ticket_value" class="form-control form-control-sm" value="{{ $first->ticket_value }}" placeholder="Enter value">
            </div>

            <div class="mb-2">
              <label class="form-label">Next Follow-Up Date</label>
              <input type="text" name="next_follow_up_date" id="next_follow_up_date" class="form-control form-control-sm" placeholder="dd/mm/yyyy"
                     value="{{ isset($first->next_follow_up_date) ? \Carbon\Carbon::parse($first->next_follow_up_date)->format('d/m/Y') : '' }}">
            </div>

            <div class="mb-3">
              <label class="form-label">Status</label>
              <select name="sales_status" id="sales_status" class="form-select form-select-sm">
                <option value="">Select Status</option>
                <!-- AJAX options -->
              </select>
            </div>

            <button type="submit" onclick="submitRemark(event)" class="btn btn-warning btn-sm w-100 mt-auto">Submit Remark</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Previous Remarks -->
    <div class="col-md-5 d-flex">
      <div class="card shadow-sm w-100 h-100">
        <div class="card-header text-white py-2 px-3" style="background: linear-gradient(to right, #6a11cb, #2575fc);">
          <strong>Previous Remarks</strong>
        </div>
       <div class="card-body p-3 overflow-auto" style="max-height: 500px; background: linear-gradient(to right, #6a11cb, #2575fc);" id="remarkList">

          <ul class="list-group small" id="remarkList">
            @php $allRemarks = $record->remarks; @endphp

            @forelse ($allRemarks as $remark)
              <li class="list-group-item d-flex justify-content-between align-items-start py-2 px-3">
                <div>
                  <strong>{{ \Carbon\Carbon::parse($remark->remark_date)->format('d/m/Y') }}:</strong>
                  <div>{{ \Illuminate\Support\Str::limit($remark->remark, 100) }}</div>
                </div>
                <button class="btn btn-sm btn-warning ms-2"
                        onclick="editRemark('{{ $remark->id }}', '{{ $remark->remark_date }}', `{{ addslashes($remark->remark) }}`)">
                  Edit
                </button>
              </li>
            @empty
              <li class="list-group-item text-muted">No remarks found.</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  /* Targeting the scroll container */
  #remarkList {
    max-height: 450px;
    overflow-y: auto;
  }

  /* Scrollbar styling for Webkit browsers (Chrome, Edge, Safari) */
  #remarkList::-webkit-scrollbar {
    width: 8px;
  }

  #remarkList::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1); 
    border-radius: 4px;
  }

  #remarkList::-webkit-scrollbar-thumb {
    background-color: rgba(255, 255, 255, 0.4);
    border-radius: 4px;
  }

  /* Firefox Scrollbar */
  #remarkList {
    scrollbar-width: thin;
    scrollbar-color: rgba(255, 255, 255, 0.4) rgba(255, 255, 255, 0.1);
  }
</style>
@endpush




@push('scripts')
<script>
   function editRemark(id, date, remark) {
    console.log("Edit Remark Clicked:");
    console.log("ID:", id);
    console.log("Original Date:", date);
    console.log("Remark Text:", remark);

    var formattedDate = formatDateToJNY(date);
    console.log("Formatted Date:", formattedDate);

    document.getElementById('remark_id').value = id;
    document.getElementById('remark_date').value = formattedDate;
    document.getElementById('remark').value = remark;

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

    function formatDateToJNY(date) {
        var dateParts = date.split('-'); 
        return dateParts[2] + '/' + dateParts[1] + '/' + dateParts[0]; 
    }


    // get status

           $(document).ready(function () {
        const selectedStatusName = "{{ $first->status->status_name }}"; // e.g., "Cold"

        $.ajax({
            url: "{{ route('getStatuses') }}",
            type: 'GET',
            success: function (data) {
                $('#sales_status').empty().append('<option value="">Select Status</option>');

                $.each(data, function (key, status) {
                    const selected = status.status_name === selectedStatusName ? 'selected' : '';
                    $('#sales_status').append(`<option value="${status.id}" ${selected}>${status.status_name}</option>`);
                });
            },
            error: function () {
                alert('Failed to load sales statuses.');
            }
        });
    });

    // convert date
    document.addEventListener('DOMContentLoaded', function () {
    const dateInput = document.getElementById('remark_date');

    // If input is empty, set to today's date
    if (!dateInput.value) {
        const today = new Date();
        const dd = String(today.getDate()).padStart(2, '0');
        const mm = String(today.getMonth() + 1).padStart(2, '0'); // Months are zero-based
        const yyyy = today.getFullYear();
        dateInput.value = `${dd}/${mm}/${yyyy}`;
    }
});


// submit remark

function submitRemark(e) {
  e.preventDefault();

let remark_id = $('#remark_id').val();
let remark_date = $('#remark_date').val();
let remark = $('#remark').val();
let ticket_value = $('#ticket_value').val();
let next_follow_up_date = $('#next_follow_up_date').val();
let sales_status = $('#sales_status').val();
let sales_record_id = $('#sales_record_id').val();

 console.log("Form Data:");
  console.log("remark_id:", remark_id);
  console.log("remark_date:", remark_date);
  console.log("remark:", remark);
  console.log("ticket_value:", ticket_value);
  console.log("next_follow_up_date:", next_follow_up_date);
  console.log("sales_status:", sales_status);
  console.log("sales_record_id:", sales_record_id);

  $.ajax({
    url: '{{ route("saveremark") }}', 
    method: 'POST',
    data: {
      _token: '{{ csrf_token() }}', 
      remark_id: remark_id,
      sales_record_id :sales_record_id,
      remark_date: remark_date,
      remark: remark,
      ticket_value: ticket_value,
      next_follow_up_date: next_follow_up_date,
      sales_status: sales_status
    },
    success: function(response) {
      console.log("Response:", response);
      alert('Remark submitted successfully!');
      // Optionally reload or reset form
    },
    error: function(xhr) {
      console.error("Error:", xhr.responseText);
      alert('Something went wrong!');
    }
  });
}



</script>
@endpush
