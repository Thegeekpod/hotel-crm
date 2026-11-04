@extends('admin.layouts.app')

@section('title', 'User Management & Roles - Hotel Sagar Sonnet PMS')

@section('content')
<main class="flex-1 p-4 lg:p-6 space-y-6">

  <!-- Page Header -->
  <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 ring-4 ring-indigo-50 animate-pulse"></span>
      <div>
        <h1 class="text-base font-extrabold text-slate-900 tracking-tight">System &amp; Employee Access Management</h1>
        <p class="text-xs text-slate-500 font-medium">Create and manage hotel staff accounts and assign specific module permissions.</p>
      </div>
    </div>
    <div class="flex items-center gap-2">
      <button type="button" class="inline-flex items-center gap-1.5 h-9 px-4 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl transition-all shadow-xs cursor-pointer" onclick="openAddUserModal()">
        <i class="fa-solid fa-user-plus"></i> Add New Employee / User
      </button>
    </div>
  </div>

  <!-- Metric Overview Cards -->
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
    <!-- Total Users -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
      <div class="flex items-center justify-between text-slate-400 mb-1.5">
        <span class="text-[10px] font-black uppercase tracking-wider">Total Users</span>
        <i class="fa-solid fa-users text-indigo-500 text-sm"></i>
      </div>
      <div class="text-xl font-black text-slate-800">{{ $totalUsers }}</div>
      <div class="text-[10px] text-slate-400 mt-0.5">Registered accounts</div>
    </div>

    <!-- Active Staff -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
      <div class="flex items-center justify-between text-slate-400 mb-1.5">
        <span class="text-[10px] font-black uppercase tracking-wider">Active Staff</span>
        <i class="fa-solid fa-user-check text-emerald-500 text-sm"></i>
      </div>
      <div class="text-xl font-black text-emerald-600">{{ $activeUsers }}</div>
      <div class="text-[10px] text-emerald-500 font-medium mt-0.5">Able to login</div>
    </div>

    <!-- Front Office -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
      <div class="flex items-center justify-between text-slate-400 mb-1.5">
        <span class="text-[10px] font-black uppercase tracking-wider">Front Office</span>
        <i class="fa-solid fa-desktop text-indigo-500 text-sm"></i>
      </div>
      <div class="text-xl font-black text-indigo-700">{{ $frontOfficeCount }}</div>
      <div class="text-[10px] text-slate-400 mt-0.5">Desk &amp; Folio access</div>
    </div>

    <!-- POS Sales -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
      <div class="flex items-center justify-between text-slate-400 mb-1.5">
        <span class="text-[10px] font-black uppercase tracking-wider">POS Sales</span>
        <i class="fa-solid fa-utensils text-emerald-500 text-sm"></i>
      </div>
      <div class="text-xl font-black text-emerald-700">{{ $posCount }}</div>
      <div class="text-[10px] text-slate-400 mt-0.5">Restaurant dining</div>
    </div>

    <!-- Stores Stock -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
      <div class="flex items-center justify-between text-slate-400 mb-1.5">
        <span class="text-[10px] font-black uppercase tracking-wider">Stores</span>
        <i class="fa-solid fa-boxes-stacked text-amber-500 text-sm"></i>
      </div>
      <div class="text-xl font-black text-amber-700">{{ $storesCount }}</div>
      <div class="text-[10px] text-slate-400 mt-0.5">Inventory &amp; Menus</div>
    </div>

    <!-- Banquet -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
      <div class="flex items-center justify-between text-slate-400 mb-1.5">
        <span class="text-[10px] font-black uppercase tracking-wider">Banquet</span>
        <i class="fa-solid fa-champagne-glasses text-purple-500 text-sm"></i>
      </div>
      <div class="text-xl font-black text-purple-700">{{ $banquetCount }}</div>
      <div class="text-[10px] text-slate-400 mt-0.5">Halls &amp; Event orders</div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-6 items-start">
    @include('admin.includes.sidebar')

    <section class="min-w-0 space-y-4">
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        <!-- Table Header & Controls -->
        <div class="p-5 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div>
            <div class="flex items-center gap-2.5">
              <h2 class="text-sm font-extrabold text-slate-900 tracking-tight">Staff &amp; User Accounts Register</h2>
              <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80" id="users-count">{{ $users->total() }} Employees</span>
            </div>
            <p class="text-xs text-slate-500 font-medium mt-0.5">
              Configure credentials, phone login, and assigned modules (Front Office, POS, Stores, Banquet).
            </p>
          </div>
          <button type="button" class="inline-flex items-center gap-1.5 h-9 px-4 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-xs cursor-pointer shrink-0" onclick="openAddUserModal()">
            <i class="fa-solid fa-plus"></i> Add User
          </button>
        </div>

        <!-- Filter Ribbon Toolbar -->
        <form method="GET" action="{{ route('admin.users.index') }}" class="p-4 bg-slate-50/70 border-b border-slate-200/80 flex flex-wrap items-center justify-between gap-3" id="users-filter-form">
          <div class="flex items-center gap-2.5 flex-1 min-w-[280px]">
            <div class="relative flex-1 max-w-sm">
              <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
              <input type="text" name="search" class="h-9.5 pl-8 pr-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full" placeholder="Search by name, phone or email..." value="{{ request('search') }}">
            </div>

            <!-- Role Filter -->
            <select name="role" class="h-9.5 px-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 cursor-pointer" onchange="document.getElementById('users-filter-form').submit()">
              <option value="">All Roles</option>
              <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
              <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff / Employee</option>
            </select>

            <!-- Section Filter -->
            <select name="section" class="h-9.5 px-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 cursor-pointer" onchange="document.getElementById('users-filter-form').submit()">
              <option value="">All Sections</option>
              <option value="frontoffice" {{ request('section') === 'frontoffice' ? 'selected' : '' }}>Front Office</option>
              <option value="pos" {{ request('section') === 'pos' ? 'selected' : '' }}>POS Sales</option>
              <option value="stores" {{ request('section') === 'stores' ? 'selected' : '' }}>Stores</option>
              <option value="banquet" {{ request('section') === 'banquet' ? 'selected' : '' }}>Banquet</option>
              <option value="accounts" {{ request('section') === 'accounts' ? 'selected' : '' }}>Accounts</option>
            </select>

            <!-- Status Filter -->
            <select name="status" class="h-9.5 px-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 cursor-pointer" onchange="document.getElementById('users-filter-form').submit()">
              <option value="">All Statuses</option>
              <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
              <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>

          <div class="flex items-center gap-2">
            <button type="submit" class="inline-flex items-center gap-1.5 h-9 px-3.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-xs cursor-pointer">
              <i class="fa-solid fa-filter"></i> Apply
            </button>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 h-9 px-3 text-xs font-bold text-slate-600 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl transition-all shadow-xs">
              <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
          </div>
        </form>

        <!-- Users Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs" id="users-table">
            <thead>
              <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                <th class="py-3 px-4 w-12 text-center">#</th>
                <th class="py-3 px-4 min-w-[200px]">Employee / User</th>
                <th class="py-3 px-4 min-w-[130px]">Phone (Login)</th>
                <th class="py-3 px-4 min-w-[120px]">Role</th>
                <th class="py-3 px-4 min-w-[280px]">Assigned Sections (Allowed Views)</th>
                <th class="py-3 px-4 w-28 text-center">Status</th>
                <th class="py-3 px-4 w-32 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              @forelse($users as $index => $u)
                @php
                  $isCurrent = Auth::id() === $u->id;
                  $isAdmin = $u->isAdmin();
                  $sections = $u->assigned_sections ?? [];
                  if ($isAdmin) {
                    $sections = ['frontoffice', 'pos', 'stores', 'banquet', 'accounts', 'admin'];
                  }
                @endphp
                <tr class="hover:bg-indigo-50/20 transition-colors {{ $isCurrent ? 'bg-indigo-50/30' : '' }}" id="user-row-{{ $u->id }}">
                  <td class="py-3 px-4 text-center font-bold text-slate-400">
                    {{ $users->firstItem() + $index }}
                  </td>
                  <td class="py-3 px-4">
                    <div class="flex items-center gap-3">
                      <div class="w-8 h-8 rounded-xl bg-linear-to-br {{ $isAdmin ? 'from-indigo-600 to-violet-600' : 'from-slate-700 to-slate-900' }} text-white font-black text-xs flex items-center justify-center shadow-xs">
                        {{ strtoupper(substr($u->name, 0, 2)) }}
                      </div>
                      <div>
                        <div class="font-bold text-slate-900 flex items-center gap-1.5">
                          {{ $u->name }}
                          @if($isCurrent)
                            <span class="text-[9px] font-black uppercase px-1.5 py-0.2 rounded bg-indigo-100 text-indigo-700 border border-indigo-200">You</span>
                          @endif
                        </div>
                        <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                          {{ $u->email ?? 'No email configured' }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="py-3 px-4 font-mono font-bold text-slate-800">
                    <div class="inline-flex items-center gap-1.5">
                      <i class="fa-solid fa-phone text-slate-400 text-[10px]"></i>
                      <span>{{ $u->phone ?? '—' }}</span>
                    </div>
                  </td>
                  <td class="py-3 px-4">
                    @if($isAdmin)
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10.5px] font-black bg-indigo-50 text-indigo-700 border border-indigo-200">
                        <i class="fa-solid fa-shield-halved text-[10px]"></i> Administrator
                      </span>
                    @else
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10.5px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        <i class="fa-solid fa-user text-[10px] text-slate-400"></i> Staff / Employee
                      </span>
                    @endif
                  </td>
                  <td class="py-3 px-4">
                    @if($isAdmin)
                      <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-linear-to-r from-indigo-50 to-purple-50 text-indigo-800 border border-indigo-200">
                        <i class="fa-solid fa-unlock-keyhole text-indigo-600"></i> Full Access (All Sections &amp; Settings)
                      </span>
                    @else
                      <div class="flex flex-wrap gap-1">
                        @if(empty($sections))
                          <span class="text-slate-400 text-xs italic">No sections assigned</span>
                        @else
                          @foreach($sections as $sec)
                            @php
                              $secKey = strtolower($sec);
                              $badgeClass = match($secKey) {
                                'frontoffice' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                'pos'         => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'stores'      => 'bg-amber-50 text-amber-800 border-amber-200',
                                'banquet'     => 'bg-purple-50 text-purple-700 border-purple-200',
                                'accounts'    => 'bg-violet-50 text-violet-700 border-violet-200',
                                default       => 'bg-slate-100 text-slate-700 border-slate-200',
                              };
                              $secLabel = match($secKey) {
                                'frontoffice' => 'Front Office',
                                'pos'         => 'POS Sales',
                                'stores'      => 'Stores',
                                'banquet'     => 'Banquet',
                                'accounts'    => 'Accounts',
                                default       => ucfirst($sec),
                              };
                              $secIco = match($secKey) {
                                'frontoffice' => 'fa-desktop',
                                'pos'         => 'fa-utensils',
                                'stores'      => 'fa-boxes-stacked',
                                'banquet'     => 'fa-champagne-glasses',
                                'accounts'    => 'fa-file-invoice-dollar',
                                default       => 'fa-circle-check',
                              };
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-bold border {{ $badgeClass }}">
                              <i class="fa-solid {{ $secIco }} text-[9px]"></i> {{ $secLabel }}
                            </span>
                          @endforeach
                        @endif
                      </div>
                    @endif
                  </td>
                  <td class="py-3 px-4 text-center">
                    <button type="button" 
                            onclick="toggleUserStatus({{ $u->id }})" 
                            {{ $isCurrent ? 'disabled title="Cannot deactivate yourself"' : '' }}
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider transition-all cursor-pointer {{ $u->status === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100' }} {{ $isCurrent ? 'opacity-70 cursor-not-allowed' : '' }}" 
                            id="status-btn-{{ $u->id }}">
                      <span class="w-1.5 h-1.5 rounded-full {{ $u->status === 'Active' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                      <span id="status-text-{{ $u->id }}">{{ $u->status }}</span>
                    </button>
                  </td>
                  <td class="py-3 px-4 text-right">
                    <div class="inline-flex items-center gap-1.5 justify-end">
                      <button type="button" 
                              class="w-7 h-7 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 flex items-center justify-center transition-colors cursor-pointer" 
                              title="Edit User" 
                              onclick="openEditUserModal({{ $u->id }})">
                        <i class="fa-solid fa-pen text-xs"></i>
                      </button>
                      @if(!$isCurrent && strtolower($u->email ?? '') !== 'admin@hotelsagarsonnet.com')
                        <button type="button" 
                                class="w-7 h-7 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors cursor-pointer" 
                                title="Delete User" 
                                onclick="deleteUser({{ $u->id }}, '{{ addslashes($u->name) }}')">
                          <i class="fa-solid fa-trash-can text-xs"></i>
                        </button>
                      @endif
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="py-12 text-center text-slate-400">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-lg">
                      <i class="fa-solid fa-users-slash"></i>
                    </div>
                    <div class="font-bold text-slate-700 text-sm">No user accounts found</div>
                    <p class="text-xs text-slate-400 mt-1">Try clearing filters or add a new user to the system.</p>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($users->hasPages())
          <div class="p-4 border-t border-slate-200/80 bg-slate-50/50">
            {{ $users->links() }}
          </div>
        @endif

      </div>
    </section>
  </div>

</main>

<!-- ADD / EDIT USER MODAL -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden transition-opacity" id="user-crud-modal">
  <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl w-full max-w-xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
    
    <!-- Modal Header -->
    <div class="px-6 py-4.5 bg-slate-50/90 border-b border-slate-200 flex items-center justify-between">
      <div class="flex items-center gap-2.5">
        <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-sm shadow-xs font-bold" id="modal-icon">
          <i class="fa-solid fa-user-plus"></i>
        </div>
        <div>
          <h3 class="text-sm font-black text-slate-900 tracking-tight" id="modal-title">Add New Employee Account</h3>
          <p class="text-[11px] text-slate-500 font-medium mt-0.5">Configure login credentials and assign module permissions.</p>
        </div>
      </div>
      <button type="button" class="w-8 h-8 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors flex items-center justify-center cursor-pointer" onclick="closeUserModal()">
        <i class="fa-solid fa-xmark text-sm"></i>
      </button>
    </div>

    <!-- Modal Form Body -->
    <form id="user-crud-form" onsubmit="handleUserSubmit(event)" class="p-6 space-y-4">
      <input type="hidden" id="user-form-id" value="">
      
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Name -->
        <div class="sm:col-span-2">
          <label class="block text-[11px] font-black uppercase tracking-wider text-slate-700 mb-1.5">
            Full Employee Name <span class="text-rose-500">*</span>
          </label>
          <div class="relative">
            <i class="fa-solid fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" id="user-name" name="name" required placeholder="e.g. Rahul Sharma" class="w-full h-10 pl-9 pr-3.5 text-xs font-bold rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition-all">
          </div>
        </div>

        <!-- Phone (Primary Login) -->
        <div>
          <label class="block text-[11px] font-black uppercase tracking-wider text-slate-700 mb-1.5">
            Phone Number (Login ID) <span class="text-rose-500">*</span>
          </label>
          <div class="relative">
            <i class="fa-solid fa-phone absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" id="user-phone" name="phone" required placeholder="e.g. 9876543210" class="w-full h-10 pl-9 pr-3.5 text-xs font-mono font-bold rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition-all">
          </div>
          <p class="text-[10px] text-slate-400 mt-1">Staff will use this phone number to log in.</p>
        </div>

        <!-- Password -->
        <div>
          <label class="block text-[11px] font-black uppercase tracking-wider text-slate-700 mb-1.5">
            Password <span class="text-rose-500" id="password-req-star">*</span>
          </label>
          <div class="relative">
            <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="password" id="user-password" name="password" placeholder="Set login password" class="w-full h-10 pl-9 pr-3.5 text-xs font-bold rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition-all">
          </div>
          <p class="text-[10px] text-slate-400 mt-1" id="password-help-text">Minimum 4 characters.</p>
        </div>

        <!-- Role -->
        <div>
          <label class="block text-[11px] font-black uppercase tracking-wider text-slate-700 mb-1.5">
            System Role <span class="text-rose-500">*</span>
          </label>
          <select id="user-role" name="role" required onchange="handleRoleChange(this.value)" class="w-full h-10 px-3 text-xs font-bold rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition-all cursor-pointer">
            <option value="staff" selected>Staff / Employee</option>
            <option value="admin">Administrator (Full Access)</option>
          </select>
        </div>

        <!-- Status -->
        <div>
          <label class="block text-[11px] font-black uppercase tracking-wider text-slate-700 mb-1.5">
            Account Status <span class="text-rose-500">*</span>
          </label>
          <select id="user-status" name="status" required class="w-full h-10 px-3 text-xs font-bold rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition-all cursor-pointer">
            <option value="Active" selected>Active (Can Login)</option>
            <option value="Inactive">Inactive (Disabled)</option>
          </select>
        </div>

        <!-- Email (Optional) -->
        <div class="sm:col-span-2">
          <label class="block text-[11px] font-black uppercase tracking-wider text-slate-700 mb-1.5">
            Email Address <span class="text-slate-400 font-normal lowercase">(optional)</span>
          </label>
          <div class="relative">
            <i class="fa-solid fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="email" id="user-email" name="email" placeholder="e.g. employee@hotelsagarsonnet.com" class="w-full h-10 pl-9 pr-3.5 text-xs font-bold rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition-all">
          </div>
        </div>
      </div>

      <!-- Assigned Sections Box -->
      <div class="pt-2 border-t border-slate-100" id="sections-container">
        <div class="flex items-center justify-between mb-2">
          <div>
            <label class="text-[11px] font-black uppercase tracking-wider text-slate-800 block">
              Assigned Sections (Views &amp; Modules) <span class="text-rose-500">*</span>
            </label>
            <p class="text-[10px] text-slate-500">Select which views this employee can see and access. Unchecked sections are hidden.</p>
          </div>
          <button type="button" class="text-[10.5px] font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer" onclick="toggleAllSections()">
            Select All
          </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5" id="sections-checkboxes">
          @foreach($availableSections as $key => $sec)
            <label class="section-choice-card flex items-start gap-3 p-3 rounded-xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/20 cursor-pointer transition-all select-none">
              <input type="checkbox" name="assigned_sections[]" value="{{ $key }}" class="section-checkbox mt-0.5 w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
              <div class="min-w-0">
                <div class="text-xs font-extrabold text-slate-800 flex items-center gap-1.5">
                  <i class="fa-solid {{ $sec['icon'] }} text-indigo-600"></i>
                  <span>{{ $sec['name'] }}</span>
                </div>
                <div class="text-[10px] text-slate-400 mt-0.5 leading-tight">
                  {{ $sec['desc'] }}
                </div>
              </div>
            </label>
          @endforeach
        </div>

        <div class="hidden p-3 bg-indigo-50/60 rounded-xl border border-indigo-200 text-xs text-indigo-800 font-semibold items-center gap-2 mt-2" id="admin-role-notice">
          <i class="fa-solid fa-circle-info text-indigo-600"></i>
          <span>Administrator role automatically grants full, unrestricted access to all PMS modules &amp; master settings.</span>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
        <button type="button" class="h-10 px-4 text-xs font-bold text-slate-600 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl transition-all cursor-pointer" onclick="closeUserModal()">
          Cancel
        </button>
        <button type="submit" class="h-10 px-6 text-xs font-extrabold uppercase tracking-wider text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl transition-all shadow-md shadow-indigo-600/20 flex items-center gap-2 cursor-pointer" id="btn-save-user">
          <i class="fa-solid fa-check"></i> <span id="btn-save-user-text">Save User</span>
        </button>
      </div>
    </form>

  </div>
</div>

@push('scripts')
<script>
  const CSRF_TOKEN = '{{ csrf_token() }}';

  function openAddUserModal() {
    document.getElementById('user-form-id').value = '';
    document.getElementById('modal-title').textContent = 'Add New Employee Account';
    document.getElementById('modal-icon').innerHTML = '<i class="fa-solid fa-user-plus"></i>';
    document.getElementById('btn-save-user-text').textContent = 'Save User';
    document.getElementById('password-req-star').style.display = 'inline';
    document.getElementById('user-password').setAttribute('required', 'required');
    document.getElementById('password-help-text').textContent = 'Minimum 4 characters.';
    
    // Reset form fields
    document.getElementById('user-name').value = '';
    document.getElementById('user-phone').value = '';
    document.getElementById('user-password').value = '';
    document.getElementById('user-email').value = '';
    document.getElementById('user-role').value = 'staff';
    document.getElementById('user-status').value = 'Active';

    // Reset checkboxes
    document.querySelectorAll('.section-checkbox').forEach(cb => cb.checked = false);
    handleRoleChange('staff');

    const modal = document.getElementById('user-crud-modal');
    modal.classList.remove('hidden');
  }

  async function openEditUserModal(userId) {
    try {
      const res = await fetch(`{{ url('admin/users') }}/${userId}`, {
        headers: { 'Accept': 'application/json' }
      });
      const data = await res.json();
      if (!data.success || !data.user) {
        throw new Error('Failed to fetch user data');
      }

      const u = data.user;
      document.getElementById('user-form-id').value = u.id;
      document.getElementById('modal-title').textContent = `Edit User: ${u.name}`;
      document.getElementById('modal-icon').innerHTML = '<i class="fa-solid fa-user-pen"></i>';
      document.getElementById('btn-save-user-text').textContent = 'Update User';
      document.getElementById('password-req-star').style.display = 'none';
      document.getElementById('user-password').removeAttribute('required');
      document.getElementById('password-help-text').textContent = 'Leave empty to keep existing password.';

      document.getElementById('user-name').value = u.name || '';
      document.getElementById('user-phone').value = u.phone || '';
      document.getElementById('user-password').value = '';
      document.getElementById('user-email').value = u.email && !u.email.endsWith('@hotelsagarsonnet.local') ? u.email : '';
      document.getElementById('user-role').value = u.role || 'staff';
      document.getElementById('user-status').value = u.status || 'Active';

      // Checkboxes
      const assigned = (u.assigned_sections || []).map(s => String(s).toLowerCase());
      document.querySelectorAll('.section-checkbox').forEach(cb => {
        cb.checked = assigned.includes(cb.value.toLowerCase()) || u.role === 'admin';
      });

      handleRoleChange(u.role || 'staff');

      const modal = document.getElementById('user-crud-modal');
      modal.classList.remove('hidden');
    } catch (err) {
      console.error(err);
      PmsAlert.toast('Failed to load user information', 'error');
    }
  }

  function closeUserModal() {
    const modal = document.getElementById('user-crud-modal');
    modal.classList.add('hidden');
  }

  function handleRoleChange(role) {
    const isAdm = role === 'admin';
    const notice = document.getElementById('admin-role-notice');
    const checkboxes = document.querySelectorAll('.section-checkbox');

    if (isAdm) {
      if (notice) notice.classList.remove('hidden');
      if (notice) notice.classList.add('flex');
      checkboxes.forEach(cb => {
        cb.checked = true;
        cb.disabled = true;
      });
    } else {
      if (notice) notice.classList.add('hidden');
      if (notice) notice.classList.remove('flex');
      checkboxes.forEach(cb => {
        cb.disabled = false;
      });
    }
  }

  function toggleAllSections() {
    const checkboxes = document.querySelectorAll('.section-checkbox:not(:disabled)');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
  }

  async function handleUserSubmit(e) {
    e.preventDefault();
    const id = document.getElementById('user-form-id').value;
    const isEdit = Boolean(id);

    const name = document.getElementById('user-name').value.trim();
    const phone = document.getElementById('user-phone').value.trim();
    const password = document.getElementById('user-password').value;
    const role = document.getElementById('user-role').value;
    const status = document.getElementById('user-status').value;
    const email = document.getElementById('user-email').value.trim();

    const sections = [];
    document.querySelectorAll('.section-checkbox:checked').forEach(cb => sections.push(cb.value));

    if (role === 'staff' && sections.length === 0) {
      PmsAlert.toast('Please select at least one assigned section for this staff member!', 'warning');
      return;
    }

    const payload = {
      name,
      phone,
      password: password || undefined,
      role,
      status,
      email: email || undefined,
      assigned_sections: sections
    };

    const url = isEdit ? `{{ url('admin/users') }}/${id}` : `{{ route('admin.users.store') }}`;
    const method = isEdit ? 'PUT' : 'POST';

    const saveBtn = document.getElementById('btn-save-user');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

    try {
      const res = await fetch(url, {
        method: method,
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: JSON.stringify(payload)
      });

      const data = await res.json();
      if (!res.ok || !data.success) {
        let msg = data.message || 'Validation error';
        if (data.errors) {
          msg = Object.values(data.errors).flat().join('<br>');
        }
        Swal.fire({
          icon: 'error',
          title: 'Unable to Save User',
          html: msg,
          confirmButtonColor: '#4f46e5'
        });
        return;
      }

      PmsAlert.toast(data.message || 'User saved successfully!', 'success');
      closeUserModal();
      setTimeout(() => window.location.reload(), 400);

    } catch (err) {
      console.error(err);
      PmsAlert.toast('Network error occurred while saving user', 'error');
    } finally {
      saveBtn.disabled = false;
      saveBtn.innerHTML = '<i class="fa-solid fa-check"></i> <span id="btn-save-user-text">Save User</span>';
    }
  }

  async function toggleUserStatus(userId) {
    try {
      const res = await fetch(`{{ url('admin/users') }}/${userId}/toggle-status`, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': CSRF_TOKEN
        }
      });
      const data = await res.json();
      if (!data.success) {
        PmsAlert.toast(data.message || 'Failed to update status', 'warning');
        return;
      }

      PmsAlert.toast(data.message, 'success');
      const btn = document.getElementById(`status-btn-${userId}`);
      const txt = document.getElementById(`status-text-${userId}`);
      if (btn && txt) {
        txt.textContent = data.status;
        if (data.status === 'Active') {
          btn.className = btn.className.replace('bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100', 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100');
          btn.querySelector('span:first-child').className = 'w-1.5 h-1.5 rounded-full bg-emerald-500';
        } else {
          btn.className = btn.className.replace('bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100', 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100');
          btn.querySelector('span:first-child').className = 'w-1.5 h-1.5 rounded-full bg-rose-500';
        }
      }
    } catch (e) {
      console.error(e);
      PmsAlert.toast('Network error updating status', 'error');
    }
  }

  function deleteUser(userId, userName) {
    Swal.fire({
      title: 'Delete User Account?',
      html: `Are you sure you want to delete employee <strong>${userName}</strong>?<br><span class="text-rose-500 text-xs">This user will immediately lose access to the PMS system.</span>`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#e11d48',
      cancelButtonColor: '#64748b',
      confirmButtonText: '<i class="fa-solid fa-trash-can mr-1"></i> Yes, Delete',
      cancelButtonText: 'Cancel',
      reverseButtons: true
    }).then(async res => {
      if (res.isConfirmed) {
        try {
          const resp = await fetch(`{{ url('admin/users') }}/${userId}`, {
            method: 'DELETE',
            headers: {
              'Accept': 'application/json',
              'X-CSRF-TOKEN': CSRF_TOKEN
            }
          });
          const d = await resp.json();
          if (!d.success) {
            Swal.fire('Error', d.message || 'Failed to delete user', 'error');
            return;
          }

          PmsAlert.toast(d.message, 'success');
          const row = document.getElementById(`user-row-${userId}`);
          if (row) row.remove();
        } catch (e) {
          console.error(e);
          PmsAlert.toast('Network error deleting user', 'error');
        }
      }
    });
  }
</script>
@endpush

@endsection

