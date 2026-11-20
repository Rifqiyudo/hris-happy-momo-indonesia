@extends('employee.layouts.app')

@section('title', 'Dashboard Karyawan - Happy Momo')

@section('content')

{{-- DATA CONTAINER UNTUK JAVASCRIPT --}}
<div id="attendance-data" 
    data-has-clock-in="{{ isset($todayAttendance) && $todayAttendance ? 'true' : 'false' }}"
    data-has-clock-out="{{ isset($todayAttendance) && $todayAttendance && $todayAttendance->clock_out ? 'true' : 'false' }}"
    data-server-time="{{ now()->format('H:i:s') }}"
    class="hidden">
</div>

{{-- WRAPPER UTAMA --}}
<div class="max-w-md mx-auto bg-slate-50 dark:bg-slate-950 min-h-screen shadow-2xl dark:shadow-none relative pb-28 overflow-x-hidden font-sans transition-colors duration-300">

    {{-- 1. HEADER & GREETING --}}
    {{-- z-[100] memastikan header ini menang mutlak dari semua lapisan --}}
    <div class="px-6 pt-10 pb-6 flex justify-between items-center bg-slate-50 dark:bg-slate-950 relative z-[100] transition-colors duration-300">
        <div class="flex items-center gap-4 min-w-0">
            <div class="relative shrink-0">
                <img src="{{ Auth::user()->avatar ? asset('storage/'.Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=f8fafc&color=4f46e5' }}" 
                     class="w-12 h-12 rounded-full object-cover border-2 border-white dark:border-slate-800 shadow-sm">
                <span class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-emerald-500 border-2 border-white dark:border-slate-950 rounded-full"></span>
            </div>
            <div class="min-w-0">
                <h1 class="text-base font-black text-slate-800 dark:text-white tracking-tight truncate">
                    Halo, {{ strtoupper(explode(' ', Auth::user()->name)[0]) }}!
                </h1>
                <p class="text-[9px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest mt-0.5 truncate">
                    {{ Auth::user()->position->name ?? 'STAFF' }} • {{ Auth::user()->unit_kerja ?? 'PUSAT' }}
                </p>
            </div>
        </div>
        
        {{-- [BARU] Grup Tombol Mode Terang/Malam & Logout --}}
        <div class="flex items-center gap-2 shrink-0 relative z-[120]">
            {{-- [BARU] Tombol Switcher Mode Terang / Mode Malam --}}
            <button type="button"
                    id="btn-theme-toggle"
                    onclick="toggleDashboardDarkMode(event)"
                    class="w-10 h-10 bg-white dark:bg-slate-900 text-slate-600 dark:text-amber-400 rounded-full flex items-center justify-center shadow-sm dark:shadow-none border border-slate-200/80 dark:border-slate-800 active:scale-90 transition hover:border-indigo-300 dark:hover:border-indigo-500/50 cursor-pointer pointer-events-auto"
                    title="Ganti Mode Terang / Malam">
                <i class="fas fa-moon text-sm theme-icon-moon"></i>
                <i class="fas fa-sun text-sm theme-icon-sun theme-hidden" style="display: none;"></i>
            </button>

            {{-- TOMBOL LOGOUT (Langsung dibungkus Form agar tidak meleset) --}}
            <form action="{{ route('logout') }}" method="POST" id="form-logout-header" class="m-0 p-0">
                @csrf
                <button type="button" onclick="keluarAplikasi()" class="w-10 h-10 bg-rose-50 dark:bg-rose-500/15 text-rose-500 dark:text-rose-400 rounded-full flex items-center justify-center shadow-sm dark:shadow-none border border-rose-100 dark:border-rose-500/30 active:scale-90 transition hover:bg-rose-500 dark:hover:bg-rose-600 hover:text-white dark:hover:text-white cursor-pointer pointer-events-auto" title="Keluar">
                    <i class="fas fa-power-off text-sm"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- 2. KARTU ABSENSI UTAMA --}}
    <div class="px-6 mb-6">
        <div class="bg-gradient-to-br from-indigo-600 via-indigo-500 to-purple-600 dark:from-indigo-600 dark:via-indigo-700 dark:to-purple-900 rounded-[2.5rem] p-8 text-white shadow-xl shadow-indigo-200 dark:shadow-indigo-950/70 relative overflow-hidden text-center border-4 border-white/40 dark:border-indigo-400/20 z-10">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/20 dark:bg-white/10 rounded-full -mr-16 -mt-16 blur-2xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-indigo-900/40 dark:bg-purple-950/60 rounded-full -ml-10 -mb-10 blur-xl pointer-events-none"></div>
            
            <div class="relative z-20 space-y-1 mb-8 mt-2">
                <p class="text-indigo-100 text-[10px] font-black uppercase tracking-[0.3em] drop-shadow-sm">{{ now()->translatedFormat('l, d F Y') }}</p>
                <h2 id="realtime-clock" class="text-5xl font-black tracking-widest tabular-nums drop-shadow-lg text-white">
                    {{ now()->format('H:i:s') }}
                </h2>
            </div>

            <div class="relative z-20">
                @if(isset($todayAttendance) && $todayAttendance && $todayAttendance->clock_out)
                    <div class="bg-white/20 dark:bg-slate-950/50 backdrop-blur-md rounded-2xl p-4 border border-white/30 dark:border-white/15 text-center shadow-inner">
                        <i class="fas fa-check-circle text-2xl mb-1 text-emerald-300"></i>
                        <p class="text-xs font-black uppercase tracking-widest text-white">Tugas Selesai</p>
                    </div>
                @elseif(isset($todayAttendance) && $todayAttendance)
                    <a href="{{ route('employee.attendance.create') }}" class="block w-full bg-rose-500 hover:bg-rose-600 text-white font-black text-sm tracking-widest uppercase py-4 rounded-2xl shadow-lg dark:shadow-rose-950/60 active:scale-95 transition flex items-center justify-center gap-3">
                        <i class="fas fa-sign-out-alt text-lg"></i> Absen Pulang
                    </a>
                @else
                    <a href="{{ route('employee.attendance.create') }}" class="block w-full bg-white dark:bg-slate-950/90 text-indigo-600 dark:text-white border border-transparent dark:border-indigo-400/30 font-black text-sm tracking-widest uppercase py-4 rounded-2xl shadow-lg dark:shadow-none hover:shadow-xl active:scale-95 transition flex items-center justify-center gap-3 hover:bg-slate-50 dark:hover:bg-slate-900">
                        <i class="fas fa-fingerprint text-lg text-indigo-600 dark:text-indigo-400"></i> Absen Masuk
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- 3. STATS GRID --}}
    <div class="px-6 mb-8 relative z-20">
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-white dark:bg-slate-900 py-4 px-2 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm dark:shadow-none text-center transition-colors">
                <p class="text-[9px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest mb-1">Hadir</p>
                <p class="text-xl font-black text-slate-800 dark:text-white tabular-nums">{{ $stats['hadir'] ?? 0 }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 py-4 px-2 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm dark:shadow-none text-center transition-colors">
                <p class="text-[9px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest mb-1">Cuti</p>
                <p class="text-xl font-black text-indigo-600 dark:text-indigo-400 tabular-nums">{{ $stats['sisa_cuti'] ?? 0 }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 py-4 px-2 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm dark:shadow-none text-center transition-colors">
                <p class="text-[9px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest mb-1">Telat</p>
                <p class="text-xl font-black text-rose-500 dark:text-rose-400 tabular-nums">{{ $stats['terlambat'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- 4. MENU PINTASAN --}}
    <div class="px-8 mb-10 relative z-20">
        <div class="flex justify-between items-start">
            
            <a href="{{ route('employee.history.index') ?? '#' }}" class="flex flex-col items-center gap-2 group">
                <div class="w-14 h-14 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 group-hover:border-blue-300 dark:group-hover:border-blue-500/50 rounded-[1.25rem] flex items-center justify-center text-blue-500 dark:text-blue-400 text-xl shadow-sm dark:shadow-none active:scale-95 transition group-hover:shadow-md">
                    <i class="fas fa-history"></i>
                </div>
                <span class="text-[9px] font-black text-slate-500 dark:text-slate-300 uppercase tracking-widest">Riwayat</span>
            </a>

            <a href="{{ route('employee.leave.index') ?? '#' }}" class="flex flex-col items-center gap-2 group relative">
                <div class="w-14 h-14 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 group-hover:border-purple-300 dark:group-hover:border-purple-500/50 rounded-[1.25rem] flex items-center justify-center text-purple-500 dark:text-purple-400 text-xl shadow-sm dark:shadow-none active:scale-95 transition relative group-hover:shadow-md">
                    <i class="fas fa-envelope-open-text"></i>
                    @if(isset($hasApprovedLeave) && $hasApprovedLeave)
                    <span class="absolute -top-1 -right-1 flex h-3.5 w-3.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-amber-500 border-2 border-white dark:border-slate-900"></span>
                    </span>
                    @endif
                </div>
                <span class="text-[9px] font-black text-slate-500 dark:text-slate-300 uppercase tracking-widest">Izin</span>
            </a>

            <a href="{{ route('employee.overtime.index') ?? '#' }}" class="flex flex-col items-center gap-2 group">
                <div class="w-14 h-14 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 group-hover:border-amber-300 dark:group-hover:border-amber-500/50 rounded-[1.25rem] flex items-center justify-center text-orange-500 dark:text-amber-400 text-xl shadow-sm dark:shadow-none active:scale-95 transition group-hover:shadow-md">
                    <i class="fas fa-clock"></i>
                </div>
                <span class="text-[9px] font-black text-slate-500 dark:text-slate-300 uppercase tracking-widest">Lembur</span>
            </a>

            <a href="{{ route('employee.reimbursement.index') ?? '#' }}" class="flex flex-col items-center gap-2 group">
                <div class="w-14 h-14 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 group-hover:border-emerald-300 dark:group-hover:border-emerald-500/50 rounded-[1.25rem] flex items-center justify-center text-emerald-500 dark:text-emerald-400 text-xl shadow-sm dark:shadow-none active:scale-95 transition group-hover:shadow-md">
                    <i class="fas fa-receipt"></i>
                </div>
                <span class="text-[9px] font-black text-slate-500 dark:text-slate-300 uppercase tracking-widest">Klaim</span>
            </a>

        </div>
    </div>

    {{-- 5. PENGUMUMAN --}}
    <div class="px-6 space-y-4 relative z-20">
        <div class="flex justify-between items-end border-b border-slate-200 dark:border-slate-800 pb-2 mb-4">
            <h3 class="text-[11px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest">Info Perusahaan</h3>
            <span class="text-[9px] font-black text-indigo-500 dark:text-indigo-400 uppercase tracking-widest">Divisi: {{ Auth::user()->department->name ?? 'UMUM' }}</span>
        </div>

        @if(isset($announcements) && $announcements->count() > 0)
            @foreach($announcements as $item)
            <div class="bg-amber-50/50 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/25 rounded-[2rem] p-6 relative overflow-hidden shadow-sm dark:shadow-none transition-colors">
                <div class="absolute -right-4 -bottom-4 opacity-10 text-7xl text-amber-500 dark:text-amber-400 pointer-events-none">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                
                <div class="relative z-10">
                    <div class="flex items-start gap-4 mb-3">
                        <div class="w-10 h-10 bg-amber-50 dark:bg-amber-500/20 rounded-full flex items-center justify-center text-amber-500 dark:text-amber-400 shadow-sm dark:shadow-none shrink-0 border border-amber-100 dark:border-amber-500/30">
                            <i class="fas fa-exclamation-triangle text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tight leading-snug">{{ $item->title }}</h4>
                            <span class="text-[9px] font-black text-amber-500 dark:text-amber-400 uppercase italic mt-0.5 block">{{ $item->created_at->translatedFormat('d F Y') }} • {{ $item->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    
                    <p class="text-xs text-slate-600 dark:text-slate-300 font-medium leading-relaxed mt-2 ml-14">
                        {{ $item->content }}
                    </p>
                </div>
            </div>
            @endforeach
        @else
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-8 flex flex-col items-center justify-center text-center opacity-60 dark:opacity-90 shadow-sm dark:shadow-none transition-colors">
                <div class="w-12 h-12 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-400 dark:text-slate-500 mb-3">
                    <i class="fas fa-bell-slash text-xl"></i>
                </div>
                <h4 class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Belum Ada Info</h4>
            </div>
        @endif
    </div>
    
    {{-- 6. NAVIGASI BAWAH --}}
    <div class="fixed bottom-0 left-0 right-0 bg-white/90 dark:bg-slate-900/95 backdrop-blur-xl border-t border-slate-100 dark:border-slate-800 px-6 py-2 flex justify-between items-end z-50 shadow-[0_-10px_40px_-15px_rgba(0,0,0,0.05)] dark:shadow-none rounded-t-3xl md:max-w-md md:mx-auto transition-colors duration-300">
        
        <a href="{{ route('employee.dashboard') }}" class="flex flex-col items-center text-indigo-600 dark:text-indigo-400 group w-12 pb-2">
            <i class="fas fa-home text-xl mb-1.5 transition-transform group-active:scale-90"></i>
            <span class="text-[9px] font-black tracking-widest">Beranda</span>
        </a>

        <a href="{{ route('employee.message.index') ?? '#' }}" class="flex flex-col items-center text-slate-400 dark:text-slate-500 hover:text-indigo-500 dark:hover:text-indigo-400 transition-colors group relative w-12 pb-2">
            <div class="relative">
                <i class="fas fa-comment-dots text-xl mb-1.5 transition-transform group-active:scale-90"></i>
                
                @php 
                    $hasUnreadMsg = \App\Models\ChatMessage::where('is_read', false)
                        ->where('sender_id', '!=', auth()->id())
                        ->exists(); 
                @endphp

                @if($hasUnreadMsg)
                <span class="absolute -top-1 -right-1 flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500 border border-white dark:border-slate-900"></span>
                </span>
                @endif
            </div>
            <span class="text-[9px] font-black tracking-widest">Pesan</span>
        </a>

        <div class="relative -top-6">
            <a href="{{ route('employee.attendance.create') ?? '#' }}" class="w-16 h-16 bg-indigo-500 dark:bg-indigo-600 rounded-[1.5rem] flex items-center justify-center text-white text-3xl shadow-xl shadow-indigo-200 dark:shadow-indigo-950/90 border-4 border-white dark:border-slate-950 active:scale-95 transition-transform hover:bg-indigo-600 dark:hover:bg-indigo-500">
                <i class="fas fa-fingerprint"></i>
            </a>
        </div>

        <a href="{{ route('employee.history.index') ?? '#' }}" class="flex flex-col items-center text-slate-400 dark:text-slate-500 hover:text-indigo-500 dark:hover:text-indigo-400 transition-colors group w-12 pb-2">
            <i class="fas fa-file-invoice text-xl mb-1.5 transition-transform group-active:scale-90"></i>
            <span class="text-[9px] font-black tracking-widest">Riwayat</span>
        </a>

        <a href="{{ route('employee.profile.index') ?? '#' }}" class="flex flex-col items-center text-slate-400 dark:text-slate-500 hover:text-indigo-500 dark:hover:text-indigo-400 transition-colors group w-12 pb-2">
            <i class="fas fa-user text-xl mb-1.5 transition-transform group-active:scale-90"></i>
            <span class="text-[9px] font-black tracking-widest">Profil</span>
        </a>
    </div>

</div>

{{-- SCRIPT DITETAPKAN SECARA LANGSUNG --}}
{{-- Tanpa @push agar 100% dipaksa dieksekusi browser apa pun kondisi layoutnya --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // [BARU] Kontroler Dark Mode Terintegrasi & Tahan Bentrok
    function toggleDashboardDarkMode(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        if (typeof window.toggleEmployeeDarkMode === 'function') {
            window.toggleEmployeeDarkMode();
            return;
        }
        const html = document.documentElement;
        const willBeDark = !html.classList.contains('dark');
        html.classList.toggle('dark', willBeDark);
        html.setAttribute('data-theme', willBeDark ? 'dark' : 'light');
        localStorage.setItem('employee_theme', willBeDark ? 'dark' : 'light');
        localStorage.setItem('theme', willBeDark ? 'dark' : 'light');

        document.querySelectorAll('.theme-icon-sun').forEach(el => {
            el.classList.toggle('theme-hidden', !willBeDark);
            el.style.display = willBeDark ? 'inline-block' : 'none';
        });
        document.querySelectorAll('.theme-icon-moon').forEach(el => {
            el.classList.toggle('theme-hidden', willBeDark);
            el.style.display = willBeDark ? 'none' : 'inline-block';
        });
    }

    (function syncInitialDashboardTheme() {
        const isDark = document.documentElement.classList.contains('dark');
        document.querySelectorAll('.theme-icon-sun').forEach(el => {
            el.classList.toggle('theme-hidden', !isDark);
            el.style.display = isDark ? 'inline-block' : 'none';
        });
        document.querySelectorAll('.theme-icon-moon').forEach(el => {
            el.classList.toggle('theme-hidden', isDark);
            el.style.display = isDark ? 'none' : 'inline-block';
        });
    })();

    // 1. Logika Jam Realtime Format 24 Jam
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' }).replace(/\./g, ':');
        const clockElement = document.getElementById('realtime-clock');
        if(clockElement) clockElement.textContent = timeString;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // 2. Logika Pop-Up Logout yang Tahan Banting
    function keluarAplikasi() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Keluar Aplikasi?',
                text: "Sesi kerja Anda saat ini akan berakhir.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#f43f5e',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-[2rem]' }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-logout-header').submit();
                }
            });
        } else {
            // Jika CDN SweetAlert diblokir koneksi
            if (confirm('Keluar Aplikasi? Sesi kerja Anda saat ini akan berakhir.')) {
                document.getElementById('form-logout-header').submit();
            }
        }
    }
</script>

@endsection