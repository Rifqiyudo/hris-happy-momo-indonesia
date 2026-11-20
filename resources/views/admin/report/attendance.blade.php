@extends('admin.layouts.master')
@section('title', 'Laporan Kehadiran Karyawan')

@section('content')
<div class="space-y-6 animate-fadeIn">
    
    {{-- HEADER & FILTER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm dark:shadow-none relative z-30">
        <div>
            <h2 class="font-black text-slate-800 dark:text-white text-xl tracking-tight">Laporan Kehadiran</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Analitik dan rekapitulasi tren presensi karyawan harian dan bulanan.</p>
        </div>
        
        <form method="GET" action="{{ route('admin.report.attendance') }}" id="attendanceFilterForm" class="flex flex-wrap items-center gap-3">
            @php
                $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                // [BARU] Normalisasi nilai bulan & tahun aktif agar selalu aman
                $activeMonth = (int) ($month ?? request('month', date('n')));
                $activeYear  = (int) ($year ?? request('year', date('Y')));
            @endphp

            {{-- KAPSUL FILTER BULAN & TAHUN (ANTI-BOCOR DARK/LIGHT MODE) --}}
            <div class="relative flex items-center bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-indigo-500/60 rounded-2xl focus-within:ring-2 focus-within:ring-indigo-500 dark:shadow-[0_0_0_3px_rgba(99,102,241,0.12)] transition-all">
                <i class="fas fa-calendar-alt text-slate-400 dark:text-slate-400 pl-4 text-sm"></i>

                {{-- [BARU] TRIGGER CUSTOM DROPDOWN BULAN (Menyatu dengan Tema Gelap & Terang) --}}
                <div class="relative" id="monthDropdownWrapper">
                    <button type="button" id="monthDropdownBtn" onclick="toggleReportDropdown('month')"
                        class="flex items-center gap-2 text-sm font-bold text-slate-700 dark:text-white px-3 py-2.5 outline-none cursor-pointer hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        <span id="monthDropdownLabel">{{ $months[$activeMonth - 1] ?? 'Januari' }}</span>
                        <i id="monthDropdownIcon" class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200"></i>
                    </button>

                    {{-- POPUP MENU BULAN --}}
                    <div id="monthDropdownMenu"
                        class="hidden absolute left-0 mt-2 w-48 max-h-64 overflow-y-auto report-scrollbar rounded-2xl bg-white dark:bg-[#13151d] border border-slate-200 dark:border-slate-800 shadow-2xl shadow-slate-900/15 dark:shadow-black/80 py-1.5 z-50">
                        @foreach($months as $index => $namaBulan)
                            @php $mVal = $index + 1; @endphp
                            <button type="button"
                                onclick="selectReportOption('month', '{{ $mVal }}', '{{ $namaBulan }}')"
                                data-value="{{ $mVal }}"
                                class="month-option-item w-full px-4 py-2 text-left text-xs flex items-center justify-between transition-colors cursor-pointer {{ $activeMonth == $mVal ? 'bg-indigo-600 text-white font-black' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/90 font-bold' }}">
                                <span>{{ $namaBulan }}</span>
                                <i class="fas fa-check text-[10px] {{ $activeMonth == $mVal ? '' : 'hidden' }}"></i>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- SELECT BULAN ASLI (Tetap Dipertahankan & Tersinkronisasi) --}}
                <select name="month" id="nativeMonthSelect" class="sr-only dark:[color-scheme:dark] bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-100">
                    @foreach($months as $index => $namaBulan)
                        <option value="{{ $index + 1 }}" class="bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-100" {{ $activeMonth == ($index + 1) ? 'selected' : '' }}>
                            {{ $namaBulan }}
                        </option>
                    @endforeach
                </select>
                
                <div class="w-px h-5 bg-slate-300 dark:bg-slate-700"></div>

                {{-- [BARU] TRIGGER CUSTOM DROPDOWN TAHUN --}}
                <div class="relative" id="yearDropdownWrapper">
                    <button type="button" id="yearDropdownBtn" onclick="toggleReportDropdown('year')"
                        class="flex items-center gap-2 text-sm font-bold text-slate-700 dark:text-white px-3.5 py-2.5 outline-none cursor-pointer hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        <span id="yearDropdownLabel">{{ $activeYear }}</span>
                        <i id="yearDropdownIcon" class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200"></i>
                    </button>

                    {{-- POPUP MENU TAHUN --}}
                    <div id="yearDropdownMenu"
                        class="hidden absolute right-0 mt-2 w-32 max-h-60 overflow-y-auto report-scrollbar rounded-2xl bg-white dark:bg-[#13151d] border border-slate-200 dark:border-slate-800 shadow-2xl shadow-slate-900/15 dark:shadow-black/80 py-1.5 z-50">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <button type="button"
                                onclick="selectReportOption('year', '{{ $y }}', '{{ $y }}')"
                                data-value="{{ $y }}"
                                class="year-option-item w-full px-4 py-2 text-left text-xs flex items-center justify-between transition-colors cursor-pointer {{ $activeYear == $y ? 'bg-indigo-600 text-white font-black' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/90 font-bold' }}">
                                <span>{{ $y }}</span>
                                <i class="fas fa-check text-[10px] {{ $activeYear == $y ? '' : 'hidden' }}"></i>
                            </button>
                        @endfor
                    </div>
                </div>
                
                {{-- SELECT TAHUN ASLI (Tetap Dipertahankan & Tersinkronisasi) --}}
                <select name="year" id="nativeYearSelect" class="sr-only dark:[color-scheme:dark] bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-100">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" class="bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-100" {{ $activeYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-200 dark:hover:shadow-indigo-950/50 transition-all flex items-center gap-2 cursor-pointer">
                <i class="fas fa-filter"></i> Terapkan
            </button>

            {{-- [BARU] Tombol Reset Cepat ke Bulan Berjalan jika filter sedang melihat bulan lain --}}
            @if($activeMonth != date('n') || $activeYear != date('Y'))
                <a href="{{ route('admin.report.attendance') }}"
                   title="Kembali ke Bulan Ini"
                   class="px-4 py-2.5 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-black uppercase tracking-wider transition-all flex items-center gap-1.5">
                    <i class="fas fa-undo text-[10px]"></i> Bulan Ini
                </a>
            @endif
        </form>
    </div>

    {{-- SUMMARY CARDS (Statistik Berdasarkan Filter Bulan) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm dark:shadow-none flex items-center gap-5 group hover:border-emerald-200 dark:hover:border-emerald-500/40 transition-colors">
            <div class="w-16 h-16 rounded-[1.25rem] bg-emerald-50 dark:bg-emerald-500/15 text-emerald-500 dark:text-emerald-400 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-fingerprint"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest mb-1">Total Hadir Bulan Ini</p>
                <h3 class="text-4xl font-black text-slate-800 dark:text-white">{{ $summaryPresent ?? 0 }} <span class="text-sm font-bold text-slate-400 dark:text-slate-400 ml-1">Karyawan</span></h3>
            </div>
        </div>
        
        <div class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm dark:shadow-none flex items-center gap-5 group hover:border-amber-200 dark:hover:border-amber-500/40 transition-colors">
            <div class="w-16 h-16 rounded-[1.25rem] bg-amber-50 dark:bg-amber-500/15 text-amber-500 dark:text-amber-400 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-user-clock"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest mb-1">Terlambat Bulan Ini</p>
                <h3 class="text-4xl font-black text-slate-800 dark:text-white">{{ $summaryLate ?? 0 }} <span class="text-sm font-bold text-slate-400 dark:text-slate-400 ml-1">Karyawan</span></h3>
            </div>
        </div>
        
        <div class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm dark:shadow-none flex items-center gap-5 group hover:border-rose-200 dark:hover:border-rose-500/40 transition-colors">
            <div class="w-16 h-16 rounded-[1.25rem] bg-rose-50 dark:bg-rose-500/15 text-rose-500 dark:text-rose-400 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-plane-departure"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest mb-1">Cuti / Izin Bulan Ini</p>
                <h3 class="text-4xl font-black text-slate-800 dark:text-white">{{ $summaryLeave ?? 0 }} <span class="text-sm font-bold text-slate-400 dark:text-slate-400 ml-1">Karyawan</span></h3>
            </div>
        </div>
    </div>

    {{-- CHART AREA --}}
    <div class="bg-white dark:bg-slate-900 p-8 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm dark:shadow-none">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h3 class="font-black text-slate-800 dark:text-white text-base uppercase tracking-widest">
                    <i class="fas fa-chart-bar text-indigo-500 mr-2"></i> Tren Kehadiran
                </h3>
                <p class="text-xs text-slate-400 dark:text-slate-400 font-bold mt-1">
                    Bulan {{ $months[$activeMonth - 1] }} {{ $activeYear }}
                </p>
            </div>
            <button onclick="downloadChart()" class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-300 flex items-center justify-center hover:bg-indigo-50 dark:hover:bg-indigo-500/20 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors shadow-sm dark:shadow-none cursor-pointer" title="Unduh Grafik">
                <i class="fas fa-download"></i>
            </button>
        </div>
        
        <div class="w-full h-80 relative">
            @if(count($monthlyTrend) > 0)
                <canvas id="attendanceChart"></canvas>
            @else
                <div class="absolute inset-0 flex flex-col items-center justify-center bg-slate-50/50 dark:bg-slate-950/60 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-800">
                    <i class="fas fa-folder-open text-4xl text-slate-300 dark:text-slate-600 mb-3"></i>
                    <p class="text-slate-500 dark:text-slate-300 font-bold text-sm uppercase tracking-widest">Tidak Ada Data</p>
                    <p class="text-slate-400 dark:text-slate-500 text-xs mt-1">Belum ada rekam jejak absensi pada bulan ini.</p>
                </div>
            @endif
        </div>
    </div>
    
</div>

{{-- [BARU] CSS KHUSUS PENANGANAN DROPDOWN MODE GELAP & SCROLLBAR --}}
<style>
    html.dark select,
    body.dark select,
    .dark select {
        color-scheme: dark;
    }

    html.dark select option,
    body.dark select option,
    .dark select option {
        background-color: #0f172a !important;
        color: #f1f5f9 !important;
    }

    .report-scrollbar::-webkit-scrollbar { width: 6px; }
    .report-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .report-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
    .dark .report-scrollbar::-webkit-scrollbar-thumb,
    html.dark .report-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
</style>
@endsection

@section('scripts')
{{-- Include Chart.js via CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // [BARU] Logika Custom Dropdown Filter Bulan & Tahun (Anti-Bocor Warna Putih)
    function toggleReportDropdown(type) {
        const monthMenu = document.getElementById('monthDropdownMenu');
        const yearMenu  = document.getElementById('yearDropdownMenu');
        const monthIcon = document.getElementById('monthDropdownIcon');
        const yearIcon  = document.getElementById('yearDropdownIcon');

        if (type === 'month') {
            const isHidden = monthMenu.classList.contains('hidden');
            yearMenu.classList.add('hidden');
            yearIcon.classList.remove('rotate-180', 'text-indigo-400');

            if (isHidden) {
                monthMenu.classList.remove('hidden');
                monthIcon.classList.add('rotate-180', 'text-indigo-400');
            } else {
                monthMenu.classList.add('hidden');
                monthIcon.classList.remove('rotate-180', 'text-indigo-400');
            }
        } else if (type === 'year') {
            const isHidden = yearMenu.classList.contains('hidden');
            monthMenu.classList.add('hidden');
            monthIcon.classList.remove('rotate-180', 'text-indigo-400');

            if (isHidden) {
                yearMenu.classList.remove('hidden');
                yearIcon.classList.add('rotate-180', 'text-indigo-400');
            } else {
                yearMenu.classList.add('hidden');
                yearIcon.classList.remove('rotate-180', 'text-indigo-400');
            }
        }
    }

    function selectReportOption(type, value, label) {
        if (type === 'month') {
            document.getElementById('nativeMonthSelect').value = value;
            document.getElementById('monthDropdownLabel').textContent = label;
            document.querySelectorAll('.month-option-item').forEach(btn => {
                const isSelected = btn.getAttribute('data-value') === String(value);
                const icon = btn.querySelector('i');
                btn.className = isSelected
                    ? 'month-option-item w-full px-4 py-2 text-left text-xs flex items-center justify-between transition-colors cursor-pointer bg-indigo-600 text-white font-black'
                    : 'month-option-item w-full px-4 py-2 text-left text-xs flex items-center justify-between transition-colors cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/90 font-bold';
                if (icon) icon.classList.toggle('hidden', !isSelected);
            });
            document.getElementById('monthDropdownMenu').classList.add('hidden');
            document.getElementById('monthDropdownIcon').classList.remove('rotate-180', 'text-indigo-400');
        } else if (type === 'year') {
            document.getElementById('nativeYearSelect').value = value;
            document.getElementById('yearDropdownLabel').textContent = label;
            document.querySelectorAll('.year-option-item').forEach(btn => {
                const isSelected = btn.getAttribute('data-value') === String(value);
                const icon = btn.querySelector('i');
                btn.className = isSelected
                    ? 'year-option-item w-full px-4 py-2 text-left text-xs flex items-center justify-between transition-colors cursor-pointer bg-indigo-600 text-white font-black'
                    : 'year-option-item w-full px-4 py-2 text-left text-xs flex items-center justify-between transition-colors cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/90 font-bold';
                if (icon) icon.classList.toggle('hidden', !isSelected);
            });
            document.getElementById('yearDropdownMenu').classList.add('hidden');
            document.getElementById('yearDropdownIcon').classList.remove('rotate-180', 'text-indigo-400');
        }
    }

    // Tutup dropdown jika klik di luar area filter
    document.addEventListener('click', function(e) {
        const monthWrap = document.getElementById('monthDropdownWrapper');
        const yearWrap  = document.getElementById('yearDropdownWrapper');

        if (monthWrap && !monthWrap.contains(e.target)) {
            document.getElementById('monthDropdownMenu')?.classList.add('hidden');
            document.getElementById('monthDropdownIcon')?.classList.remove('rotate-180', 'text-indigo-400');
        }
        if (yearWrap && !yearWrap.contains(e.target)) {
            document.getElementById('yearDropdownMenu')?.classList.add('hidden');
            document.getElementById('yearDropdownIcon')?.classList.remove('rotate-180', 'text-indigo-400');
        }
    });

    // Hanya render grafik jika data trend ada
    @if(count($monthlyTrend) > 0)
        const trendData = @json($monthlyTrend);
        const isDarkTheme = document.documentElement.classList.contains('dark') || document.body.classList.contains('dark');
        
        // Memformat Label Tanggal (contoh: dari "2026-08-01" menjadi "01 Agt")
        const labels = trendData.map(item => {
            const dateObj = new Date(item.date);
            return dateObj.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
        });
        
        const presentData = trendData.map(item => item.total_present);
        const lateData = trendData.map(item => item.total_late);

        // Menyiapkan Data untuk Stacked Bar Chart
        // Hadir Tepat Waktu = Total Hadir - Total Terlambat
        const onTimeData = presentData.map((total, index) => Math.max(0, total - (lateData[index] || 0)));

        const ctx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Hadir Tepat Waktu',
                        data: onTimeData,
                        backgroundColor: '#10b981', // Tailwind Emerald 500
                        hoverBackgroundColor: '#059669',
                        borderRadius: { topLeft: 4, topRight: 4, bottomLeft: 0, bottomRight: 0 },
                        barPercentage: 0.6
                    },
                    {
                        label: 'Hadir Terlambat',
                        data: lateData,
                        backgroundColor: '#f59e0b', // Tailwind Amber 500
                        hoverBackgroundColor: '#d97706',
                        borderRadius: { topLeft: 4, topRight: 4, bottomLeft: 0, bottomRight: 0 },
                        barPercentage: 0.6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    x: { 
                        stacked: true, 
                        grid: { display: false },
                        ticks: { font: { family: "'Inter', sans-serif", size: 11, weight: 'bold' }, color: '#94a3b8' }
                    },
                    y: { 
                        stacked: true, 
                        beginAtZero: true, 
                        border: { dash: [4, 4] },
                        grid: { color: isDarkTheme ? 'rgba(148, 163, 184, 0.12)' : '#f1f5f9' },
                        ticks: { font: { family: "'Inter', sans-serif", size: 11, weight: 'bold' }, color: '#94a3b8', stepSize: 1 }
                    }
                },
                plugins: {
                    legend: { 
                        position: 'top', 
                        align: 'end',
                        labels: { 
                            color: isDarkTheme ? '#cbd5e1' : '#475569',
                            font: { family: "'Inter', sans-serif", size: 12, weight: 'bold' },
                            usePointStyle: true,
                            boxWidth: 8
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { family: "'Inter', sans-serif", size: 13 },
                        bodyFont: { family: "'Inter', sans-serif", size: 12, weight: 'bold' },
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: true
                    }
                }
            }
        });

        // Fungsi Opsional: Download Chart menjadi gambar PNG
        function downloadChart() {
            const link = document.createElement('a');
            link.download = `Tren_Kehadiran_{{ $months[$activeMonth - 1] }}_{{ $activeYear }}.png`;
            link.href = attendanceChart.toBase64Image();
            link.click();
        }
    @endif
</script>
@endsection