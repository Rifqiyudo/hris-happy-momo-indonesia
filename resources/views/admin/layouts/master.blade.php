<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Admin Panel') - HRIS PT. Happy Momo Indonesia</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    boxShadow: {
                        /* Custom Glow Shadows untuk Estetika Dark Mode */
                        'glow-subtle': '0 0 25px -5px rgba(99, 102, 241, 0.15)',
                        'glow-dark': '0 0 30px -5px rgba(129, 140, 248, 0.12)',
                        'glow-amber': '0 0 20px -2px rgba(251, 191, 36, 0.25)',
                        'glow-rose': '0 0 20px -2px rgba(244, 63, 94, 0.25)',
                        'glow-emerald': '0 0 20px -2px rgba(16, 185, 129, 0.25)',
                        'ios-card': '0 10px 40px -10px rgba(0, 0, 0, 0.6)',
                    },
                    colors: {
                        /* Palet Warna Latar Hitam Pekat & Aksen Khas iOS */
                        'ios-dark-bg': '#000000',
                        'ios-dark-card': 'rgba(28, 28, 30, 0.7)',
                        'ios-dark-border': 'rgba(255, 255, 255, 0.08)',
                    }
                }
            }
        }
    </script>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <!-- SCRIPT INISIALISASI DARK MODE (MENCEGAH FLASH SAAT LOAD) -->
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- UBAH TYPE KE TAILWINDCSS AGAR CSS KITA MASUK KE INTI TAILWIND -->
    <style type="text/tailwindcss">
        body { font-family: 'Inter', sans-serif; }
        
        /* Custom Scrollbar Estetik */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        .dark ::-webkit-scrollbar-track { background: #000000; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .dark ::-webkit-scrollbar-thumb { background: #2c2c2e; border: 1px solid rgba(255,255,255,0.05); }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .dark ::-webkit-scrollbar-thumb:hover { background: #48484a; }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fadeIn { animation: fadeIn 0.4s ease-out; }

        @layer utilities {
            /* KELAS GLOBAL EFEK KARTU AESTHETIC DARK MODE */
            .dark .card-aesthetic {
                background-color: theme('colors.ios-dark-card');
                backdrop-filter: blur(24px);
                -webkit-backdrop-filter: blur(24px);
                border: 1px solid theme('colors.ios-dark-border');
                box-shadow: theme('boxShadow.ios-card'), inset 0 1px 0 0 rgba(255,255,255,0.05);
            }

            /* =====================================================================
               🌟 1. SAPU JAGAT BACKGROUND DASAR
               Mencari semua kelas bg-slate-50 atau bg-gray-50 di seluruh halaman
               ===================================================================== */
            .dark body, 
            .dark [class*="bg-slate-50"], 
            .dark [class*="bg-gray-50"],
            .dark [class*="bg-[#f8fafc]"] {
                background-color: theme('colors.ios-dark-bg') !important;
                color: #f8fafc !important;
            }

            /* =====================================================================
               🌟 2. SAPU JAGAT KARTU (CARD)
               Begitu mode malam aktif, SEMUA kotak putih otomatis jadi kaca estetik
               ===================================================================== */
            .dark .bg-white {
                background-color: theme('colors.ios-dark-card') !important;
                backdrop-filter: blur(24px) !important;
                -webkit-backdrop-filter: blur(24px) !important;
                border-color: theme('colors.ios-dark-border') !important;
                box-shadow: theme('boxShadow.ios-card'), inset 0 1px 0 0 rgba(255,255,255,0.05) !important;
            }

            /* Hover efek untuk list, dropdown, atau tabel */
            .dark .hover\:bg-slate-50:hover, 
            .dark .hover\:bg-gray-50:hover { 
                background-color: rgba(255,255,255,0.05) !important; 
            }

            /* =====================================================================
               🌟 3. SAPU JAGAT WARNA TEKS 
               Tidak peduli Anda pakai text-slate-800, 900, dll semuanya otomatis putih!
               ===================================================================== */
            /* Tangkap teks gelap (Judul & Teks Utama) -> Ubah ke Putih Menyala */
            .dark [class*="text-slate-6"], .dark [class*="text-slate-7"], .dark [class*="text-slate-8"], .dark [class*="text-slate-9"],
            .dark [class*="text-gray-6"], .dark [class*="text-gray-7"], .dark [class*="text-gray-8"], .dark [class*="text-gray-9"] {
                color: #ffffff !important;
                text-shadow: 0 1px 2px rgba(0,0,0,0.5) !important;
            }

            /* Tangkap teks sedang (Subtitle/Keterangan) -> Ubah ke Abu-abu Terang */
            .dark [class*="text-slate-4"], .dark [class*="text-slate-5"],
            .dark [class*="text-gray-4"], .dark [class*="text-gray-5"] {
                color: #94a3b8 !important;
            }

            /* =====================================================================
               🌟 4. SAPU JAGAT GARIS (BORDER)
               Semua border bawaan Tailwind otomatis jadi transparan tipis ala iOS
               ===================================================================== */
            .dark [class*="border-slate-1"], .dark [class*="border-slate-2"], .dark [class*="border-slate-3"],
            .dark [class*="border-gray-1"], .dark [class*="border-gray-2"], .dark [class*="border-gray-3"] {
                border-color: theme('colors.ios-dark-border') !important;
            }

            /* =====================================================================
               🌟 5. SAPU JAGAT TABEL
               Semua tabel di halaman manapun otomatis menyesuaikan
               ===================================================================== */
            .dark table { border-color: theme('colors.ios-dark-border') !important; }
            .dark thead, .dark th { 
                background-color: rgba(255,255,255,0.04) !important; 
                color: #cbd5e1 !important; 
                border-bottom: 1px solid theme('colors.ios-dark-border') !important; 
            }
            .dark tbody tr { 
                border-bottom: 1px solid rgba(255,255,255,0.04) !important; 
                transition: background-color 0.2s ease !important; 
            }
            .dark tbody tr:hover { background-color: rgba(255,255,255,0.03) !important; }
            .dark td { color: #f1f5f9 !important; }
        }
    </style>
    @yield('styles')
</head>
<body class="bg-slate-50 dark:bg-ios-dark-bg text-slate-800 dark:text-slate-100 antialiased overflow-x-hidden transition-colors duration-300">

    <div class="flex h-screen overflow-hidden relative">
        
        {{-- BACKDROP / LATAR BELAKANG GELAP SAAT SIDEBAR DIBUKA DI HP --}}
        <div id="sidebarBackdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity opacity-0"></div>

        {{-- SIDEBAR (Responsif: Slide-over di HP/Tablet, Flex statis di Desktop) --}}
        <aside id="appSidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-72 lg:w-64 bg-white dark:bg-black/40 dark:backdrop-blur-3xl border-r border-slate-200 dark:border-ios-dark-border transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col flex-shrink-0 shadow-2xl lg:shadow-none z-50 relative">
            {{-- BRANDING --}}
            <div class="h-20 px-6 flex items-center justify-between border-b border-slate-100 dark:border-ios-dark-border">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-600 dark:bg-indigo-500 rounded-lg flex items-center justify-center text-white font-black shadow-lg shadow-indigo-200 dark:shadow-glow-subtle text-xl">
                        M
                    </div>
                    <div>
                        <h1 class="font-black text-sm text-slate-900 dark:text-white leading-tight tracking-tight">HRIS PT. Happy<br>Momo Indonesia</h1>
                    </div>
                </div>
                {{-- Tombol Tutup (X) khusus tampilan Mobile --}}
                <button onclick="toggleSidebar()" class="lg:hidden w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800/60 text-slate-500 dark:text-slate-300 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-900/40 dark:hover:text-rose-400 flex items-center justify-center transition-colors border border-transparent dark:border-white/10">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto py-6 custom-scrollbar space-y-5">
                
                {{-- FOLDER 1: MENU UTAMA --}}
                @php $isUtamaActive = request()->routeIs('admin.dashboard', 'admin.master.user.*'); @endphp
                <div>
                    <button onclick="toggleMenu('menu-utama', 'icon-utama')" class="w-full flex items-center justify-between px-6 py-2 focus:outline-none group">
                        <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors drop-shadow-sm">Menu Utama</span>
                        <i id="icon-utama" class="fas fa-chevron-right text-slate-300 dark:text-slate-600 text-[10px] transition-transform duration-300 {{ $isUtamaActive ? 'rotate-90 text-indigo-500 dark:text-indigo-400 dark:drop-shadow-[0_0_8px_rgba(99,102,241,0.8)]' : 'group-hover:text-indigo-500' }}"></i>
                    </button>
                    
                    <div id="menu-utama" class="mt-2 space-y-1 relative {{ $isUtamaActive ? '' : 'hidden' }}">
                        <div class="absolute left-8 top-0 bottom-0 w-[1px] bg-slate-100 dark:bg-white/[0.08]"></div>

                        <a href="{{ route('admin.dashboard') }}" class="flex items-center pl-12 pr-6 py-2.5 text-[13px] font-bold transition-all relative {{ request()->routeIs('admin.dashboard') ? 'text-indigo-600 dark:text-indigo-400 dark:drop-shadow-[0_0_5px_rgba(99,102,241,0.5)]' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]' }}">
                            @if(request()->routeIs('admin.dashboard')) <div class="absolute left-[31px] w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 border border-white dark:border-black shadow-sm dark:shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div> @endif
                            <i class="fas fa-home w-5 text-center mr-2 opacity-70"></i> Dashboard
                        </a>

                        @if(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.master.user.index') }}" class="flex items-center pl-12 pr-6 py-2.5 text-[13px] font-bold transition-all relative {{ request()->routeIs('admin.master.user.*') ? 'text-indigo-600 dark:text-indigo-400 dark:drop-shadow-[0_0_5px_rgba(99,102,241,0.5)]' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]' }}">
                            @if(request()->routeIs('admin.master.user.*')) <div class="absolute left-[31px] w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 border border-white dark:border-black shadow-sm dark:shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div> @endif
                            <i class="fas fa-users w-5 text-center mr-2 opacity-70"></i> Data Karyawan
                        </a>
                        @endif
                    </div>
                </div>

                {{-- FOLDER 2: ORGANISASI --}}
                @if(Auth::user()->role === 'admin')
                @php $isOrgActive = request()->routeIs('admin.master.department.*', 'admin.master.branch.*', 'admin.master.position.*'); @endphp
                <div>
                    <button onclick="toggleMenu('menu-org', 'icon-org')" class="w-full flex items-center justify-between px-6 py-2 focus:outline-none group">
                        <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors drop-shadow-sm">Organisasi</span>
                        <i id="icon-org" class="fas fa-chevron-right text-slate-300 dark:text-slate-600 text-[10px] transition-transform duration-300 {{ $isOrgActive ? 'rotate-90 text-indigo-500 dark:text-indigo-400 dark:drop-shadow-[0_0_8px_rgba(99,102,241,0.8)]' : 'group-hover:text-indigo-500' }}"></i>
                    </button>
                    
                    <div id="menu-org" class="mt-2 space-y-1 relative {{ $isOrgActive ? '' : 'hidden' }}">
                        <div class="absolute left-8 top-0 bottom-0 w-[1px] bg-slate-100 dark:bg-white/[0.08]"></div>

                        <a href="{{ route('admin.master.department.index') }}" class="flex items-center pl-12 pr-6 py-2.5 text-[13px] font-bold transition-all relative {{ request()->routeIs('admin.master.department.*') ? 'text-indigo-600 dark:text-indigo-400 dark:drop-shadow-[0_0_5px_rgba(99,102,241,0.5)]' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]' }}">
                            @if(request()->routeIs('admin.master.department.*')) <div class="absolute left-[31px] w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 border border-white dark:border-black shadow-sm dark:shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div> @endif
                            <i class="fas fa-sitemap w-5 text-center mr-2 opacity-70"></i> Data Departemen
                        </a>

                        <a href="{{ route('admin.master.branch.index') }}" class="flex items-center pl-12 pr-6 py-2.5 text-[13px] font-bold transition-all relative {{ request()->routeIs('admin.master.branch.*') ? 'text-indigo-600 dark:text-indigo-400 dark:drop-shadow-[0_0_5px_rgba(99,102,241,0.5)]' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]' }}">
                            @if(request()->routeIs('admin.master.branch.*')) <div class="absolute left-[31px] w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 border border-white dark:border-black shadow-sm dark:shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div> @endif
                            <i class="fas fa-building w-5 text-center mr-2 opacity-70"></i> Data Cabang
                        </a>
                        
                        <a href="{{ route('admin.master.position.index') }}" class="flex items-center pl-12 pr-6 py-2.5 text-[13px] font-bold transition-all relative {{ request()->routeIs('admin.master.position.*') ? 'text-indigo-600 dark:text-indigo-400 dark:drop-shadow-[0_0_5px_rgba(99,102,241,0.5)]' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]' }}">
                            @if(request()->routeIs('admin.master.position.*')) <div class="absolute left-[31px] w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 border border-white dark:border-black shadow-sm dark:shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div> @endif
                            <i class="fas fa-briefcase w-5 text-center mr-2 opacity-70"></i> Data Jabatan
                        </a>
                    </div>
                </div>
                @endif

                {{-- FOLDER 3: PENGATURAN KOMPONEN --}}
                @if(Auth::user()->role === 'admin')
                @php $isKompActive = request()->routeIs('admin.master.jadwal.shift.*', 'admin.master.allowance.*', 'admin.master.kpi-indicator.*'); @endphp
                <div>
                    <button onclick="toggleMenu('menu-komp', 'icon-komp')" class="w-full flex items-center justify-between px-6 py-2 focus:outline-none group">
                        <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors drop-shadow-sm">Pengaturan Komponen</span>
                        <i id="icon-komp" class="fas fa-chevron-right text-slate-300 dark:text-slate-600 text-[10px] transition-transform duration-300 {{ $isKompActive ? 'rotate-90 text-indigo-500 dark:text-indigo-400 dark:drop-shadow-[0_0_8px_rgba(99,102,241,0.8)]' : 'group-hover:text-indigo-500' }}"></i>
                    </button>
                    
                    <div id="menu-komp" class="mt-2 space-y-1 relative {{ $isKompActive ? '' : 'hidden' }}">
                        <div class="absolute left-8 top-0 bottom-0 w-[1px] bg-slate-100 dark:bg-white/[0.08]"></div>

                        <a href="{{ route('admin.master.jadwal.shift.index') }}" class="flex items-center pl-12 pr-6 py-2.5 text-[13px] font-bold transition-all relative {{ request()->routeIs('admin.master.jadwal.shift.*') ? 'text-indigo-600 dark:text-indigo-400 dark:drop-shadow-[0_0_5px_rgba(99,102,241,0.5)]' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]' }}">
                            @if(request()->routeIs('admin.master.jadwal.shift.*')) <div class="absolute left-[31px] w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 border border-white dark:border-black shadow-sm dark:shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div> @endif
                            <i class="fas fa-clock w-5 text-center mr-2 opacity-70"></i> Jadwal Shift
                        </a>

                        <a href="{{ route('admin.master.allowance.index') }}" class="flex items-center pl-12 pr-6 py-2.5 text-[13px] font-bold transition-all relative {{ request()->routeIs('admin.master.allowance.*') ? 'text-indigo-600 dark:text-indigo-400 dark:drop-shadow-[0_0_5px_rgba(99,102,241,0.5)]' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]' }}">
                            @if(request()->routeIs('admin.master.allowance.*')) <div class="absolute left-[31px] w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 border border-white dark:border-black shadow-sm dark:shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div> @endif
                            <i class="fas fa-wallet w-5 text-center mr-2 opacity-70"></i> Tunjangan
                        </a>

                        <a href="{{ route('admin.master.kpi-indicator.index') }}" class="flex items-center pl-12 pr-6 py-2.5 text-[13px] font-bold transition-all relative {{ request()->routeIs('admin.master.kpi-indicator.*') ? 'text-indigo-600 dark:text-indigo-400 dark:drop-shadow-[0_0_5px_rgba(99,102,241,0.5)]' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]' }}">
                            @if(request()->routeIs('admin.master.kpi-indicator.*')) <div class="absolute left-[31px] w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 border border-white dark:border-black shadow-sm dark:shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div> @endif
                            <i class="fas fa-tasks w-5 text-center mr-2 opacity-70"></i> Indikator KPI
                        </a>
                    </div>
                </div>
                @endif

                {{-- FOLDER 4: TRANSAKSI & AKTIVITAS --}}
                @php 
                    $isTransActive = request()->routeIs('admin.transaksi.asset.*', 'admin.chat.*', 'admin.transaksi.attendance.*', 'admin.transaksi.leave.*', 'admin.transaksi.overtime.*', 'admin.transaksi.reimbursement.*', 'admin.transaksi.kpi.*', 'admin.transaksi.payroll.*', 'admin.gaji.*', 'admin.master.announcement.*'); 
                    
                    $unreadChatsCount = \App\Models\ChatMessage::where('is_read', false)->where('sender_id', '!=', auth()->id())->count();
                    $pendingLeave = \App\Models\LeaveRequest::where('status', 'Pending')->count(); 
                    $pendingOvertime = class_exists('\App\Models\Overtime') ? \App\Models\Overtime::where('status', 'pending')->count() : 0;
                    $pendingReimburse = class_exists('\App\Models\Reimbursement') ? \App\Models\Reimbursement::where('status', 'Pending')->count() : 0;
                    
                    $hasTransNotif = ($unreadChatsCount > 0 || $pendingLeave > 0 || $pendingOvertime > 0 || $pendingReimburse > 0);
                @endphp
                <div>
                    <button onclick="toggleMenu('menu-trans', 'icon-trans')" class="w-full flex items-center justify-between px-6 py-2 focus:outline-none group">
                        <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors flex items-center drop-shadow-sm">
                            Transaksi & Aktivitas
                            @if($hasTransNotif)
                                <span class="w-2 h-2 rounded-full bg-rose-500 ml-2 animate-pulse shadow-glow-rose border border-rose-400/50"></span>
                            @endif
                        </span>
                        <i id="icon-trans" class="fas fa-chevron-right text-slate-300 dark:text-slate-600 text-[10px] transition-transform duration-300 {{ $isTransActive ? 'rotate-90 text-indigo-500 dark:text-indigo-400 dark:drop-shadow-[0_0_8px_rgba(99,102,241,0.8)]' : 'group-hover:text-indigo-500' }}"></i>
                    </button>
                    
                    <div id="menu-trans" class="mt-2 space-y-1 relative {{ $isTransActive ? '' : 'hidden' }}">
                        <div class="absolute left-8 top-0 bottom-0 w-[1px] bg-slate-100 dark:bg-white/[0.08]"></div>

                        <a href="{{ route('admin.transaksi.asset.index') }}" class="flex items-center pl-12 pr-6 py-2.5 text-[13px] font-bold transition-all relative {{ request()->routeIs('admin.transaksi.asset.*') ? 'text-indigo-600 dark:text-indigo-400 dark:drop-shadow-[0_0_5px_rgba(99,102,241,0.5)]' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]' }}">
                            @if(request()->routeIs('admin.transaksi.asset.*')) <div class="absolute left-[31px] w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 border border-white dark:border-black shadow-sm dark:shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div> @endif
                            <i class="fas fa-box-open w-5 text-center mr-2 opacity-70"></i> Manajemen Aset
                        </a>

                        <a href="{{ route('admin.chat.index') }}" class="flex items-center justify-between pl-12 pr-6 py-2.5 text-[13px] font-bold transition-all relative {{ request()->routeIs('admin.chat.*') ? 'text-indigo-600 dark:text-indigo-400 dark:drop-shadow-[0_0_5px_rgba(99,102,241,0.5)]' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]' }}">
                            @if(request()->routeIs('admin.chat.*')) <div class="absolute left-[31px] w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 border border-white dark:border-black shadow-sm dark:shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div> @endif
                            <div class="flex items-center">
                                <i class="fas fa-comments w-5 text-center mr-2 opacity-70"></i> Pesan Internal
                            </div>
                            @if($unreadChatsCount > 0)
                                <span class="bg-rose-500 text-white text-[9px] font-black w-4 h-4 flex items-center justify-center rounded-full animate-pulse shadow-glow-rose border border-rose-400/50">{{ $unreadChatsCount }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.transaksi.attendance.scan') }}" class="flex items-center pl-12 pr-6 py-2.5 text-[13px] font-bold transition-all relative {{ request()->routeIs('admin.transaksi.attendance.scan') ? 'text-indigo-600 dark:text-indigo-400 dark:drop-shadow-[0_0_5px_rgba(99,102,241,0.5)]' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]' }}">
                            @if(request()->routeIs('admin.transaksi.attendance.scan')) <div class="absolute left-[31px] w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 border border-white dark:border-black shadow-sm dark:shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div> @endif
                            <i class="fas fa-qrcode w-5 text-center mr-2 opacity-70"></i> Kiosk Barcode
                        </a>

                        <a href="{{ route('admin.transaksi.attendance.index') }}" class="flex items-center pl-12 pr-6 py-2.5 text-[13px] font-bold transition-all relative {{ request()->routeIs('admin.transaksi.attendance.index') ? 'text-indigo-600 dark:text-indigo-400 dark:drop-shadow-[0_0_5px_rgba(99,102,241,0.5)]' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]' }}">
                            @if(request()->routeIs('admin.transaksi.attendance.index')) <div class="absolute left-[31px] w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 border border-white dark:border-black shadow-sm dark:shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div> @endif
                            <i class="fas fa-calendar-check w-5 text-center mr-2 opacity-70"></i> Monitoring Absen
                        </a>

                        <a href="{{ route('admin.transaksi.leave.index') }}" class="flex items-center justify-between pl-12 pr-6 py-2.5 text-[13px] font-bold transition-all relative {{ request()->routeIs('admin.transaksi.leave.*') ? 'text-indigo-600 dark:text-indigo-400 dark:drop-shadow-[0_0_5px_rgba(99,102,241,0.5)]' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]' }}">
                            @if(request()->routeIs('admin.transaksi.leave.*')) <div class="absolute left-[31px] w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 border border-white dark:border-black shadow-sm dark:shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div> @endif
                            <div class="flex items-center">
                                <i class="fas fa-envelope-open-text w-5 text-center mr-2 opacity-70"></i> Pengajuan Cuti
                            </div>
                            @if($pendingLeave > 0)
                                <span class="bg-amber-500 dark:bg-amber-400 text-white dark:text-black text-[9px] font-black px-1.5 py-0.5 rounded-full shadow-glow-amber border border-amber-400/30">{{ $pendingLeave }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.transaksi.overtime.index') }}" class="flex items-center justify-between pl-12 pr-6 py-2.5 text-[13px] font-bold transition-all relative {{ request()->routeIs('admin.transaksi.overtime.*') ? 'text-indigo-600 dark:text-indigo-400 dark:drop-shadow-[0_0_5px_rgba(99,102,241,0.5)]' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]' }}">
                            @if(request()->routeIs('admin.transaksi.overtime.*')) <div class="absolute left-[31px] w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 border border-white dark:border-black shadow-sm dark:shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div> @endif
                            <div class="flex items-center">
                                <i class="fas fa-clock w-5 text-center mr-2 opacity-70"></i> Pengajuan Lembur
                            </div>
                            @if($pendingOvertime > 0)
                                <span class="bg-amber-500 dark:bg-amber-400 text-white dark:text-black text-[9px] font-black px-1.5 py-0.5 rounded-full shadow-glow-amber border border-amber-400/30">{{ $pendingOvertime }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.transaksi.reimbursement.index') }}" class="flex items-center justify-between pl-12 pr-6 py-2.5 text-[13px] font-bold transition-all relative {{ request()->routeIs('admin.transaksi.reimbursement.*') ? 'text-indigo-600 dark:text-indigo-400 dark:drop-shadow-[0_0_5px_rgba(99,102,241,0.5)]' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]' }}">
                            @if(request()->routeIs('admin.transaksi.reimbursement.*')) <div class="absolute left-[31px] w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 border border-white dark:border-black shadow-sm dark:shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div> @endif
                            <div class="flex items-center">
                                <i class="fas fa-receipt w-5 text-center mr-2 opacity-70"></i> Klaim & Kasbon
                            </div>
                            @if($pendingReimburse > 0)
                                <span class="bg-amber-500 dark:bg-amber-400 text-white dark:text-black text-[9px] font-black px-1.5 py-0.5 rounded-full shadow-glow-amber border border-amber-400/30">{{ $pendingReimburse }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.transaksi.kpi.index') }}" class="flex items-center pl-12 pr-6 py-2.5 text-[13px] font-bold transition-all relative {{ request()->routeIs('admin.transaksi.kpi.*') ? 'text-indigo-600 dark:text-indigo-400 dark:drop-shadow-[0_0_5px_rgba(99,102,241,0.5)]' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]' }}">
                            @if(request()->routeIs('admin.transaksi.kpi.*')) <div class="absolute left-[31px] w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 border border-white dark:border-black shadow-sm dark:shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div> @endif
                            <i class="fas fa-chart-line w-5 text-center mr-2 opacity-70"></i> Penilaian KPI
                        </a>

                        @if(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.transaksi.payroll.index') }}" class="flex items-center pl-12 pr-6 py-2.5 text-[13px] font-bold transition-all relative {{ request()->routeIs('admin.transaksi.payroll.*') ? 'text-indigo-600 dark:text-indigo-400 dark:drop-shadow-[0_0_5px_rgba(99,102,241,0.5)]' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]' }}">
                            @if(request()->routeIs('admin.transaksi.payroll.*')) <div class="absolute left-[31px] w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 border border-white dark:border-black shadow-sm dark:shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div> @endif
                            <i class="fas fa-money-bill-wave w-5 text-center mr-2 opacity-70"></i> Penggajian
                        </a>

                        <a href="{{ route('admin.gaji.index') }}" class="flex items-center pl-12 pr-6 py-2.5 text-[13px] font-bold transition-all relative {{ request()->routeIs('admin.gaji.*') ? 'text-indigo-600 dark:text-indigo-400 dark:drop-shadow-[0_0_5px_rgba(99,102,241,0.5)]' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]' }}">
                            @if(request()->routeIs('admin.gaji.*')) <div class="absolute left-[31px] w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 border border-white dark:border-black shadow-sm dark:shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div> @endif
                            <i class="fas fa-file-invoice-dollar w-5 text-center mr-2 opacity-70"></i> Master Gaji
                        </a>
                        
                        <a href="{{ route('admin.master.announcement.index') }}" class="flex items-center pl-12 pr-6 py-2.5 text-[13px] font-bold transition-all relative {{ request()->routeIs('admin.master.announcement.*') ? 'text-indigo-600 dark:text-indigo-400 dark:drop-shadow-[0_0_5px_rgba(99,102,241,0.5)]' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]' }}">
                            @if(request()->routeIs('admin.master.announcement.*')) <div class="absolute left-[31px] w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 border border-white dark:border-black shadow-sm dark:shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div> @endif
                            <i class="fas fa-bullhorn w-5 text-center mr-2 opacity-70"></i> Pengumuman
                        </a>
                        @endif
                    </div>
                </div>

                {{-- FOLDER 5: LAPORAN & ANALITIK --}}
                @php $isReportActive = request()->routeIs('admin.report.*'); @endphp
                <div>
                    <button onclick="toggleMenu('menu-report', 'icon-report')" class="w-full flex items-center justify-between px-6 py-2 focus:outline-none group">
                        <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors drop-shadow-sm">Laporan & Analitik</span>
                        <i id="icon-report" class="fas fa-chevron-right text-slate-300 dark:text-slate-600 text-[10px] transition-transform duration-300 {{ $isReportActive ? 'rotate-90 text-indigo-500 dark:text-indigo-400 dark:drop-shadow-[0_0_8px_rgba(99,102,241,0.8)]' : 'group-hover:text-indigo-500' }}"></i>
                    </button>
                    
                    <div id="menu-report" class="mt-2 space-y-1 relative {{ $isReportActive ? '' : 'hidden' }}">
                        <div class="absolute left-8 top-0 bottom-0 w-[1px] bg-slate-100 dark:bg-white/[0.08]"></div>

                        <a href="{{ route('admin.report.attendance') }}" class="flex items-center pl-12 pr-6 py-2.5 text-[13px] font-bold transition-all relative {{ request()->routeIs('admin.report.attendance') ? 'text-indigo-600 dark:text-indigo-400 dark:drop-shadow-[0_0_5px_rgba(99,102,241,0.5)]' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]' }}">
                            @if(request()->routeIs('admin.report.attendance')) <div class="absolute left-[31px] w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 border border-white dark:border-black shadow-sm dark:shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div> @endif
                            <i class="fas fa-file-signature w-5 text-center mr-2 opacity-70"></i> Kehadiran
                        </a>

                        <a href="{{ route('admin.report.payroll') }}" class="flex items-center pl-12 pr-6 py-2.5 text-[13px] font-bold transition-all relative {{ request()->routeIs('admin.report.payroll') ? 'text-indigo-600 dark:text-indigo-400 dark:drop-shadow-[0_0_5px_rgba(99,102,241,0.5)]' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]' }}">
                            @if(request()->routeIs('admin.report.payroll')) <div class="absolute left-[31px] w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 border border-white dark:border-black shadow-sm dark:shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div> @endif
                            <i class="fas fa-file-invoice-dollar w-5 text-center mr-2 opacity-70"></i> Laporan Gaji
                        </a>

                        <a href="{{ route('admin.report.employee') }}" class="flex items-center pl-12 pr-6 py-2.5 text-[13px] font-bold transition-all relative {{ request()->routeIs('admin.report.employee') ? 'text-indigo-600 dark:text-indigo-400 dark:drop-shadow-[0_0_5px_rgba(99,102,241,0.5)]' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]' }}">
                            @if(request()->routeIs('admin.report.employee')) <div class="absolute left-[31px] w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 border border-white dark:border-black shadow-sm dark:shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div> @endif
                            <i class="fas fa-chart-pie w-5 text-center mr-2 opacity-70"></i> Demografi Karyawan
                        </a>
                    </div>
                </div>

                {{-- FOLDER 6: PENGATURAN SISTEM --}}
                @if(Auth::user()->role === 'admin')
                @php $isSettingsActive = request()->routeIs('admin.settings.*'); @endphp
                <div>
                    <button onclick="toggleMenu('menu-settings', 'icon-settings')" class="w-full flex items-center justify-between px-6 py-2 focus:outline-none group">
                        <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors drop-shadow-sm">Pengaturan Sistem</span>
                        <i id="icon-settings" class="fas fa-chevron-right text-slate-300 dark:text-slate-600 text-[10px] transition-transform duration-300 {{ $isSettingsActive ? 'rotate-90 text-indigo-500 dark:text-indigo-400 dark:drop-shadow-[0_0_8px_rgba(99,102,241,0.8)]' : 'group-hover:text-indigo-500' }}"></i>
                    </button>
                    
                    <div id="menu-settings" class="mt-2 space-y-1 relative {{ $isSettingsActive ? '' : 'hidden' }}">
                        <div class="absolute left-8 top-0 bottom-0 w-[1px] bg-slate-100 dark:bg-white/[0.08]"></div>

                        <a href="{{ route('admin.settings.role') }}" class="flex items-center pl-12 pr-6 py-2.5 text-[13px] font-bold transition-all relative {{ request()->routeIs('admin.settings.role') ? 'text-indigo-600 dark:text-indigo-400 dark:drop-shadow-[0_0_5px_rgba(99,102,241,0.5)]' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]' }}">
                            @if(request()->routeIs('admin.settings.role')) <div class="absolute left-[31px] w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 border border-white dark:border-black shadow-sm dark:shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div> @endif
                            <i class="fas fa-user-shield w-5 text-center mr-2 opacity-70"></i> Role & Akses
                        </a>

                        <a href="{{ route('admin.settings.company') }}" class="flex items-center pl-12 pr-6 py-2.5 text-[13px] font-bold transition-all relative {{ request()->routeIs('admin.settings.company') ? 'text-indigo-600 dark:text-indigo-400 dark:drop-shadow-[0_0_5px_rgba(99,102,241,0.5)]' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]' }}">
                            @if(request()->routeIs('admin.settings.company')) <div class="absolute left-[31px] w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 border border-white dark:border-black shadow-sm dark:shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div> @endif
                            <i class="fas fa-cogs w-5 text-center mr-2 opacity-70"></i> Konfigurasi Aplikasi
                        </a>

                        <a href="{{ route('admin.settings.audit') }}" class="flex items-center pl-12 pr-6 py-2.5 text-[13px] font-bold transition-all relative {{ request()->routeIs('admin.settings.audit') ? 'text-indigo-600 dark:text-indigo-400 dark:drop-shadow-[0_0_5px_rgba(99,102,241,0.5)]' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]' }}">
                            @if(request()->routeIs('admin.settings.audit')) <div class="absolute left-[31px] w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 border border-white dark:border-black shadow-sm dark:shadow-[0_0_8px_rgba(99,102,241,0.8)]"></div> @endif
                            <i class="fas fa-history w-5 text-center mr-2 opacity-70"></i> Audit Log
                        </a>
                    </div>
                </div>
                @endif

            </nav>

            {{-- TOMBOL KELUAR --}}
            <div class="p-4 border-t border-slate-100 dark:border-ios-dark-border bg-white dark:bg-transparent">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-3 bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-500 hover:text-white dark:hover:bg-rose-500/20 rounded-xl text-xs font-black uppercase tracking-widest transition-colors group border border-transparent dark:border-rose-500/20 dark:shadow-glow-rose">
                        <i class="fas fa-sign-out-alt w-6 text-center group-hover:-translate-x-1 transition-transform"></i> Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- MAIN CONTENT AREA --}}
        <div class="flex-1 flex flex-col overflow-hidden relative z-10">
            
            {{-- HEADER ATAS --}}
            <header class="bg-white dark:bg-black/60 dark:backdrop-blur-xl border-b border-gray-200 dark:border-ios-dark-border h-16 flex items-center justify-between px-4 sm:px-8 z-10 sticky top-0 shadow-sm dark:shadow-none transition-colors duration-300">
                <div class="flex items-center gap-3">
                    {{-- TOMBOL HAMBURGER MOBILE --}}
                    <button onclick="toggleSidebar()" class="lg:hidden w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all shadow-sm dark:shadow-glow-subtle border border-transparent dark:border-indigo-500/20 active:scale-95">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    <h2 class="text-base sm:text-xl font-black text-slate-800 dark:text-white tracking-tight truncate max-w-[200px] sm:max-w-md drop-shadow-sm">@yield('title', 'Overview')</h2>
                </div>
                
                <div class="flex items-center gap-4">
                    
                    {{-- TOMBOL TOGGLE MODE TERANG & MALAM (EFEK GLOW KETIKA DARK MODE) --}}
                    <button id="theme-toggle" type="button" class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-amber-500/10 text-slate-600 dark:text-amber-400 flex items-center justify-center hover:bg-slate-200 dark:hover:bg-amber-500/20 transition shadow-sm dark:shadow-glow-amber border border-transparent dark:border-amber-500/20" title="Ganti Mode Terang / Malam">
                        <i id="theme-toggle-light-icon" class="fas fa-sun text-sm hidden"></i>
                        <i id="theme-toggle-dark-icon" class="fas fa-moon text-sm hidden"></i>
                    </button>

                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-slate-800 dark:text-white leading-none drop-shadow-sm">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] font-bold text-indigo-500 dark:text-indigo-400 mt-1 uppercase tracking-widest">
                            {{ Auth::user()->role === 'admin' ? 'Administrator (HRD)' : 'PIC / Supervisor' }}
                        </p>
                    </div>
                    
                    {{-- PROFIL FOTO / AVATAR --}}
                    <a href="{{ route('admin.profile.index') }}" class="relative group cursor-pointer" title="Pengaturan Profil">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-10 h-10 rounded-[10px] object-cover border-2 border-white dark:border-[#1c1c1e] shadow-sm dark:shadow-glow-subtle group-hover:shadow-md group-hover:-translate-y-0.5 transition-all">
                        @else
                            <div class="w-10 h-10 rounded-[10px] bg-slate-900 dark:bg-indigo-600 text-white flex items-center justify-center font-bold border-2 border-white dark:border-[#1c1c1e] shadow-sm dark:shadow-[0_0_15px_rgba(99,102,241,0.4)] group-hover:shadow-md group-hover:-translate-y-0.5 transition-all text-sm">
                                {{ substr(Auth::user()->name, 0, 2) }}
                            </div>
                        @endif
                        {{-- STATUS ONLINE GLOW --}}
                        <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-emerald-500 border-2 border-white dark:border-[#1c1c1e] rounded-full dark:shadow-glow-emerald"></div>
                    </a>
                </div>
            </header>

            {{-- BACKGROUND EFEK CAHAYA GRADASI KHAS DARK MODE (DI BLAKANG KONTEN) --}}
            <div class="hidden dark:block absolute top-[-20%] left-[-10%] w-[50%] h-[50%] bg-indigo-600/10 blur-[120px] rounded-full pointer-events-none -z-10"></div>
            <div class="hidden dark:block absolute bottom-[-20%] right-[-10%] w-[50%] h-[50%] bg-indigo-800/10 blur-[120px] rounded-full pointer-events-none -z-10"></div>

            <main class="flex-1 overflow-y-auto bg-[#f8fafc] dark:bg-transparent p-4 sm:p-6 md:p-8 scroll-smooth transition-colors duration-300 relative z-0">
                
                {{-- FLASH MESSAGES --}}
                @if(session('success'))
                    <div class="bg-emerald-50 dark:bg-emerald-500/10 border-l-4 border-emerald-500 text-emerald-700 dark:text-emerald-400 p-4 mb-6 rounded-r-xl shadow-sm dark:shadow-glow-emerald dark:backdrop-blur-md flex items-center animate-fadeIn" role="alert">
                        <i class="fas fa-check-circle mr-3 text-xl"></i>
                        <div>
                            <p class="font-black text-xs uppercase tracking-widest drop-shadow-sm">Berhasil</p>
                            <p class="text-sm font-medium mt-0.5">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-rose-50 dark:bg-rose-500/10 border-l-4 border-rose-500 text-rose-700 dark:text-rose-400 p-4 mb-6 rounded-r-xl shadow-sm dark:shadow-glow-rose dark:backdrop-blur-md flex items-center animate-fadeIn" role="alert">
                        <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
                        <div>
                            <p class="font-black text-xs uppercase tracking-widest drop-shadow-sm">Terjadi Kesalahan</p>
                            <p class="text-sm font-medium mt-0.5">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                {{-- KONTEN DINAMIS HALAMAN --}}
                @yield('content')
                
                {{-- FOOTER --}}
                <div class="mt-10 pt-6 border-t border-slate-200 dark:border-white/[0.08] text-center text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                    &copy; {{ date('Y') }} PT. Happy Momo Indonesia. All rights reserved.
                </div>
            </main>
        </div>
    </div>

    {{-- Script Logika Dropdown/Accordion Menu, Hamburger Sidebar, & Dark/Light Mode Switcher --}}
    <script>
        // Toggle Dropdown Menu Samping
        function toggleMenu(menuId, iconId) {
            const menu = document.getElementById(menuId);
            const icon = document.getElementById(iconId);
            
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                icon.classList.add('rotate-90');
                icon.classList.add('text-indigo-500');
                icon.classList.add('dark:text-indigo-400');
            } else {
                menu.classList.add('hidden');
                icon.classList.remove('rotate-90');
                icon.classList.remove('text-indigo-500');
                icon.classList.remove('dark:text-indigo-400');
            }
        }

        // Toggle Sidebar Mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('appSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                setTimeout(() => {
                    backdrop.classList.add('opacity-100');
                }, 10);
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.remove('opacity-100');
                setTimeout(() => {
                    backdrop.classList.add('hidden');
                }, 300);
                document.body.style.overflow = 'auto';
            }
        }

        // Script Tombol Mode Terang & Mode Malam
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        if (document.documentElement.classList.contains('dark')) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        var themeToggleBtn = document.getElementById('theme-toggle');

        themeToggleBtn.addEventListener('click', function() {
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>