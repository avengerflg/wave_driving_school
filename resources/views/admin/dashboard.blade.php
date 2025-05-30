@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="row">
  <div class="col-lg-8 mb-4 order-0">
    <div class="card shadow-sm rounded">
      <div class="d-flex align-items-end row">
        <div class="col-sm-7">
          <div class="card-body">
            <h5 class="card-title text-primary">Welcome, {{ Auth::user()->name }}! 🎉</h5>
            <p class="mb-4">
              You have <span class="fw-bold">{{ $monthlyRevenue > 0 && $totalRevenue > 0 ? round(($monthlyRevenue / $totalRevenue) * 100, 1) : 0 }}%</span> of this year's revenue this month.
            </p>
            <a href="/" class="btn btn-sm btn-outline-primary">View Profile</a>
          </div>
        </div>
        <div class="col-sm-5 text-center text-sm-left">
          <div class="card-body pb-0 px-0 px-md-4">
            <img
              src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}"
              height="140"
              alt="View Badge User"
              class="img-fluid rounded-circle"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-lg-4 col-md-4 order-1">
    <div class="row">
      <div class="col-lg-6 col-md-12 col-6 mb-4">
        <div class="card shadow-sm rounded">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between">
              <div class="avatar flex-shrink-0">
                <img src="{{ asset('assets/img/icons/unicons/chart-success.png') }}" alt="chart success" class="rounded" />
              </div>
            </div>
            <span class="fw-semibold d-block mb-1">Total Revenue</span>
            <h3 class="card-title mb-2">${{ number_format($totalRevenue, 2) }}</h3>
            <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> This Month: ${{ number_format($monthlyRevenue, 2) }}</small>
          </div>
        </div>
      </div>

      <div class="col-lg-6 col-md-12 col-6 mb-4">
        <div class="card shadow-sm rounded">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between">
              <div class="avatar flex-shrink-0">
                <img src="{{ asset('assets/img/icons/unicons/wallet-info.png') }}" alt="Credit Card" class="rounded" />
              </div>
            </div>
            <span>Bookings</span>
            <h3 class="card-title text-nowrap mb-1">{{ $totalBookings }}</h3>
            <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> Pending: {{ $pendingBookings }}</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Total Revenue Chart -->
  <div class="col-12 col-lg-8 order-2 order-md-3 order-lg-2 mb-4">
    <div class="card shadow-sm rounded">
      <div class="row row-bordered g-0">
        <div class="col-md-8">
          <h5 class="card-header m-0 me-2 pb-3">Monthly Revenue</h5>
          <div class="px-2">
            <canvas id="monthlyRevenueChart"></canvas>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card-body">
            <div class="text-center">
              <div class="dropdown">
                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="growthReportId" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  {{ now()->year }}
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="growthReportId">
                  @for($y = now()->year; $y >= now()->year - 3; $y--)
                    <a class="dropdown-item" href="#">{{ $y }}</a>
                  @endfor
                </div>
              </div>
            </div>
          </div>
          <div class="text-center fw-semibold pt-3 mb-2">
            {{ $totalBookings }} Bookings this year
          </div>
          <div class="d-flex px-xxl-4 px-lg-2 p-4 gap-xxl-3 gap-lg-1 gap-3 justify-content-between">
            <div class="d-flex">
              <div class="me-2">
                <span class="badge bg-label-primary p-2"><i class="bx bx-dollar text-primary"></i></span>
              </div>
              <div class="d-flex flex-column">
                <small>This Month</small>
                <h6 class="mb-0">${{ number_format($monthlyRevenue, 2) }}</h6>
              </div>
            </div>
            <div class="d-flex">
              <div class="me-2">
                <span class="badge bg-label-info p-2"><i class="bx bx-wallet text-info"></i></span>
              </div>
              <div class="d-flex flex-column">
                <small>Total</small>
                <h6 class="mb-0">${{ number_format($totalRevenue, 2) }}</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!--/ Total Revenue Chart -->
  <div class="col-12 col-md-8 col-lg-4 order-3 order-md-2">
    <div class="row">
      <div class="col-6 mb-4">
        <div class="card shadow-sm rounded">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between">
              <div class="avatar flex-shrink-0">
                <img src="{{ asset('assets/img/icons/unicons/paypal.png') }}" alt="Credit Card" class="rounded" />
              </div>
            </div>
            <span class="d-block mb-1">Confirmed</span>
            <h3 class="card-title text-nowrap mb-2">{{ $confirmedBookings }}</h3>
            <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> Completed: {{ $completedBookings }}</small>
          </div>
        </div>
      </div>
      <div class="col-6 mb-4">
        <div class="card shadow-sm rounded">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between">
              <div class="avatar flex-shrink-0">
                <img src="{{ asset('assets/img/icons/unicons/cc-primary.png') }}" alt="Credit Card" class="rounded" />
              </div>
            </div>
            <span class="fw-semibold d-block mb-1">Cancelled</span>
            <h3 class="card-title mb-2">{{ $cancelledBookings }}</h3>
            <small class="text-danger fw-semibold"><i class="bx bx-down-arrow-alt"></i> Pending: {{ $pendingBookings }}</small>
          </div>
        </div>
      </div>

      <div class="col-12 mb-4">
        <div class="card shadow-sm rounded">
          <div class="card-body">
            <div class="d-flex justify-content-between flex-sm-row flex-column gap-3">
              <div class="d-flex flex-sm-column flex-row align-items-start justify-content-between">
                <div class="card-title">
                  <h5 class="text-nowrap mb-2">Top Instructors</h5>
                  <span class="badge bg-label-warning rounded-pill">Top 5</span>
                </div>
                <div class="mt-sm-auto">
                  <ul class="list-group">
                    @foreach($topInstructors as $instructor)
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $instructor->user->name }}
                        <span class="badge bg-primary rounded-pill">{{ $instructor->bookings_count }}</span>
                      </li>
                    @endforeach
                  </ul>
                </div>
              </div>
              <div>
                <h6 class="mb-2">Top Suburbs</h6>
                <ul class="list-group">
                  @foreach($topSuburbs as $suburb)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      {{ $suburb->name }}
                      <span class="badge bg-info rounded-pill">{{ $suburb->bookings_count }}</span>
                    </li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Recent Bookings -->
  <div class="col-12 mb-4">
    <div class="card shadow-sm rounded">
      <div class="card-header">
        <h6>Recent Bookings</h6>
      </div>
      <div class="card-body p-0">
        <ul class="list-group list-group-flush">
          @foreach($recentBookings as $booking)
            <li class="list-group-item">
              <strong>{{ $booking->user->name }}</strong> booked 
              <strong>{{ $booking->service->name }}</strong> with 
              <strong>{{ $booking->instructor->user->name }}</strong>
              ({{ $booking->suburb->name ?? 'N/A' }})<br>
              <small>{{ $booking->created_at->format('d M Y H:i') }}</small>
            </li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>

  <!-- Monthly Bookings Chart -->
  <div class="col-12 mb-4">
    <div class="card shadow-sm rounded">
      <div class="card-header">
        <h6>Monthly Bookings ({{ now()->year }})</h6>
      </div>
      <div class="card-body">
        <canvas id="monthlyBookingsChart"></canvas>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  // Use direct JSON output to avoid parsing errors
  const revenueData = JSON.parse('{!! json_encode($monthlyRevenueChartData ?? [0,0,0,0,0,0,0,0,0,0,0,0]) !!}');
  const bookingsData = JSON.parse('{!! json_encode($monthlyBookingsChartData ?? [0,0,0,0,0,0,0,0,0,0,0,0]) !!}');

  // Monthly Revenue Chart
  const revenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
  new Chart(revenueCtx, {
    type: 'line',
    data: {
      labels: months,
      datasets: [{
        label: 'Revenue',
        data: revenueData,
        borderColor: 'rgba(54, 162, 235, 1)',
        backgroundColor: 'rgba(54, 162, 235, 0.2)',
        fill: true,
        tension: 0.4
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: value => '$' + value
          }
        }
      }
    }
  });

  // Monthly Bookings Chart
  const bookingsCtx = document.getElementById('monthlyBookingsChart').getContext('2d');
  new Chart(bookingsCtx, {
    type: 'bar',
    data: {
      labels: months,
      datasets: [{
        label: 'Bookings',
        data: bookingsData,
        backgroundColor: 'rgba(255, 99, 132, 0.6)'
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
});
</script>
@endpush

@endsection
