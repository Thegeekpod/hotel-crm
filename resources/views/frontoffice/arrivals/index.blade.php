@extends('frontoffice.layouts.app')

@section('title', 'Expected Arrivals - Hotel Sagar Sonnet CRM / PMS')

@section('content')
<main class="pms-main-viewport">
  <div class="pms-page-container">
    <div class="pms-page-header">
      <h2><i class="fa-solid fa-plane-arrival" style="color: #f43f5e;"></i> Expected Arrivals & Advance Bookings</h2>
      <a href="{{ route('frontoffice.dashboard') }}" class="btn-ui-primary"><i class="fa-solid fa-table-cells"></i> View Room Rack</a>
    </div>
    <div class="pms-page-content">
      <div class="pms-table-box">
        <table class="pms-table">
          <thead>
            <tr>
              <th>Booking Ref</th>
              <th>Guest Name</th>
              <th>Category</th>
              <th>Allotted Room</th>
              <th>Stay Dates</th>
              <th>Pax</th>
              <th>Advance Paid</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($arrivals as $arr)
              <tr>
                <td><strong style="font-family: var(--font-mono); color: var(--accent-cyan);">{{ $arr['ref'] }}</strong></td>
                <td><strong>{{ $arr['guest_name'] }}</strong></td>
                <td>{{ $arr['category'] }}</td>
                <td><strong style="font-family: var(--font-mono);">Room {{ $arr['room'] }}</strong></td>
                <td>{{ $arr['stay_dates'] }}</td>
                <td>{{ $arr['pax'] }}</td>
                <td style="font-family: var(--font-mono); font-weight: 700;">{{ $arr['advance'] }}</td>
                <td><span class="badge-tag {{ $arr['status_class'] }}">{{ $arr['status'] }}</span></td>
                <td>
                  @if($arr['status'] === 'Confirmed')
                    <button type="button" class="btn-ui-success" onclick="if(typeof PmsAlert !== 'undefined') PmsAlert.toast('Guest {{ $arr['guest_name'] }} checked into Room {{ $arr['room'] }}!', 'success'); else alert('Guest {{ $arr['guest_name'] }} checked into Room {{ $arr['room'] }}!')">
                      <i class="fa-solid fa-key"></i> Express Check-In
                    </button>
                  @else
                    <button type="button" class="btn-ui-secondary" onclick="if(typeof PmsAlert !== 'undefined') PmsAlert.toast('Booking {{ $arr['ref'] }} is scheduled for tomorrow.', 'info'); else alert('Booking is scheduled for tomorrow.');">
                      <i class="fa-solid fa-eye"></i> View Booking
                    </button>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" style="text-align: center; color: var(--text-muted); padding: 24px;">No expected arrivals found for today.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>

<footer class="pms-bottom-bar">
  <div style="font-size: 11px; font-weight: 700; color: var(--text-secondary); display: flex; align-items: center; gap: 8px;">
    <i class="fa-solid fa-calendar-check" style="color: var(--accent-emerald);"></i> Today Expected: {{ count($arrivals) }} | Tomorrow Expected: 2
  </div>
</footer>
@endsection
