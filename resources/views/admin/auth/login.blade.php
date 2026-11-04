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
        @if(session('error') || $errors->any())
          <div class="mb-4 p-3 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs flex items-start gap-2.5">
            <i class="fa-solid fa-triangle-exclamation text-rose-400 mt-0.5 shrink-0"></i>
            <div>
              {{ session('error') ?? $errors->first() }}
            </div>
          </div>
        @endif

        @if(session('success'))
          <div class="mb-4 p-3 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-xs flex items-start gap-2.5">
            <i class="fa-solid fa-circle-check text-emerald-400 mt-0.5 shrink-0"></i>
            <div>{{ session('success') }}</div>
          </div>
        @endif

        @if(session('warning'))
          <div class="mb-4 p-3 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-300 text-xs flex items-start gap-2.5">
            <i class="fa-solid fa-circle-info text-amber-400 mt-0.5 shrink-0"></i>
            <div>{{ session('warning') }}</div>
          </div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST" class="flex flex-col gap-4">
          @csrf

          <div>
            <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1.5">Phone Number / Staff ID / Email</label>
            <div class="relative">
              <i class="fa-solid fa-user-tie absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
              <input type="text" 
                     name="login" 
                     id="input-login"
                     class="w-full h-11 pl-9 pr-3.5 text-xs font-bold rounded-xl bg-slate-800/90 text-slate-100 border border-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all placeholder-slate-500" 
                     value="{{ old('login', '9876543210') }}" 
                     placeholder="Enter phone or email" 
                     required>
            </div>
          </div>

          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400">Password</label>
              <button type="button" class="text-[10px] font-bold text-indigo-400 hover:text-indigo-300" onclick="togglePassVisibility()">
                <i class="fa-regular fa-eye" id="pass-eye-icon"></i> <span id="pass-eye-text">Show</span>
              </button>
            </div>
            <div class="relative">
              <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
              <input type="password" 
                     name="password" 
                     id="input-password"
                     class="w-full h-11 pl-9 pr-3.5 text-xs font-bold rounded-xl bg-slate-800/90 text-slate-100 border border-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all placeholder-slate-500" 
                     value="sagar2026" 
                     placeholder="Enter password" 
                     required>
            </div>
          </div>

          <button type="submit" class="w-full h-12 mt-2 bg-linear-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-indigo-600/30 flex items-center justify-center gap-2 transition-all hover:scale-101 active:scale-98 cursor-pointer">
            <i class="fa-solid fa-right-to-bracket"></i> Login to System
          </button>

          <!-- Quick Fill Presets for Seamless Testing -->
          <div class="mt-2 p-3 bg-slate-800/50 rounded-2xl border border-slate-700/60 text-center text-xs text-slate-400">
            <div class="text-[11px] font-bold text-slate-300 mb-2 flex items-center justify-center gap-1.5">
              <i class="fa-solid fa-bolt text-amber-400"></i> Quick Fill Test Accounts:
            </div>
            <div class="grid grid-cols-2 gap-2">
              <button type="button" 
                      onclick="fillCreds('9876543210', 'sagar2026')"
                      class="px-2.5 py-1.5 rounded-lg bg-indigo-600/20 hover:bg-indigo-600/40 border border-indigo-500/30 text-indigo-300 text-[10.5px] font-bold transition-all text-left truncate cursor-pointer">
                <i class="fa-solid fa-shield-halved text-[9px] mr-1"></i> Admin (All Views)
              </button>
              <button type="button" 
                      onclick="fillCreds('9876543211', 'frontdesk123')"
                      class="px-2.5 py-1.5 rounded-lg bg-emerald-600/20 hover:bg-emerald-600/40 border border-emerald-500/30 text-emerald-300 text-[10.5px] font-bold transition-all text-left truncate cursor-pointer">
                <i class="fa-solid fa-desktop text-[9px] mr-1"></i> Front Desk Staff
              </button>
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

  <script>
    function togglePassVisibility() {
      const pass = document.getElementById('input-password');
      const eye = document.getElementById('pass-eye-icon');
      const txt = document.getElementById('pass-eye-text');
      if (pass.type === 'password') {
        pass.type = 'text';
        eye.className = 'fa-regular fa-eye-slash';
        txt.textContent = 'Hide';
      } else {
        pass.type = 'password';
        eye.className = 'fa-regular fa-eye';
        txt.textContent = 'Show';
      }
    }

    function fillCreds(login, pass) {
      document.getElementById('input-login').value = login;
      document.getElementById('input-password').value = pass;
    }
  </script>

    </div>
  </div>

</body>
</html>
