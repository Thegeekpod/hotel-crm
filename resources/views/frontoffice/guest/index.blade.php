@extends('frontoffice.layouts.app')

@section('title', 'Guest CRM & Master - Hotel Sagar Sonnet CRM / PMS')

@section('content')
<main class="pms-main-viewport">
  <div class="pms-page-container">
    <div class="pms-page-header">
      <h2><i class="fa-solid fa-address-card" style="color: #f59e0b;"></i> Guest Master & CRM Database</h2>
      <div style="display: flex; gap: 8px; align-items: center;">
        <input type="text" class="pms-input" id="guest-search-input" style="width: 240px; border-radius: var(--radius-md);" placeholder="Search guest name or ID..." onkeyup="filterGuestTable()">
        <button type="button" class="btn-ui-primary" onclick="filterGuestTable()"><i class="fa-solid fa-search"></i> Search</button>
      </div>
    </div>
    <div class="pms-page-content">
      <div class="pms-table-box">
        <table class="pms-table" id="guest-table">
          <thead>
            <tr>
              <th>Guest ID</th>
              <th>Full Name</th>
              <th>Phone</th>
              <th>Email</th>
              <th>ID / Passport</th>
              <th>Total Stays</th>
              <th>Loyalty Tier</th>
              <th>Last Visit</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($guests as $g)
              <tr>
                <td style="font-family: var(--font-mono); color: var(--accent-cyan); font-weight: 700;">{{ $g['id'] }}</td>
                <td><strong>{{ $g['name'] }}</strong></td>
                <td style="font-family: var(--font-mono); font-size: 11px;">{{ $g['phone'] }}</td>
                <td style="font-size: 11px;">{{ $g['email'] }}</td>
                <td style="font-family: var(--font-mono); font-size: 11px;">{{ $g['id_proof'] }}</td>
                <td style="font-family: var(--font-mono); font-weight: 700;">{{ $g['total_stays'] }}</td>
                <td>
                  <span class="badge-tag" style="{{ $g['tier_style'] }}">
                    <i class="fa-solid {{ $g['tier_icon'] }}" style="margin-right: 4px;"></i>{{ $g['tier'] }}
                  </span>
                </td>
                <td>{{ $g['last_visit'] }}</td>
                <td>
                  <button type="button" class="btn-ui-secondary" onclick="viewGuestProfile('{{ $g['id'] }}', '{{ $g['name'] }}', '{{ $g['phone'] }}', '{{ $g['email'] }}', '{{ $g['tier'] }}', {{ $g['total_stays'] }})">
                    <i class="fa-solid fa-file-lines"></i> Profile
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" style="text-align: center; color: var(--text-muted); padding: 24px;">No guests found in directory.</td>
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
    <i class="fa-solid fa-database" style="color: var(--accent-primary);"></i> Guest Database: {{ count($guests) }} Active Profiles • Loyalty Program Active
  </div>
</footer>
@endsection

@push('scripts')
<script>
function filterGuestTable() {
  const query = (document.getElementById('guest-search-input').value || '').toLowerCase();
  const rows = document.querySelectorAll('#guest-table tbody tr');
  rows.forEach(r => {
    const text = r.innerText.toLowerCase();
    r.style.display = text.includes(query) ? '' : 'none';
  });
}

function viewGuestProfile(id, name, phone, email, tier, stays) {
  if (typeof Swal !== 'undefined') {
    Swal.fire({
      title: `<strong style="font-size: 16px;">${name} (${id})</strong>`,
      html: `
        <div style="text-align: left; font-size: 13px; line-height: 1.8; padding: 10px 0;">
          <div><strong>Loyalty Status:</strong> <span style="color: #d97706; font-weight: 800;">${tier} Tier</span></div>
          <div><strong>Total Stays:</strong> ${stays} bookings recorded</div>
          <div><strong>Phone:</strong> <span style="font-family: monospace;">${phone}</span></div>
          <div><strong>Email:</strong> ${email}</div>
          <hr style="margin: 12px 0; border: none; border-top: 1px solid #e2e8f0;">
          <div style="font-size: 11px; color: #64748b;">Special Preferences: Extra pillows requested, Non-smoking floor preferred.</div>
        </div>
      `,
      icon: 'info',
      confirmButtonColor: '#6366f1',
      confirmButtonText: 'Close Profile'
    });
  } else {
    alert(`Guest: ${name}\nID: ${id}\nTier: ${tier}\nStays: ${stays}\nPhone: ${phone}`);
  }
}
</script>
@endpush
