@php
  $latestHk = $room->latestHousekeepingHistory;
  $hkStatus = $latestHk?->housekeepingStatus?->name ?? 'Clean';
  $hkStatusLower = strtolower($hkStatus);
  $taskStatus = strtolower($latestHk?->status ?? 'pending');
  
  $isClean = (bool)preg_match('/clean|sanit|ready|inspected/i', $hkStatusLower);
  $isDirty = (bool)preg_match('/dirty|due|cleaning/i', $hkStatusLower);
  $isInspect = (bool)preg_match('/inspect|touch/i', $hkStatusLower);
  
  $cardStateClass = 'border-slate-200';
  $hkBadgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
  if ($isDirty) {
      $cardStateClass = 'border-amber-300 bg-amber-50/20';
      $hkBadgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
  } elseif ($isInspect) {
      $cardStateClass = 'border-cyan-300 bg-cyan-50/20';
      $hkBadgeClass = 'bg-cyan-50 text-cyan-700 border-cyan-200';
  }

  $opStatus = $room->latestOperationalHistory?->operationalStatus?->name ?? 'Active';
  $isMaintenance = (bool)preg_match('/maintenance|block|order/i', strtolower($opStatus));
  if ($isMaintenance) {
      $cardStateClass = 'border-rose-300 bg-rose-50/20';
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

<div class="bg-white border rounded-2xl p-4 shadow-xs hover:shadow-md transition-all flex flex-col justify-between gap-3 hk-room-card {{ $cardStateClass }}" id="roomCard-{{ $room->id }}">
  <div class="flex items-start justify-between gap-2 hk-room-header">
    <div class="flex items-center gap-2.5 hk-room-title-box">
      <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer hk-check-box room-select-box" value="{{ $room->id }}" onchange="onRoomCheckboxChange()">
      <div>
        <div class="text-base font-black text-slate-900 font-mono leading-tight hk-room-number">Room {{ $room->room_number }}</div>
        <div class="text-[11px] font-bold text-slate-500 hk-room-cat">{{ $room->categoryRelation?->name ?? 'Standard' }} &bull; Floor {{ $room->floorRelation?->floor ?? '1' }}</div>
      </div>
    </div>
    <div class="flex flex-col items-end gap-1 hk-badges-col">
      <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase border hk-badge {{ $hkBadgeClass }}">
        <i class="fa-solid {{ $isClean ? 'fa-circle-check text-emerald-500' : ($isDirty ? 'fa-broom text-amber-500' : 'fa-clipboard-check text-cyan-500') }}"></i>
        {{ $hkStatus }}
      </span>
      <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase border hk-badge {{ $isOccupied ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
        <i class="fa-solid {{ $isOccupied ? 'fa-user-check text-rose-500' : 'fa-door-open text-slate-400' }}"></i>
        {{ $isOccupied ? 'Occupied' : 'Vacant' }}
      </span>
      @if($isMaintenance)
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase border bg-purple-50 text-purple-700 border-purple-200 hk-badge">
          <i class="fa-solid fa-wrench text-purple-500"></i> {{ $opStatus }}
        </span>
      @endif
    </div>
  </div>

  <div class="bg-slate-50/80 rounded-xl p-2.5 flex flex-col gap-1 text-xs hk-room-body">
    <div class="flex justify-between items-center text-slate-600 hk-detail-row">
      <span class="text-[11px] font-bold">Occupant:</span>
      <strong class="text-slate-900 font-bold truncate max-w-[140px]">{{ $isOccupied ? trim($guestTitle . ' ' . $guestName) : 'Vacant' }}</strong>
    </div>
    <div class="flex justify-between items-center text-slate-600 hk-detail-row">
      <span class="text-[11px] font-bold">Bedding:</span>
      <strong class="text-slate-800 font-semibold text-[11px]">{{ $room->beddingConfigRelation?->name ?? 'Standard Bed' }}</strong>
    </div>
    <div class="flex justify-between items-center text-slate-600 hk-detail-row">
      <span class="text-[11px] font-bold">Task Status:</span>
      <strong class="capitalize flex items-center gap-1 text-[11px] font-bold {{ $taskStatus === 'complete' ? 'text-emerald-700' : 'text-amber-700' }}">
        <i class="fa-solid {{ $taskStatus === 'complete' ? 'fa-check text-emerald-500' : 'fa-clock text-amber-500' }}"></i>
        {{ $taskStatus }}
      </strong>
    </div>
    <div class="flex justify-between items-center text-slate-600 hk-detail-row">
      <span class="text-[11px] font-bold">Last Updated:</span>
      <strong class="text-slate-400 font-semibold text-[10px]">{{ $lastUpdated }}</strong>
    </div>
  </div>

  <div class="flex items-center justify-between gap-1.5 pt-2 border-t border-slate-100 hk-room-actions">
    @if($isCompleteSection === true || $isStateCompletedType)
      <button type="button" class="flex-1 inline-flex items-center justify-center gap-1.5 px-2.5 py-1.5 text-xs font-bold rounded-lg border border-amber-200 bg-amber-50 hover:bg-amber-100 text-amber-800 transition-all hk-btn-quick btn-mark-dirty" onclick="quickUpdateStatus({{ $room->id }}, {{ $dirtyStateId }}, 'Dirty')">
        <i class="fa-solid fa-rotate-left text-amber-600"></i> <span class="truncate">Mark Dirty</span>
      </button>
    @else
      <button type="button" class="flex-1 inline-flex items-center justify-center gap-1.5 px-2.5 py-1.5 text-xs font-bold rounded-lg border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 transition-all hk-btn-quick btn-mark-clean" onclick="quickUpdateStatus({{ $room->id }}, {{ $cleanStateId }}, 'Clean')">
        <i class="fa-solid fa-check-double text-emerald-600"></i> <span class="truncate">Mark Clean</span>
      </button>
    @endif

    <button type="button" class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 text-xs font-bold rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 transition-all hk-btn-quick" onclick="openRoomStatusModal({{ $room->id }}, '{{ $room->room_number }}', {{ $latestHk?->housekeeping_status_id ?? $cleanStateId }}, '{{ $taskStatus }}')" title="Change Status">
      <i class="fa-solid fa-pen-to-square text-indigo-500"></i>
    </button>

    <button type="button" class="w-8 h-8 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 flex items-center justify-center text-xs transition-all hk-btn-history" title="Inspection & Cleaning History" onclick="viewRoomHistory({{ $room->id }}, '{{ $room->room_number }}')">
      <i class="fa-solid fa-clock-rotate-left text-cyan-600"></i>
    </button>
  </div>
</div>
