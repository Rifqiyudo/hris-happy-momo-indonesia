@extends('admin.layouts.master')
@section('title', 'Executive Dashboard')

@section('content')

{{-- DATA CONTAINER (Untuk dikonsumsi JavaScript ApexCharts) --}}
<div id="dashboard-data" 
    data-chart-dates='@json($chartDates ?? [])'
    data-chart-attendance='@json($chartAttendance ?? [])'
    data-dept-labels='@json($deptLabels ?? [])'
    data-dept-data='@json($deptData ?? [])'
    data-payroll-dates='@json($payrollDates ?? [])'
    data-payroll-totals='@json($payrollTotals ?? [])'
    class="hidden">
</div>

<div class="max-w-7xl mx-auto px-4 space-y-8 pb-20 animate-fadeIn transition-colors duration-300">
    
    {{-- 1. HEADER SECTION & QUICK ACTIONS --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 bg-white dark:bg-transparent dark:card-aesthetic p-8 rounded-[2.5rem] border border-slate-200 shadow-sm relative overflow-hidden transition-all">
        <div class="absolute right-0 top-0 w-64 h-64 bg-indigo-50 dark:bg-indigo-500/10 rounded-bl-[150px] opacity-50 dark:opacity-40 pointer-events-none dark:blur-2xl"></div>
        
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-2">
                <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-300 rounded-lg text-[9px] font-black uppercase tracking-widest border border-indigo-100 dark:border-indigo-400/30 dark:shadow-glow-subtle">
                    Executive Control Center
                </span>
                <span class="text-xs font-bold text-slate-400 dark:text-slate-500">•</span>
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider drop-shadow-sm">PT. Happy Momo Indonesia</span>
            </div>
            <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight drop-shadow-sm">Executive Overview</h1>
        </div>
        
        <div class="flex items-center gap-4 relative z-10 flex-wrap">
            {{-- Widget Jam Server --}}
            <div class="bg-slate-50 dark:bg-black/40 px-5 py-3 rounded-2xl border border-slate-200/80 dark:border-white/[0.08] shadow-sm flex items-center gap-3 backdrop-blur-md">
                <div class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs dark:shadow-glow-subtle border border-transparent dark:border-indigo-500/20">
                    <i class="far fa-clock"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none mb-1">Current Time</p>
                    <p id="server-clock" class="text-sm font-black text-indigo-600 dark:text-indigo-400 tracking-wider font-mono drop-shadow-sm">00:00:00</p>
                </div>
            </div>
            
            @if(Auth::user()->role === 'admin')
            <div class="flex gap-2">
                <a href="{{ route('admin.master.user.import') }}" class="w-12 h-12 bg-white dark:bg-black/40 border border-slate-200 dark:border-white/[0.08] rounded-2xl flex items-center justify-center text-slate-400 dark:text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-300 hover:border-indigo-300 dark:hover:border-indigo-500/50 hover:bg-indigo-50 dark:hover:bg-indigo-500/20 transition-all shadow-sm backdrop-blur-md" title="Import Data Karyawan">
                    <i class="fas fa-file-import text-sm"></i>
                </a>
                <a href="{{ route('admin.transaksi.attendance.scan') }}" target="_blank" class="h-12 px-5 bg-indigo-600 dark:bg-indigo-500/20 text-white dark:text-indigo-300 rounded-2xl flex items-center justify-center gap-2 hover:bg-indigo-700 dark:hover:bg-indigo-500/30 transition-all shadow-lg shadow-indigo-200 dark:shadow-glow-subtle border border-transparent dark:border-indigo-400/30 text-xs font-black uppercase tracking-widest backdrop-blur-md" title="Buka Kiosk Absensi">
                    <i class="fas fa-desktop"></i> Kiosk Absensi
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- 2. NOTIFIKASI PENGALAMAN (CRITICAL ALERTS) --}}
    <div class="space-y-4">
        {{-- Alert Cuti Pending --}}
        @if(isset($cutiPending) && $cutiPending > 0)
        <div class="bg-amber-50 dark:bg-amber-500/10 border-2 border-amber-200/60 dark:border-amber-500/30 rounded-3xl p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm dark:shadow-glow-amber animate-pulse-subtle backdrop-blur-xl">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-500 rounded-2xl flex items-center justify-center text-white shadow-md shadow-amber-200 dark:shadow-glow-amber shrink-0 border border-amber-400/50">
                    <i class="fas fa-bell text-lg"></i>
                </div>
                <div>
                    <h4 class="text-sm font-black text-amber-900 dark:text-amber-400 uppercase tracking-tight drop-shadow-sm">Pengajuan Cuti Menunggu Aksi</h4>
                    <p class="text-xs text-amber-700 dark:text-amber-500/80 font-bold mt-0.5">Ada {{ $cutiPending }} pengajuan cuti karyawan yang memerlukan persetujuan Anda.</p>
                </div>
            </div>
            <a href="{{ route('admin.transaksi.leave.index', ['status' => 'pending']) }}" class="px-5 py-3 bg-amber-600 dark:bg-amber-500/20 text-white dark:text-amber-400 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-amber-700 dark:hover:bg-amber-500/30 transition shadow-lg shadow-amber-200 dark:shadow-glow-amber border border-transparent dark:border-amber-400/40 whitespace-nowrap">
                Tinjau Sekarang
            </a>
        </div>
        @endif

        {{-- Alert Kontrak Berakhir --}}
        @if(isset($expiringToday) && $expiringToday->count() > 0)
        <div class="bg-rose-50 dark:bg-rose-500/10 border-2 border-rose-200/60 dark:border-rose-500/30 rounded-[2.5rem] p-8 shadow-xl shadow-rose-100/30 dark:shadow-glow-rose animate-bounce-subtle backdrop-blur-xl">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                <div class="w-16 h-16 bg-rose-500 rounded-[1.5rem] flex items-center justify-center text-white shadow-lg shadow-rose-200 dark:shadow-glow-rose shrink-0 border border-rose-400/50">
                    <i class="fas fa-file-contract text-2xl"></i>
                </div>
                <div class="flex-1 w-full">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                        <h2 class="text-base font-black text-rose-700 dark:text-rose-400 uppercase tracking-tight drop-shadow-sm">Peringatan: Kontrak Kerja Berakhir Hari Ini!</h2>
                        <span class="bg-rose-600 text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest self-start sm:self-auto shadow-sm dark:shadow-glow-rose border border-rose-400/50">{{ $expiringToday->count() }} Orang</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($expiringToday as $emp)
                        <div class="bg-white dark:bg-black/40 p-3.5 rounded-2xl border border-rose-100 dark:border-rose-500/20 flex items-center gap-3.5 shadow-sm group hover:border-rose-400 dark:hover:border-rose-400/60 transition-all backdrop-blur-md">
                            <img src="{{ $emp->avatar_url }}" class="w-11 h-11 rounded-xl object-cover border border-slate-100 dark:border-[#1c1c1e]">
                            <div class="min-w-0">
                                <h4 class="text-xs font-black text-slate-800 dark:text-slate-200 uppercase truncate leading-tight">{{ $emp->name }}</h4>
                                <p class="text-[9px] font-bold text-rose-500 dark:text-rose-400 uppercase tracking-widest mt-0.5">{{ $emp->nik }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- 3. SUMMARY CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Total Staff --}}
        <div class="bg-white dark:bg-transparent dark:card-aesthetic p-6 rounded-[2.5rem] border border-slate-200 shadow-sm hover:shadow-lg transition-all duration-300 group relative overflow-hidden">
            <div class="hidden dark:block absolute -right-4 -bottom-4 w-20 h-20 bg-indigo-500/10 rounded-full blur-2xl"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl group-hover:bg-indigo-600 group-hover:text-white dark:group-hover:bg-indigo-500/30 transition-all shadow-sm dark:shadow-glow-subtle border border-transparent dark:border-indigo-500/20">
                    <i class="fas fa-users"></i>
                </div>
                <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 px-3 py-1 rounded-lg border border-emerald-100 dark:border-emerald-500/30 uppercase tracking-widest">Aktif</span>
            </div>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase font-black tracking-widest mb-1 relative z-10">Total Staff</p>
            <h3 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight drop-shadow-sm relative z-10">{{ $totalKaryawan ?? 0 }}</h3>
        </div>

        {{-- Kehadiran --}}
        <div class="bg-white dark:bg-transparent dark:card-aesthetic p-6 rounded-[2.5rem] border border-slate-200 shadow-sm hover:shadow-lg transition-all duration-300 border-l-4 border-l-emerald-500 dark:border-l-emerald-400 relative overflow-hidden">
            <div class="hidden dark:block absolute -right-4 -bottom-4 w-20 h-20 bg-emerald-500/10 rounded-full blur-2xl"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl shadow-sm dark:shadow-glow-emerald border border-transparent dark:border-emerald-500/20">
                    <i class="fas fa-fingerprint"></i>
                </div>
                <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest flex items-center gap-1.5 drop-shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-pulse shadow-glow-emerald border border-emerald-300/50"></span> Live
                </span>
            </div>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase font-black tracking-widest mb-1 relative z-10">Hadir Hari Ini</p>
            <h3 class="text-3xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight drop-shadow-sm relative z-10">{{ $hadirHariIni ?? 0 }}</h3>
        </div>

        {{-- Terlambat --}}
        <div class="bg-white dark:bg-transparent dark:card-aesthetic p-6 rounded-[2.5rem] border border-slate-200 shadow-sm hover:shadow-lg transition-all duration-300 border-l-4 border-l-amber-500 dark:border-l-amber-400 relative overflow-hidden">
            <div class="hidden dark:block absolute -right-4 -bottom-4 w-20 h-20 bg-amber-500/10 rounded-full blur-2xl"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl shadow-sm dark:shadow-glow-amber border border-transparent dark:border-amber-500/20">
                    <i class="fas fa-clock"></i>
                </div>
                <span class="text-[10px] font-black text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 px-3 py-1 rounded-lg border border-amber-100 dark:border-amber-500/30 uppercase tracking-widest">Minutes</span>
            </div>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase font-black tracking-widest mb-1 relative z-10">Terlambat</p>
            <h3 class="text-3xl font-black text-amber-600 dark:text-amber-400 tracking-tight drop-shadow-sm relative z-10">{{ $terlambat ?? 0 }}</h3>
        </div>

        {{-- Mangkir/Belum Absen --}}
        <div class="bg-slate-900 dark:bg-transparent dark:card-aesthetic p-6 rounded-[2.5rem] shadow-xl shadow-slate-200 dark:shadow-[0_10px_40px_-10px_rgba(0,0,0,0.8)] border border-slate-800 dark:border-white/[0.08] text-white relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-rose-500/10 dark:bg-rose-500/20 rounded-full blur-xl pointer-events-none"></div>
            <div class="flex items-center justify-between mb-4 relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-white/10 dark:bg-rose-500/10 text-rose-400 flex items-center justify-center text-xl backdrop-blur-sm dark:shadow-glow-rose border border-transparent dark:border-rose-500/20">
                    <i class="fas fa-user-slash"></i>
                </div>
            </div>
            <p class="text-[10px] text-slate-400 dark:text-slate-400 uppercase font-black tracking-widest mb-1 relative z-10">Belum Absen</p>
            <h3 class="text-3xl font-black text-white tracking-tight drop-shadow-sm relative z-10">{{ $mangkir ?? 0 }}</h3>
        </div>
    </div>

    {{-- 4. ANALYTICS (Charts & Activity) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Area Chart Kehadiran --}}
        <div class="lg:col-span-2 bg-white dark:bg-transparent dark:card-aesthetic p-8 rounded-[2.5rem] shadow-sm border border-slate-200 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-black text-slate-800 dark:text-white uppercase text-xs tracking-[0.2em] flex items-center gap-2 drop-shadow-sm">
                        <i class="fas fa-chart-area text-indigo-500 dark:text-indigo-400"></i> Statistik Kehadiran
                    </h3>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider mt-1">Tren kehadiran karyawan dalam 7 hari terakhir</p>
                </div>
            </div>
            {{-- Elemen Chart (Diberi class tambahan agar ApexCharts bisa deteksi mode) --}}
            <div id="attendanceChart" class="w-full h-[320px] dark:opacity-90"></div>
        </div>

        {{-- Activity Feed --}}
        <div class="lg:col-span-1 bg-white dark:bg-transparent dark:card-aesthetic p-8 rounded-[2.5rem] shadow-sm border border-slate-200 flex flex-col relative overflow-hidden">
            <div class="hidden dark:block absolute -right-10 top-0 w-32 h-32 bg-emerald-500/5 rounded-full blur-3xl pointer-events-none"></div>
            <div class="flex items-center justify-between mb-6 relative z-10">
                <h3 class="font-black text-slate-800 dark:text-white uppercase text-xs tracking-[0.2em] drop-shadow-sm">Recent Activity</h3>
                <span class="w-2 h-2 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-ping shadow-glow-emerald border border-emerald-300/50"></span>
            </div>
            <div class="space-y-4 overflow-y-auto max-h-[320px] pr-2 custom-scrollbar flex-1 relative z-10">
                @forelse($recentAttendances as $log)
                <div class="flex items-center gap-4 p-3 rounded-2xl hover:bg-slate-50 dark:hover:bg-white/[0.04] transition-colors group">
                    <div class="relative shrink-0">
                        <img src="{{ $log->user->avatar_url }}" class="w-11 h-11 rounded-2xl object-cover border-2 border-white dark:border-[#1c1c1e] shadow-sm">
                        <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 rounded-full border-2 border-white dark:border-[#1c1c1e] {{ $log->is_late ? 'bg-amber-500 dark:shadow-glow-amber' : 'bg-emerald-500 dark:shadow-glow-emerald' }}" title="{{ $log->is_late ? 'Terlambat' : 'Tepat Waktu' }}"></div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-black text-slate-800 dark:text-slate-200 truncate uppercase leading-tight mb-1 drop-shadow-sm">{{ $log->user->name }}</p>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 font-bold tracking-wider">{{ $log->clock_in }} &bull; <span class="{{ $log->is_late ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">{{ $log->status }}</span></p>
                    </div>
                </div>
                @empty
                <div class="text-center py-16">
                    <div class="w-12 h-12 bg-slate-50 dark:bg-black/30 text-slate-300 dark:text-slate-600 rounded-2xl flex items-center justify-center text-xl mx-auto mb-3 border border-transparent dark:border-white/[0.05]">
                        <i class="far fa-folder-open"></i>
                    </div>
                    <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest italic">No activity today</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- 5. PAYROLL TREND (Admin Only) --}}
    @if(Auth::user()->role === 'admin' && !empty($payrollTotals))
    <div class="bg-white dark:bg-transparent dark:card-aesthetic p-8 rounded-[2.5rem] shadow-sm border border-slate-200">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-black text-slate-800 dark:text-white uppercase text-xs tracking-[0.2em] drop-shadow-sm">Payroll Expenditure Trend</h3>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider mt-1">Grafik pengeluaran total gaji bulanan perusahaan</p>
            </div>
        </div>
        <div id="payrollChart" class="w-full h-[260px] dark:opacity-90"></div>
    </div>
    @endif

</div>

<style>
    /* Styling Scrollbar Khusus di dalam Card (Activity Feed) */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
    
    @keyframes bounce-subtle {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }
    .animate-bounce-subtle { animation: bounce-subtle 4s infinite ease-in-out; }

    @keyframes pulse-subtle {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.85; }
    }
    .animate-pulse-subtle { animation: pulse-subtle 2s infinite ease-in-out; }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // 1. Digital Clock
    function updateClock() {
        const now = new Date();
        const clock = document.getElementById('server-clock');
        if(clock) clock.innerText = now.toLocaleTimeString('id-ID', { hour12: false });
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Deteksi Tema Berjalan untuk Menyesuaikan Teks Grafik
    const isDarkMode = document.documentElement.classList.contains('dark');
    const chartTextColor = isDarkMode ? '#94a3b8' : '#94a3b8'; // Label Color
    const chartGridColor = isDarkMode ? 'rgba(255,255,255,0.06)' : '#f1f5f9'; // Grid Line
    const chartGlowColor = isDarkMode ? '#818cf8' : '#6366f1'; 
    const tooltipTheme = isDarkMode ? 'dark' : 'light';

    // 2. Data Parsing
    const container = document.getElementById('dashboard-data');
    if(container) {
        const chartDates = JSON.parse(container.dataset.chartDates || '[]');
        const chartAttendance = JSON.parse(container.dataset.chartAttendance || '[]');
        const payrollDates = JSON.parse(container.dataset.payrollDates || '[]');
        const payrollTotals = JSON.parse(container.dataset.payrollTotals || '[]');

        // 3. Attendance Chart (ApexCharts)
        if(document.querySelector("#attendanceChart")) {
            new ApexCharts(document.querySelector("#attendanceChart"), {
                series: [{ name: 'Karyawan Hadir', data: chartAttendance }],
                chart: { type: 'area', height: 320, toolbar: { show: false }, zoom: { enabled: false }, fontFamily: 'inherit', background: 'transparent' },
                colors: [chartGlowColor],
                stroke: { curve: 'smooth', width: 3 },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.5, opacityTo: 0.05 } },
                xaxis: { 
                    categories: chartDates, 
                    labels: { style: { colors: chartTextColor, fontWeight: 'bold', fontSize: '10px' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: { labels: { style: { colors: chartTextColor, fontWeight: 'bold', fontSize: '10px' } } },
                grid: { borderColor: chartGridColor, strokeDashArray: 4 },
                dataLabels: { enabled: false },
                tooltip: { theme: tooltipTheme }
            }).render();
        }

        // 4. Payroll Chart
        if(document.querySelector("#payrollChart") && payrollTotals.length > 0) {
            new ApexCharts(document.querySelector("#payrollChart"), {
                series: [{ name: 'Total Pengeluaran Gaji', data: payrollTotals }],
                chart: { type: 'bar', height: 260, toolbar: { show: false }, fontFamily: 'inherit', background: 'transparent' },
                plotOptions: { bar: { borderRadius: 8, columnWidth: '30%' } },
                colors: [isDarkMode ? '#34d399' : '#10b981'],
                xaxis: { 
                    categories: payrollDates,
                    labels: { style: { colors: chartTextColor, fontWeight: 'bold', fontSize: '10px' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: { labels: { style: { colors: chartTextColor, fontWeight: 'bold', fontSize: '10px' } } },
                grid: { borderColor: chartGridColor, strokeDashArray: 4 },
                dataLabels: { enabled: false },
                tooltip: { theme: tooltipTheme }
            }).render();
        }
    }
</script>
@endsection