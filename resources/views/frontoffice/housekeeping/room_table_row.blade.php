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

<tr class="border-b border-slate-100 hover:bg-slate-50/80 transition-colors text-xs text-slate-700" id="roomRow-{{ $room->id }}">
  <td class="p-3 text-center">
    <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer hk-check-box room-select-box" value="{{ $room->id }}" onchange="onRoomCheckboxChange()">
  </td>
  <td class="p-3 font-mono font-black text-slate-900 text-sm">
    Room {{ $room->room_number }}
  </td>
  <td class="p-3 font-medium text-slate-600">Floor {{ $room->floorRelation?->floor ?? '1' }}</td>
  <td class="p-3 font-semibold text-slate-800">{{ $room->categoryRelation?->name ?? 'Standard' }}</td>
  <td class="p-3">
    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase border hk-badge {{ $isOccupied ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
      {{ $isOccupied ? 'Occupied' : 'Vacant' }}
    </span>
    @if($isOccupied && $room->currentBooking?->guest)
      <div class="text-[11px] text-slate-500 font-medium mt-0.5">{{ $room->currentBooking->guest->guest_name }}</div>
    @endif
  </td>
  <td class="p-3">
    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase border hk-badge {{ $isClean ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($isDirty ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-cyan-50 text-cyan-700 border-cyan-200') }}">
      {{ $hkStatus }}
    </span>
  </td>
  <td class="p-3">
    <span class="capitalize flex items-center gap-1 font-bold text-xs {{ $taskStatus === 'complete' ? 'text-emerald-700' : 'text-amber-700' }}">
      <i class="fa-solid {{ $taskStatus === 'complete' ? 'fa-check text-emerald-500' : 'fa-clock text-amber-500' }}"></i>
      {{ $taskStatus }}
    </span>
  </td>
  <td class="p-3 text-[11px] text-slate-400 font-mono">
    {{ $latestHk?->completion_time ? \Carbon\Carbon::parse($latestHk->completion_time)->format('h:i A') : ($latestHk?->created_at ? $latestHk->created_at->diffForHumans() : 'Recently') }}
  </td>
  <td class="p-3 text-right">
    <div class="inline-flex items-center gap-1.5 justify-end">
      @if($isCompleteSection === true || $isStateCompletedType)
        <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-lg border border-amber-200 bg-amber-50 hover:bg-amber-100 text-amber-800 transition-all hk-btn-quick btn-mark-dirty" onclick="quickUpdateStatus({{ $room->id }}, {{ $dirtyStateId }}, 'Dirty')">
          <i class="fa-solid fa-rotate-left text-amber-600"></i> To Tasks
        </button>
      @else
        <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-lg border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 transition-all hk-btn-quick btn-mark-clean" onclick="quickUpdateStatus({{ $room->id }}, {{ $cleanStateId }}, 'Clean')">
          <i class="fa-solid fa-check text-emerald-600"></i> Complete
        </button>
      @endif

      <button type="button" class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 flex items-center justify-center text-xs transition-all hk-btn-quick" onclick="openRoomStatusModal({{ $room->id }}, '{{ $room->room_number }}', {{ $latestHk?->housekeeping_status_id ?? $cleanStateId }}, '{{ $taskStatus }}')">
        <i class="fa-solid fa-ellipsis"></i>
      </button>

      <button type="button" class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 flex items-center justify-center text-xs transition-all hk-btn-history" onclick="viewRoomHistory({{ $room->id }}, '{{ $room->room_number }}')">
        <i class="fa-solid fa-clock-rotate-left"></i>
      </button>
    </div>
  </td>
</tr>
