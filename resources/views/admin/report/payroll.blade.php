@extends('admin.layouts.master')
@section('title', 'Laporan & Analitik Penggajian')

@section('content')
@php
    // [BARU] Normalisasi parameter filter & perhitungan metrik ringkasan otomatis dari $chartData
    $activeYear  = (int) ($year ?? request('year', date('Y')));
    $activeMonth = request('month', '');
    $monthOptions = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    $chartCollection = collect($chartData ?? []);
    $nonZeroMonths   = $chartCollection->filter(fn($item) => (float) ($item['total'] ?? $item->total ?? 0) > 0);
    $activeMonthCount = $nonZeroMonths->count();
    $avgMonthlySpend  = $activeMonthCount > 0 ? ($nonZeroMonths->sum('total') / $activeMonthCount) : 0;
    $peakMonthItem    = $nonZeroMonths->sortByDesc('total')->first();
    $peakMonthName    = is_array($peakMonthItem) ? ($peakMonthItem['month'] ?? '-') : ($peakMonthItem->month ?? '-');
    $peakMonthValue   = is_array($peakMonthItem) ? ($peakMonthItem['total'] ?? 0) : ($peakMonthItem->total ?? 0);
@endphp

<div class="space-y-6 animate-fadeIn">
    
    {{-- HEADER & FILTER --}}
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 bg-white dark:bg-slate-900 p-6 md:p-7 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm dark:shadow-none relative z-30">
        <div class="flex items-start sm:items-center gap-4">
            {{-- [BARU] Ikon Header Modern --}}
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-500/15 border border-indigo-100 dark:border-indigo-500/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <h2 class="font-black text-slate-800 dark:text-white text-xl tracking-tight">Laporan Penggajian</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-500/30">
                        Tahun {{ $activeYear }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Analitik dan rekapitulasi tren pengeluaran beban gaji perusahaan tahunan.</p>
            </div>
        </div>
        
        <form method="GET" action="{{ route('admin.report.payroll') }}" id="payrollFilterForm" class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            {{-- KAPSUL FILTER BULAN & TAHUN (ANTI-BOCOR DARK/LIGHT MODE) --}}
            <div class="relative flex items-center bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-indigo-500/60 rounded-2xl focus-within:ring-2 focus-within:ring-indigo-500 dark:shadow-[0_0_0_3px_rgba(99,102,241,0.12)] transition-all">
                <i class="fas fa-calendar-alt text-slate-400 dark:text-slate-400 pl-4 text-sm"></i>

                {{-- [BARU] CUSTOM DROPDOWN FILTER BULAN (Opsional / Sorot Bulan) --}}
                <div class="relative" id="payrollMonthWrapper">
                    <button type="button" onclick="togglePayrollDropdown('month')"
                        class="flex items-center gap-2 text-xs md:text-sm font-bold text-slate-700 dark:text-white px-3.5 py-2.5 outline-none cursor-pointer hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        <span id="payrollMonthLabel">{{ !empty($activeMonth) && isset($monthOptions[(int)$activeMonth]) ? $monthOptions[(int)$activeMonth] : 'Semua Bulan' }}</span>
                        <i id="payrollMonthIcon" class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200"></i>
                    </button>

                    {{-- POPUP MENU BULAN (DARK & LIGHT MODE READY) --}}
                    <div id="payrollMonthMenu"
                        class="hidden absolute left-0 mt-2 w-48 max-h-64 overflow-y-auto payroll-scrollbar rounded-2xl bg-white dark:bg-[#13151d] border border-slate-200 dark:border-slate-800 shadow-2xl shadow-slate-900/15 dark:shadow-black/80 py-1.5 z-50">
                        <button type="button"
                            onclick="selectPayrollOption('month', '', 'Semua Bulan')"
                            data-value=""
                            class="payroll-month-item w-full px-4 py-2 text-left text-xs flex items-center justify-between transition-colors cursor-pointer {{ empty($activeMonth) ? 'bg-indigo-600 text-white font-black' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/90 font-bold' }}">
                            <span>Semua Bulan (Tahunan)</span>
                            <i class="fas fa-check text-[10px] {{ empty($activeMonth) ? '' : 'hidden' }}"></i>
                        </button>
                        <div class="my-1 border-t border-slate-100 dark:border-slate-800"></div>
                        @foreach($monthOptions as $mNum => $mName)
                            <button type="button"
                                onclick="selectPayrollOption('month', '{{ $mNum }}', '{{ $mName }}')"
                                data-value="{{ $mNum }}"
                                class="payroll-month-item w-full px-4 py-2 text-left text-xs flex items-center justify-between transition-colors cursor-pointer {{ (string)$activeMonth === (string)$mNum ? 'bg-indigo-600 text-white font-black' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/90 font-bold' }}">
                                <span>{{ $mName }}</span>
                                <i class="fas fa-check text-[10px] {{ (string)$activeMonth === (string)$mNum ? '' : 'hidden' }}"></i>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- INPUT HIDDEN BULAN (Hanya ikut terkirim jika bulan spesifik dipilih) --}}
                <input type="hidden" id="nativePayrollMonthInput" name="{{ !empty($activeMonth) ? 'month' : '' }}" value="{{ $activeMonth }}">

                <div class="w-px h-5 bg-slate-300 dark:bg-slate-700"></div>

                {{-- [BARU] CUSTOM DROPDOWN FILTER TAHUN (ANTI-BOCOR WARNA PUTIH) --}}
                <div class="relative" id="payrollYearWrapper">
                    <button type="button" onclick="togglePayrollDropdown('year')"
                        class="flex items-center gap-2 text-xs md:text-sm font-bold text-slate-700 dark:text-white px-4 py-2.5 outline-none cursor-pointer hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        <span id="payrollYearLabel">Tahun {{ $activeYear }}</span>
                        <i id="payrollYearIcon" class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200"></i>
                    </button>

                    {{-- POPUP MENU TAHUN --}}
                    <div id="payrollYearMenu"
                        class="hidden absolute right-0 mt-2 w-40 max-h-60 overflow-y-auto payroll-scrollbar rounded-2xl bg-white dark:bg-[#13151d] border border-slate-200 dark:border-slate-800 shadow-2xl shadow-slate-900/15 dark:shadow-black/80 py-1.5 z-50">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <button type="button"
                                onclick="selectPayrollOption('year', '{{ $y }}', 'Tahun {{ $y }}')"
                                data-value="{{ $y }}"
                                class="payroll-year-item w-full px-4 py-2 text-left text-xs flex items-center justify-between transition-colors cursor-pointer {{ $activeYear == $y ? 'bg-indigo-600 text-white font-black' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/90 font-bold' }}">
                                <span>Tahun {{ $y }}</span>
                                <i class="fas fa-check text-[10px] {{ $activeYear == $y ? '' : 'hidden' }}"></i>
                            </button>
                        @endfor
                    </div>
                </div>

                {{-- SELECT TAHUN ASLI (Tetap Dipertahankan & Tersinkronisasi) --}}
                <select name="year" id="nativePayrollYearSelect" class="sr-only dark:[color-scheme:dark] bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-100">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" class="bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-100" {{ $activeYear == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endfor
                </select>
            </div>
            
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-200 dark:hover:shadow-indigo-950/50 transition-all flex items-center gap-2 cursor-pointer active:scale-95">
                <i class="fas fa-filter"></i> Terapkan
            </button>

            {{-- [BARU] Tombol Reset Cepat ke Tahun Berjalan --}}
            @if($activeYear != date('Y') || !empty($activeMonth))
                <a href="{{ route('admin.report.payroll') }}"
                   title="Reset Filter ke Tahun Ini"
                   class="px-4 py-2.5 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-black uppercase tracking-wider transition-all flex items-center gap-1.5">
                    <i class="fas fa-undo text-[10px]"></i> Reset
                </a>
            @endif
        </form>
    </div>

    {{-- SUMMARY CARD (Total Pengeluaran) --}}
    <div class="grid grid-cols-1 gap-6">
        <div class="bg-gradient-to-br from-indigo-500 via-indigo-600 to-indigo-800 p-8 rounded-[2rem] shadow-lg shadow-indigo-500/15 dark:shadow-none flex flex-col md:flex-row md:items-center justify-between gap-6 text-white relative overflow-hidden group">
            {{-- Ornamen Cahaya Modern --}}
            <div class="absolute -top-24 -right-24 w-72 h-72 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-sm border border-white/20 text-[10px] font-black uppercase tracking-widest text-indigo-50 mb-3">
                    <span class="w-1.5 h-1.5 rounded-full {{ ($yearlyStats->total_net ?? 0) > 0 ? 'bg-emerald-300 animate-pulse' : 'bg-indigo-200' }}"></span>
                    Rekapitulasi Akumulasi Tahunan
                </div>
                <p class="text-xs font-black uppercase tracking-widest text-indigo-100 mb-2">Total Pengeluaran Gaji Bersih ({{ $activeYear }})</p>
                <h3 class="text-4xl md:text-5xl font-black tracking-tight">
                    Rp {{ number_format($yearlyStats->total_net ?? 0, 0, ',', '.') }}
                </h3>
            </div>

            {{-- [BARU] Badge Ringkas di Kanan Hero Card --}}
            <div class="z-10 flex flex-wrap items-center gap-3">
                <div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl px-5 py-3.5">
                    <p class="text-[9px] font-black uppercase tracking-widest text-indigo-200">Bulan Terbayar</p>
                    <p class="text-lg font-black text-white mt-0.5">{{ $activeMonthCount }} <span class="text-xs font-bold text-indigo-200">/ 12 Bulan</span></p>
                </div>
            </div>

            <i class="fas fa-wallet text-8xl opacity-10 absolute -right-4 -bottom-4 group-hover:scale-110 group-hover:-rotate-12 transition-transform duration-500"></i>
        </div>
    </div>

    {{-- [BARU] KARTU ANALITIK TAMBAHAN (Rata-rata, Puncak Pengeluaran, Bulan Terpilih) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm dark:shadow-none flex items-center gap-4 group hover:border-indigo-200 dark:hover:border-indigo-500/40 transition-colors">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-2xl shrink-0 group-hover:scale-105 transition-transform">
                <i class="fas fa-calculator"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest mb-1">Rata-Rata / Bulan Aktif</p>
                <h4 class="text-xl font-black text-slate-800 dark:text-white tracking-tight">
                    Rp {{ number_format($avgMonthlySpend, 0, ',', '.') }}
                </h4>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm dark:shadow-none flex items-center gap-4 group hover:border-emerald-200 dark:hover:border-emerald-500/40 transition-colors">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl shrink-0 group-hover:scale-105 transition-transform">
                <i class="fas fa-arrow-trend-up"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest mb-1">
                    Pengeluaran Tertinggi {{ $peakMonthValue > 0 ? '(' . $peakMonthName . ')' : '' }}
                </p>
                <h4 class="text-xl font-black text-slate-800 dark:text-white tracking-tight">
                    Rp {{ number_format($peakMonthValue, 0, ',', '.') }}
                </h4>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm dark:shadow-none flex items-center gap-4 group hover:border-amber-200 dark:hover:border-amber-500/40 transition-colors">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center text-2xl shrink-0 group-hover:scale-105 transition-transform">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest mb-1">
                    {{ !empty($activeMonth) && isset($monthOptions[(int)$activeMonth]) ? 'Sorotan Bulan ' . $monthOptions[(int)$activeMonth] : 'Status Data Payroll' }}
                </p>
                <h4 id="highlightedMonthTotal" class="text-xl font-black text-slate-800 dark:text-white tracking-tight">
                    @if(!empty($activeMonth) && isset($chartData[(int)$activeMonth - 1]))
                        @php $mItem = $chartData[(int)$activeMonth - 1]; @endphp
                        Rp {{ number_format(is_array($mItem) ? ($mItem['total'] ?? 0) : ($mItem->total ?? 0), 0, ',', '.') }}
                    @else
                        {{ $activeMonthCount > 0 ? $activeMonthCount . ' Periode Tercatat' : 'Belum Ada Transaksi' }}
                    @endif
                </h4>
            </div>
        </div>
    </div>

    {{-- CHART AREA --}}
    <div class="bg-white dark:bg-slate-900 p-8 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm dark:shadow-none">
        <div class="flex flex-wrap justify-between items-center gap-4 mb-8">
            <div>
                <h3 class="font-black text-slate-800 dark:text-white text-base uppercase tracking-widest">
                    <i class="fas fa-chart-line text-indigo-500 mr-2"></i> Tren Pengeluaran
                </h3>
                <p class="text-xs text-slate-400 dark:text-slate-400 font-bold mt-1">
                    Akumulasi Beban Gaji Tahun {{ $activeYear }}
                    @if(!empty($activeMonth) && isset($monthOptions[(int)$activeMonth]))
                        <span class="text-indigo-500 dark:text-indigo-400">&bull; Sorotan: {{ $monthOptions[(int)$activeMonth] }}</span>
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                {{-- [BARU] Tombol Toggle Tabel Rincian Bulanan --}}
                <button type="button" onclick="toggleMonthlyBreakdown()"
                    class="px-4 py-2.5 rounded-full bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-indigo-500/20 hover:text-indigo-600 dark:hover:text-indigo-400 text-[10px] font-black uppercase tracking-widest transition-colors flex items-center gap-2 cursor-pointer">
                    <i class="fas fa-table"></i> <span>Rincian Bulanan</span>
                </button>
                <button type="button" onclick="downloadChart()" class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-300 flex items-center justify-center hover:bg-indigo-50 dark:hover:bg-indigo-500/20 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors shadow-sm dark:shadow-none cursor-pointer" title="Unduh Grafik">
                    <i class="fas fa-download"></i>
                </button>
            </div>
        </div>
        
        <div class="w-full h-80 relative">
            <canvas id="payrollChart"></canvas>
        </div>

        {{-- [BARU] PANEL RINCIAN REKAPITULASI BULANAN (COLLAPSIBLE) --}}
        <div id="monthlyBreakdownPanel" class="hidden mt-8 pt-6 border-t border-slate-100 dark:border-slate-800">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-800">
                            <th class="py-3 px-4">Bulan</th>
                            <th class="py-3 px-4">Total Gaji Bersih</th>
                            <th class="py-3 px-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @foreach($chartCollection as $idx => $row)
                            @php
                                $rowMonth = is_array($row) ? ($row['month'] ?? '-') : ($row->month ?? '-');
                                $rowTotal = (float) (is_array($row) ? ($row['total'] ?? 0) : ($row->total ?? 0));
                                $isHighlighted = !empty($activeMonth) && ((int)$activeMonth === ($idx + 1));
                            @endphp
                            <tr class="{{ $isHighlighted ? 'bg-indigo-50/70 dark:bg-indigo-500/15' : 'hover:bg-slate-50/70 dark:hover:bg-slate-800/40' }} transition-colors">
                                <td class="py-3 px-4 font-bold text-slate-700 dark:text-slate-200">{{ $rowMonth }} {{ $activeYear }}</td>
                                <td class="py-3 px-4 font-black text-slate-800 dark:text-white">Rp {{ number_format($rowTotal, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right">
                                    @if($rowTotal > 0)
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400">
                                            Terbayar
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500">
                                            Nihil
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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

    .payroll-scrollbar::-webkit-scrollbar { width: 6px; }
    .payroll-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .payroll-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
    .dark .payroll-scrollbar::-webkit-scrollbar-thumb,
    html.dark .payroll-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
</style>
@endsection

@section('scripts')
{{-- Include Chart.js via CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // [BARU] Logika Custom Dropdown Filter Bulan & Tahun (Anti-Bocor Warna Putih)
    function togglePayrollDropdown(type) {
        const monthMenu = document.getElementById('payrollMonthMenu');
        const yearMenu  = document.getElementById('payrollYearMenu');
        const monthIcon = document.getElementById('payrollMonthIcon');
        const yearIcon  = document.getElementById('payrollYearIcon');

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

    function selectPayrollOption(type, value, label) {
        if (type === 'month') {
            const hiddenInput = document.getElementById('nativePayrollMonthInput');
            hiddenInput.value = value;
            hiddenInput.name = value ? 'month' : '';
            document.getElementById('payrollMonthLabel').textContent = label;

            document.querySelectorAll('.payroll-month-item').forEach(btn => {
                const isSelected = btn.getAttribute('data-value') === String(value);
                const icon = btn.querySelector('i');
                btn.className = isSelected
                    ? 'payroll-month-item w-full px-4 py-2 text-left text-xs flex items-center justify-between transition-colors cursor-pointer bg-indigo-600 text-white font-black'
                    : 'payroll-month-item w-full px-4 py-2 text-left text-xs flex items-center justify-between transition-colors cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/90 font-bold';
                if (icon) icon.classList.toggle('hidden', !isSelected);
            });

            document.getElementById('payrollMonthMenu').classList.add('hidden');
            document.getElementById('payrollMonthIcon').classList.remove('rotate-180', 'text-indigo-400');
        } else if (type === 'year') {
            document.getElementById('nativePayrollYearSelect').value = value;
            document.getElementById('payrollYearLabel').textContent = label;

            document.querySelectorAll('.payroll-year-item').forEach(btn => {
                const isSelected = btn.getAttribute('data-value') === String(value);
                const icon = btn.querySelector('i');
                btn.className = isSelected
                    ? 'payroll-year-item w-full px-4 py-2 text-left text-xs flex items-center justify-between transition-colors cursor-pointer bg-indigo-600 text-white font-black'
                    : 'payroll-year-item w-full px-4 py-2 text-left text-xs flex items-center justify-between transition-colors cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/90 font-bold';
                if (icon) icon.classList.toggle('hidden', !isSelected);
            });

            document.getElementById('payrollYearMenu').classList.add('hidden');
            document.getElementById('payrollYearIcon').classList.remove('rotate-180', 'text-indigo-400');
        }
    }

    // Tutup dropdown jika klik di luar area filter
    document.addEventListener('click', function(e) {
        const monthWrap = document.getElementById('payrollMonthWrapper');
        const yearWrap  = document.getElementById('payrollYearWrapper');

        if (monthWrap && !monthWrap.contains(e.target)) {
            document.getElementById('payrollMonthMenu')?.classList.add('hidden');
            document.getElementById('payrollMonthIcon')?.classList.remove('rotate-180', 'text-indigo-400');
        }
        if (yearWrap && !yearWrap.contains(e.target)) {
            document.getElementById('payrollYearMenu')?.classList.add('hidden');
            document.getElementById('payrollYearIcon')?.classList.remove('rotate-180', 'text-indigo-400');
        }
    });

    // [BARU] Toggle panel tabel rincian bulanan
    function toggleMonthlyBreakdown() {
        const panel = document.getElementById('monthlyBreakdownPanel');
        if (panel) panel.classList.toggle('hidden');
    }

    const rawData = @json($chartData);
    const isDarkTheme = document.documentElement.classList.contains('dark') || document.body.classList.contains('dark');
    const selectedMonthIndex = {{ !empty($activeMonth) ? ((int)$activeMonth - 1) : -1 }};
    
    // Mengekstrak label (nama bulan) dan nilai (total_net)
    const labels = rawData.map(item => item.month);
    const dataValues = rawData.map(item => Number(item.total || 0));
    const hasNonZeroData = dataValues.some(val => val > 0);

    // [BARU] Sorot titik bulan tertentu jika filter bulan dipilih
    const pointRadii = dataValues.map((_, idx) => idx === selectedMonthIndex ? 7 : 4);
    const pointColors = dataValues.map((_, idx) => idx === selectedMonthIndex ? '#f59e0b' : (isDarkTheme ? '#0f172a' : '#ffffff'));

    const ctx = document.getElementById('payrollChart').getContext('2d');
    
    // Membuat efek gradien warna di bawah kurva grafik
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.4)'); // Warna Indigo-500 transparan
    gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

    const payrollChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Pengeluaran',
                data: dataValues,
                borderColor: '#6366f1', // Indigo-500
                backgroundColor: gradient,
                borderWidth: 3,
                pointBackgroundColor: pointColors,
                pointBorderColor: '#6366f1',
                pointBorderWidth: 2,
                pointRadius: pointRadii,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.4 // Membuat garis melengkung (smooth curve)
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                x: { 
                    grid: { display: false },
                    ticks: { font: { family: "'Inter', sans-serif", size: 11, weight: 'bold' }, color: '#94a3b8' }
                },
                y: { 
                    beginAtZero: true,
                    // [BARU] Mencegah sumbu Y menampilkan Rp 0.1 - Rp 1 saat semua data masih 0
                    suggestedMax: hasNonZeroData ? undefined : 10000000,
                    border: { dash: [4, 4] },
                    grid: { color: isDarkTheme ? 'rgba(148, 163, 184, 0.12)' : '#f1f5f9' },
                    ticks: { 
                        font: { family: "'Inter', sans-serif", size: 11, weight: 'bold' }, 
                        color: '#94a3b8',
                        // Memformat angka besar di sumbu Y (Misal: 10.000.000 menjadi 10 Jt)
                        callback: function(value) {
                            if (value >= 1000000000) {
                                return 'Rp ' + (value / 1000000000).toFixed(1).replace(/\.0$/, '') + ' M';
                            }
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000).toFixed(1).replace(/\.0$/, '') + ' Jt';
                            }
                            if (value >= 1000) {
                                return 'Rp ' + (value / 1000).toFixed(0) + ' Rb';
                            }
                            return 'Rp ' + value;
                        }
                    }
                }
            },
            plugins: {
                legend: { display: false }, // Sembunyikan legend karena hanya 1 data
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleFont: { family: "'Inter', sans-serif", size: 13 },
                    bodyFont: { family: "'Inter', sans-serif", size: 12, weight: 'bold' },
                    padding: 12,
                    cornerRadius: 12,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            let value = context.raw || 0;
                            return 'Rp ' + value.toLocaleString('id-ID'); // Format rupiah di tooltip
                        }
                    }
                }
            }
        }
    });

    // Fungsi Opsional: Download Chart menjadi gambar PNG
    function downloadChart() {
        const link = document.createElement('a');
        link.download = `Tren_Pengeluaran_Gaji_{{ $activeYear }}.png`;
        link.href = payrollChart.toBase64Image();
        link.click();
    }
</script>
@endsection