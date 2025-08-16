@extends('layouts.app')

@section('title', 'Sales Product')
@section('page_title', 'Sales Product')

@section('content')
<div class="container mt-4">
  <div class="row g-4">

    <!-- Sales Summary Card -->
  <div class="col-md-6 col-lg-4">
  <div class="card shadow rounded-4 border-0">
    <div class="card-body squareCard d-flex justify-content-between align-items-center">
      <div>
        <h6 class="card-title text-light mb-1">
          <i class="bi bi-bar-chart-line me-1"></i>Today's Follow Ups
        </h6>
        <a href="{{route('todayfollowupstable')}}" class="btn btn-warning btn-sm mt-1">View Details</a>
      </div>
      <div class="display-6 fw-bold text-light" id="todayfollowups">
        0
      </div>
    </div>
  </div>
</div>


    <div class="col-md-6 col-lg-4">
  <div class="card shadow rounded-4 border-0">
    <div class="card-body squareCard d-flex justify-content-between align-items-center">
      <div>
        <h6 class="card-title text-light mb-1">
          <i class="bi bi-bar-chart-line me-1"></i>Underprocess
        </h6>
        <a href="{{route('underprocesstable')}}" class="btn btn-warning btn-sm mt-1">View Details</a>
      </div>
      <div class="display-6 fw-bold text-light" id="underprocess">
        0
      </div>
    </div>
  </div>
</div>


   <div class="col-md-6 col-lg-4">
  <div class="card shadow rounded-4 border-0">
    <div class="card-body squareCard d-flex justify-content-between align-items-center">
      <div>
        <h6 class="card-title text-light mb-1">
          <i class="bi bi-bar-chart-line me-1"></i>Today Completed
        </h6>
        <a href="{{route('todaycompletedtable')}}" class="btn btn-warning btn-sm mt-1">View Details</a>
      </div>
      <div class="display-6 fw-bold text-light" id="todaycompleted">
      0
      </div>
    </div>
  </div>
</div>


     <div class="col-md-6 col-lg-4">
  <div class="card shadow rounded-4 border-0">
    <div class="card-body squareCard d-flex justify-content-between align-items-center">
      <div>
        <h6 class="card-title text-light mb-1">
          <i class="bi bi-bar-chart-line me-1"></i>Today Pending
        </h6>
        <a href="{{route('todaypendingtable')}}" class="btn btn-warning btn-sm mt-1">View Details</a>
      </div>
      <div class="display-6 fw-bold text-light" id="todaypending">
      0 
      </div>
    </div>
  </div>
</div>

    <div class="col-md-6 col-lg-4">
  <div class="card shadow rounded-4 border-0">
    <div class="card-body squareCard d-flex justify-content-between align-items-center">
      <div>
        <h6 class="card-title text-light mb-1">
          <i class="bi bi-bar-chart-line me-1"></i>Today New
        </h6>
        <a href="{{route('todaynewtable')}}" class="btn btn-warning btn-sm mt-1">View Details</a>
      </div>
      <div class="display-6 fw-bold text-light" id="todaynew">
        0
      </div>
    </div>
  </div>
</div>

     <!-- <div class="col-md-6 col-lg-4">
  <div class="card shadow rounded-4 border-0">
    <div class="card-body squareCard d-flex justify-content-between align-items-center">
      <div>
        <h6 class="card-title text-light mb-1">
          <i class="bi bi-bar-chart-line me-1"></i> Total Ticket
        </h6>
        <a href="#" class="btn btn-warning btn-sm mt-1">View Details</a>
      </div>
      <div class="display-6 fw-bold text-light" id="estimateticket">
        0
      </div>
    </div>
  </div>
</div> -->

 <div class="col-md-6 col-lg-4">
  <div class="card shadow rounded-4 border-0">
    <div class="card-body squareCard d-flex justify-content-between align-items-center">
      <div>
        <h6 class="card-title text-light mb-1">
          <i class="bi bi-bar-chart-line me-1"></i>  My All Leads
        </h6>
        <a href="{{route('followup')}}" class="btn btn-warning btn-sm mt-1">View Details</a>
      </div>
      <div class="display-6 fw-bold text-light" id="allleads">
        0
      </div>
    </div>
  </div>
</div>



    <!-- Charts Section -->
  <div class="row g-4">
    <!-- Pie Chart -->
    <div class="col-md-6">
      <div class="card shadow rounded-4 border-0 bg-transparent">
        <div class="card-body">
          <h6 class="card-title">Lead Distribution</h6>
          <div class="d-flex justify-content-center">
            <canvas id="pieChart" width="180" height="180" style="max-width: 100%;"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Bar Chart -->
    <div class="col-md-6">
      <div class="card shadow rounded-4 border-0 bg-transparent">
        <div class="card-body">
          <h6 class="card-title">Monthly Leads</h6>
          <canvas id="barChart" height="127"></canvas>
        </div>
      </div>
    </div>
  </div>
  
  </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
  .card-title i {
    margin-right: 8px;
  }
  .squareCard{
    background: linear-gradient(to right, #6a11cb, #2575fc);
     
  }
</style>
@endpush

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  // Pie Chart
$(document).ready(function () {
  $.ajax({
    url: "/piedata",
    method: "GET",
    success: function (data) {
      const labels = data.map(item => item.status_name);
      const values = data.map(item => item.count);

      const backgroundColors = [
        '#198754', '#0d6efd', '#ffc107', '#dc3545', '#6c757d', '#6610f2', '#fd7e14'
      ];

      const ctx = document.getElementById('pieChart').getContext('2d');
      new Chart(ctx, {
        type: 'pie',
        data: {
          labels: labels,
          datasets: [{
            data: values,
            backgroundColor: backgroundColors.slice(0, labels.length),
            borderWidth: 1
          }]
        },
        options: {
          responsive: false,
          plugins: {
            legend: {
              position: 'bottom'
            }
          }
        }
      });
    },
    error: function (xhr, status, error) {
      console.error("Error loading pie chart data:", error);
    }
  });
});

  // Bar Chart
 $(document).ready(function () {
  $.ajax({
    url: "/bardata",
    method: "GET",
    success: function (res) {
      const barCtx = document.getElementById('barChart').getContext('2d');
      new Chart(barCtx, {
        type: 'bar',
        data: {
          labels: res.labels,
          datasets: [{
            label: 'Leads',
            data: res.data,
            backgroundColor: '#0d6efd'
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true
            }
          },
          plugins: {
            legend: {
              display: false
            }
          }
        }
      });
    },
    error: function (err) {
      console.error("Bar chart load error:", err);
    }
  });
});
</script>

<!-- for counts -->



<script>
   $(document).ready(function () {
    $.ajax({
      url: '/allleads',
      method: 'GET',
      success: function (response) {
        console.log(response);
        $('#allleads').text(response.allleads);
      },
      error: function () {
        $('#allleads').text('Error');
      }
    });
  });

  $(document).ready(function () {
    $.ajax({
      url: '/todayfollowups',
      method: 'GET',
      success: function (response) {
        console.log(response);
        $('#todayfollowups').text(response.totalLeads);
      },
      error: function () {
        $('#todayfollowups').text('Error');
      }
    });
  });

   $(document).ready(function () {
    $.ajax({
      url: '/underprocess',
      method: 'GET',
      success: function (response) {
        console.log(response);
        $('#underprocess').text(response.underprocess);
      },
      error: function () {
        $('#underprocess').text('Error');
      }
    });
  });

   $(document).ready(function () {
    $.ajax({
      url: '/todaycompleted',
      method: 'GET',
      success: function (response) {
        console.log(response);
        $('#todaycompleted').text(response.todaycompleted);
      },
      error: function () {
        $('#todaycompleted').text('Error');
      }
    });
  });


   $(document).ready(function () {
    $.ajax({
      url: '/todaypending',
      method: 'GET',
      success: function (response) {
        console.log(response);
        $('#todaypending').text(response.todaypending);
      },
      error: function () {
        $('#todaypending').text('Error');
      }
    });
  });

  $(document).ready(function () {
    $.ajax({
      url: '/todaynew',
      method: 'GET',
      success: function (response) {
        console.log(response);
        $('#todaynew').text(response.todaynew);
      },
      error: function () {
        $('#todaynew').text('Error');
      }
    });
  });

  $(document).ready(function () {
    $.ajax({
      url: '/estimateticket',
      method: 'GET',
      success: function (response) {
        console.log(response);
        $('#estimateticket').text(response.estimateticket);
      },
      error: function () {
        $('#estimateticket').text('Error');
      }
    });
  });
  
  
</script>
@endpush
