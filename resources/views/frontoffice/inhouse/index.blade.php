@extends('frontoffice.layouts.app')

@section('title', 'In-House Guests - Hotel Sagar Sonnet CRM / PMS')

@section('content')
<main class="pms-main-viewport">
  <div class="pms-page-container">
    <div class="pms-page-header">
      <h2><i class="fa-solid fa-users" style="color: var(--accent-primary);"></i> In-House Guests Directory</h2>
      <div style="display: flex; gap: 8px; align-items: center;">
        <input type="text" class="pms-input" id="inhouse-search-input" style="width: 220px; border-radius: var(--radius-md);" placeholder="Search guest, room or phone..." onkeyup="filterInhouseTable()">
        <a href="{{ route('frontoffice.dashboard') }}" class="btn-ui-primary"><i class="fa-solid fa-table-cells"></i> View Room Rack Grid</a>
      </div>
    </div>
    <div class="pms-page-content">
      <div class="pms-table-box">
        <table class="pms-table" id="inhouse-table">
          <thead>
            <tr>
              <th>Room</th>
              <th>Type</th>
              <th>Guest Full Name</th>
              <th>Contact</th>
              <th>Check-In</th>
              <th>Exp. Departure</th>
              <th>Pax</th>
              <th>Status</th>
              <th>Folio Balance</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($inhouseGuests as $g)
              <tr>
                <td><strong style="font-family: var(--font-mono);">{{ $g['room'] }}</strong></td>
                <td>{{ $g['type'] }}</td>
                <td><strong>{{ $g['name'] }}</strong></td>
                <td style="font-family: var(--font-mono); font-size: 11px;">{{ $g['phone'] }}</td>
                <td>{{ $g['checkin'] }}</td>
                <td>{{ $g['departure'] }}</td>
                <td>{{ $g['pax'] }}</td>
                <td><span class="badge-tag {{ $g['status_class'] }}">{{ $g['status'] }}</span></td>
                <td style="color: var(--accent-rose); font-weight: 800; font-family: var(--font-mono);">₹ {{ $g['balance'] }}</td>
                <td>
                  <button type="button" class="btn-ui-secondary" onclick="openRoomDetails('{{ $g['room'] }}', '{{ $g['type'] }}', 'Cleaned', 'Occupied', 3500, {name: '{{ $g['name'] }}', state: '{{ $g['status'] }}', folio: '{{ $g['folio'] }}', balance: {{ str_replace(',', '', $g['balance']) }} })">
                    <i class="fa-solid fa-file-lines"></i> Folio
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="10" style="text-align: center; color: var(--text-muted); padding: 24px;">No residing in-house guests found.</td>
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
    <i class="fa-solid fa-bed" style="color: var(--accent-rose);"></i> Total Inhouse Guests: {{ count($inhouseGuests) }} Rooms Occupied • 18 Total Pax
  </div>
</footer>
@endsection

@push('scripts')
<script>
function filterInhouseTable() {
  const query = (document.getElementById('inhouse-search-input').value || '').toLowerCase();
  const rows = document.querySelectorAll('#inhouse-table tbody tr');
  rows.forEach(r => {
    const text = r.innerText.toLowerCase();
    r.style.display = text.includes(query) ? '' : 'none';
  });
}
</script>
@endpush
