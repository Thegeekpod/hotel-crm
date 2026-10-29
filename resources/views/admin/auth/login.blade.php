<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Hotel Sagar Sonnet CRM</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-full flex items-center justify-center p-4 bg-slate-950 font-sans text-slate-100 relative overflow-x-hidden antialiased">

  <!-- Ambient Luxury Glow Background -->
  <div class="fixed inset-0 pointer-events-none overflow-hidden">
    <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-indigo-600/20 blur-3xl animate-pulse"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 rounded-full bg-purple-600/20 blur-3xl animate-pulse"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full bg-indigo-500/5 blur-[120px] pointer-events-none"></div>
  </div>

  <div class="w-full max-w-md relative z-10 my-8">
    <div class="bg-slate-900/80 backdrop-blur-2xl rounded-3xl border border-slate-700/60 shadow-2xl overflow-hidden relative">
      
      <!-- Top Accent Line -->
      <div class="h-1 bg-linear-to-r from-indigo-500 via-amber-400 to-indigo-500"></div>

      <!-- Header -->
      <div class="px-8 pt-8 pb-6 text-center border-b border-slate-800/80 bg-slate-800/30">
        <div class="w-16 h-16 rounded-2xl bg-linear-to-br from-indigo-600 to-violet-600 text-white flex items-center justify-center text-2xl mx-auto mb-4 shadow-lg shadow-indigo-500/25">
          <i class="fa-solid fa-hotel text-amber-300"></i>
        </div>
        <h2 class="text-xl font-black tracking-wider text-white">HOTEL SAGAR SONNET</h2>
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1">Property Management System &amp; CRM</p>
      </div>

      <!-- Body Form -->
      <div class="p-8">
        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex flex-col gap-4">
          <div>
            <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1.5">Hotel Wing / Property</label>
            <select class="w-full h-11 px-3.5 text-xs font-bold rounded-xl bg-slate-800/90 text-slate-200 border border-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
              <option>Hotel Sagar Sonnet - Main Luxury Wing</option>
              <option>Hotel Sagar Sonnet - Sea Beach Resort &amp; Spa</option>
              <option>Hotel Sagar Sonnet - Executive Heritage Suites</option>
            </select>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1.5">Staff Role</label>
              <select class="w-full h-11 px-3 text-xs font-bold rounded-xl bg-slate-800/90 text-slate-200 border border-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                <option>Front Desk Cashier</option>
                <option>Duty Manager</option>
                <option>Housekeeping Lead</option>
                <option>Restaurant Cashier</option>
                <option selected>Administrator</option>
              </select>
            </div>
            <div>
              <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1.5">Duty Shift</label>
              <select class="w-full h-11 px-3 text-xs font-bold rounded-xl bg-slate-800/90 text-slate-200 border border-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                <option>Morning (07:00 - 15:00)</option>
                <option>Evening (15:00 - 23:00)</option>
                <option>Night Audit (23:00 - 07:00)</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1.5">Staff Username</label>
            <input type="text" class="w-full h-11 px-3.5 text-xs font-bold rounded-xl bg-slate-800/90 text-slate-100 border border-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" value="admin.sagar" required>
          </div>

          <div>
            <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1.5">Password</label>
            <input type="password" class="w-full h-11 px-3.5 text-xs font-bold rounded-xl bg-slate-800/90 text-slate-100 border border-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" value="sagar2026" required>
          </div>

          <button type="submit" class="w-full h-12 mt-2 bg-linear-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-indigo-600/30 flex items-center justify-center gap-2 transition-all hover:scale-101 active:scale-98">
            <i class="fa-solid fa-right-to-bracket"></i> Login to PMS Dashboard
          </button>

          <!-- Demo Access Quick Box -->
          <div class="mt-2 p-3 bg-slate-800/50 rounded-xl border border-slate-700/60 text-center text-xs text-slate-400">
            <div>
              <strong class="text-slate-300"><i class="fa-solid fa-circle-info text-indigo-400"></i> Demo Access:</strong><br>
              User: <span class="text-indigo-400 font-bold">admin.sagar</span> | Pass: <span class="text-indigo-400 font-bold">sagar2026</span>
            </div>
            <div class="mt-2.5">
              <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-indigo-600/20 border border-indigo-500/40 text-indigo-300 font-bold text-xs hover:bg-indigo-600 hover:text-white transition-all shadow-xs">
                <i class="fa-solid fa-bolt text-amber-300"></i> One-Click Direct Entrance
              </a>
            </div>
          </div>
        </form>
      </div>

      <!-- Footer Note -->
      <div class="px-8 pb-6 text-center text-[10px] text-slate-500 font-medium">
        <i class="fa-solid fa-shield-halved text-indigo-400"></i> Secured Enterprise PMS v2026.1 • Hotel Sagar Sonnet Group
      </div>

    </div>
  </div>

</body>
</html>
