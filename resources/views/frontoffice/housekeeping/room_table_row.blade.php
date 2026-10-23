@php
  $latestHk = $room->latestHousekeepingHistory;
  $hkStatus = $latestHk?->housekeepingStatus?->name ?? 'Clean';
  $hkStatusLower = strtolower($hkStatus);
  $taskStatus = strtolower($latestHk?->status ?? 'pending');
  $isClean = (bool)preg_match('/clean|sanit|ready|inspected/i', $hkStatusLower);
  $isDirty = (bool)preg_match('/dirty|due|cleaning/i', $hkStatusLower);
  $isOccupied = !empty($room->currentBooking);
  $opStatus = $room->latestOperationalHistory?->operationalStatus?->name ?? 'Active';

  $cleanStateObj = $housekeepingStates->first(fn($s) => $s->type === 'completed_task') ?? $housekeepingStates->first(fn($s) => preg_match('/clean|ready/i', $s->name)) ?? $housekeepingStates->first();
  $dirtyStateObj = $housekeepingStates->first(fn($s) => $s->type === 'assign_task') ?? $housekeepingStates->first(fn($s) => preg_match('/dirty/i', $s->name)) ?? $housekeepingStates->skip(1)->first();
  $cleanStateId = $cleanStateObj ? $cleanStateObj->id : 1;
  $dirtyStateId = $dirtyStateObj ? $dirtyStateObj->id : 2;
  $isStateCompletedType = ($latestHk?->housekeepingStatus?->type === 'completed_task');
@endphp

<tr id="roomRow-{{ $room->id }}">
  <td>
    <input type="checkbox" class="hk-check-box room-select-box" value="{{ $room->id }}" onchange="onRoomCheckboxChange()">
  </td>
  <td>
    <strong style="font-size: 13.5px; color: #0f172a;">Room {{ $room->room_number }}</strong>
  </td>
  <td>Floor {{ $room->floorRelation?->floor ?? '1' }}</td>
  <td>{{ $room->categoryRelation?->name ?? 'Standard' }}</td>
  <td>
    <span class="hk-badge {{ $isOccupied ? 'badge-occupied' : 'badge-vacant' }}">
      {{ $isOccupied ? 'Occupied' : 'Vacant' }}
    </span>
    @if($isOccupied && $room->currentBooking?->guest)
      <div style="font-size: 11px; color: #64748b; margin-top: 2px;">{{ $room->currentBooking->guest->guest_name }}</div>
    @endif
  </td>
  <td>
    <span class="hk-badge {{ $isClean ? 'badge-clean' : ($isDirty ? 'badge-dirty' : 'badge-inspect') }}">
      {{ $hkStatus }}
    </span>
  </td>
  <td>
    <span style="font-weight: 700; text-transform: capitalize; color: {{ $taskStatus === 'complete' ? '#10b981' : '#f59e0b' }};">
      <i class="fa-solid {{ $taskStatus === 'complete' ? 'fa-check' : 'fa-clock' }}"></i>
      {{ $taskStatus }}
    </span>
  </td>
  <td style="color: #64748b; font-size: 11px;">
    {{ $latestHk?->completion_time ? \Carbon\Carbon::parse($latestHk->completion_time)->format('h:i A') : ($latestHk?->created_at ? $latestHk->created_at->diffForHumans() : 'Recently') }}
  </td>
  <td class="text-right">
    <div style="display: inline-flex; gap: 4px;">
      @if($isCompleteSection === true || $isStateCompletedType)
        <button type="button" class="hk-btn-quick btn-mark-dirty" style="padding: 4px 10px;" onclick="quickUpdateStatus({{ $room->id }}, {{ $dirtyStateId }}, 'Dirty')">
          <i class="fa-solid fa-rotate-left"></i> To Tasks
        </button>
      @else
        <button type="button" class="hk-btn-quick btn-mark-clean" style="padding: 4px 10px;" onclick="quickUpdateStatus({{ $room->id }}, {{ $cleanStateId }}, 'Clean')">
          <i class="fa-solid fa-check"></i> Complete
        </button>
      @endif

      <button type="button" class="hk-btn-quick" style="padding: 4px 8px;" onclick="openRoomStatusModal({{ $room->id }}, '{{ $room->room_number }}', {{ $latestHk?->housekeeping_status_id ?? $cleanStateId }}, '{{ $taskStatus }}')">
        <i class="fa-solid fa-ellipsis"></i>
      </button>

      <button type="button" class="hk-btn-history" style="width: 26px; height: 26px;" onclick="viewRoomHistory({{ $room->id }}, '{{ $room->room_number }}')">
        <i class="fa-solid fa-clock-rotate-left"></i>
      </button>
    </div>
  </td>
</tr>
