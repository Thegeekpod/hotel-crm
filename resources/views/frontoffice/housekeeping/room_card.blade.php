@php
  $latestHk = $room->latestHousekeepingHistory;
  $hkStatus = $latestHk?->housekeepingStatus?->name ?? 'Clean';
  $hkStatusLower = strtolower($hkStatus);
  $taskStatus = strtolower($latestHk?->status ?? 'pending');
  
  $isClean = (bool)preg_match('/clean|sanit|ready|inspected/i', $hkStatusLower);
  $isDirty = (bool)preg_match('/dirty|due|cleaning/i', $hkStatusLower);
  $isInspect = (bool)preg_match('/inspect|touch/i', $hkStatusLower);
  
  $cardStateClass = 'state-clean';
  $hkBadgeClass = 'badge-clean';
  if ($isDirty) {
      $cardStateClass = 'state-dirty';
      $hkBadgeClass = 'badge-dirty';
  } elseif ($isInspect) {
      $cardStateClass = 'state-inspect';
      $hkBadgeClass = 'badge-inspect';
  }

  $opStatus = $room->latestOperationalHistory?->operationalStatus?->name ?? 'Active';
  $isMaintenance = (bool)preg_match('/maintenance|block|order/i', strtolower($opStatus));
  if ($isMaintenance) {
      $cardStateClass = 'state-maintenance';
  }

  $isOccupied = !empty($room->currentBooking);
  $guestName = $room->currentBooking?->guest?->guest_name ?? null;
  $guestTitle = $room->currentBooking?->guest?->title?->title ?? '';
  $lastUpdated = $latestHk?->created_at ? $latestHk->created_at->diffForHumans() : 'Recently';

  $cleanStateObj = $housekeepingStates->first(fn($s) => $s->type === 'completed_task') ?? $housekeepingStates->first(fn($s) => preg_match('/clean|ready/i', $s->name)) ?? $housekeepingStates->first();
  $dirtyStateObj = $housekeepingStates->first(fn($s) => $s->type === 'assign_task') ?? $housekeepingStates->first(fn($s) => preg_match('/dirty/i', $s->name)) ?? $housekeepingStates->skip(1)->first();
  $cleanStateId = $cleanStateObj ? $cleanStateObj->id : 1;
  $dirtyStateId = $dirtyStateObj ? $dirtyStateObj->id : 2;
  $isStateCompletedType = ($latestHk?->housekeepingStatus?->type === 'completed_task');
@endphp

<div class="hk-room-card {{ $cardStateClass }}" id="roomCard-{{ $room->id }}">
  <div class="hk-room-header">
    <div class="hk-room-title-box">
      <input type="checkbox" class="hk-check-box room-select-box" value="{{ $room->id }}" onchange="onRoomCheckboxChange()">
      <div>
        <div class="hk-room-number">Room {{ $room->room_number }}</div>
        <div class="hk-room-cat">{{ $room->categoryRelation?->name ?? 'Standard' }} &bull; Floor {{ $room->floorRelation?->floor ?? '1' }}</div>
      </div>
    </div>
    <div class="hk-badges-col">
      <span class="hk-badge {{ $hkBadgeClass }}">
        <i class="fa-solid {{ $isClean ? 'fa-circle-check' : ($isDirty ? 'fa-broom' : 'fa-clipboard-check') }}"></i>
        {{ $hkStatus }}
      </span>
      <span class="hk-badge {{ $isOccupied ? 'badge-occupied' : 'badge-vacant' }}">
        <i class="fa-solid {{ $isOccupied ? 'fa-user-check' : 'fa-door-open' }}"></i>
        {{ $isOccupied ? 'Occupied' : 'Vacant' }}
      </span>
      @if($isMaintenance)
        <span class="hk-badge badge-maintenance">
          <i class="fa-solid fa-wrench"></i> {{ $opStatus }}
        </span>
      @endif
    </div>
  </div>

  <div class="hk-room-body">
    <div class="hk-detail-row">
      <span>Occupant:</span>
      <strong>{{ $isOccupied ? trim($guestTitle . ' ' . $guestName) : 'Vacant' }}</strong>
    </div>
    <div class="hk-detail-row">
      <span>Bedding:</span>
      <strong>{{ $room->beddingConfigRelation?->name ?? 'Standard Bed' }}</strong>
    </div>
    <div class="hk-detail-row">
      <span>Task Status:</span>
      <strong style="text-transform: capitalize; color: {{ $taskStatus === 'complete' ? '#10b981' : '#f59e0b' }};">
        <i class="fa-solid {{ $taskStatus === 'complete' ? 'fa-check' : 'fa-clock' }}"></i>
        {{ $taskStatus }}
      </strong>
    </div>
    <div class="hk-detail-row">
      <span>Last Updated:</span>
      <strong class="text-secondary">{{ $lastUpdated }}</strong>
    </div>
  </div>

  <div class="hk-room-actions">
    @if($isCompleteSection === true || $isStateCompletedType)
      <!-- When in Completed Tasks: Offer Mark as Dirty to instantly move it back to Pending Tasks -->
      <button type="button" class="hk-btn-quick btn-mark-dirty" onclick="quickUpdateStatus({{ $room->id }}, {{ $dirtyStateId }}, 'Dirty')">
        <i class="fa-solid fa-rotate-left" style="color: #f59e0b;"></i> Move to Tasks (Dirty)
      </button>
    @else
      <!-- When in Tasks: Offer 1-click Mark as Clean to instantly complete & move it to Completed Tasks -->
      <button type="button" class="hk-btn-quick btn-mark-clean" onclick="quickUpdateStatus({{ $room->id }}, {{ $cleanStateId }}, 'Clean')">
        <i class="fa-solid fa-check-double" style="color: #10b981;"></i> Mark as Clean (Done)
      </button>
    @endif

    <button type="button" class="hk-btn-quick" onclick="openRoomStatusModal({{ $room->id }}, '{{ $room->room_number }}', {{ $latestHk?->housekeeping_status_id ?? $cleanStateId }}, '{{ $taskStatus }}')">
      <i class="fa-solid fa-pen-to-square"></i> Change
    </button>

    <button type="button" class="hk-btn-history" title="Inspection & Cleaning History" onclick="viewRoomHistory({{ $room->id }}, '{{ $room->room_number }}')">
      <i class="fa-solid fa-clock-rotate-left"></i>
    </button>
  </div>
</div>
