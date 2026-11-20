@extends('admin.layouts.master')
@section('title', 'Monitoring Absensi')

@section('content')
<div class="max-w-[100vw] mx-auto px-4 space-y-8 pb-20 animate-fadeIn overflow-x-hidden">
    
    {{-- 1. HEADER & EXCEL ACTIONS --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-none relative z-30">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <div class="w-10 h-10 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-md shadow-indigo-200 dark:shadow-indigo-950/50">
                    <i class="fas fa-calendar-check text-sm"></i>
                </div>
                <h1 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">Monitoring Absensi</h1>
            </div>
            <p class="text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider ml-13">Rekap Log Mesin Fingerprint & Input Absensi Manual Kolektif</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @php
                // [BARU] Label aktif cabang untuk Custom Dropdown Anti-Bocor
                $selectedBranchAttendance = $branches->firstWhere('id', request('branch_id'));
                $selectedBranchAttendanceLabel = $selectedBranchAttendance ? strtoupper($selectedBranchAttendance->name) : 'Semua Cabang';
            @endphp

            {{-- [FITUR BARU] Filter Rentang Tanggal, Cabang, & Search --}}
            <form action="{{ route('admin.transaksi.attendance.index') }}" method="GET" id="attendanceMonitorFilterForm" class="flex flex-wrap items-center gap-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 shadow-sm dark:shadow-none w-full md:w-auto">
                
                {{-- [TAMBAHAN BARU] Input Search NIK / Nama Karyawan --}}
                <div class="flex items-center gap-2 border-r border-slate-200 dark:border-slate-800 pr-3 mr-1">
                    <i class="fas fa-search text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / NIK..." 
                        class="!bg-transparent text-slate-700 dark:text-slate-200 text-xs font-bold outline-none w-32 md:w-40 placeholder-slate-400 dark:placeholder-slate-500"
                        onkeypress="if(event.keyCode == 13) { this.form.submit(); return false; }">
                </div>

                {{-- Dropdown Filter Cabang (Anti-Bocor Dark & Light Mode) --}}
                <div class="flex items-center gap-1.5 border-r border-slate-200 dark:border-slate-800 pr-3 mr-1 relative" id="attBranchDropdownWrapper">
                    <i class="fas fa-store text-slate-400 text-xs"></i>

                    {{-- [BARU] Trigger Custom Dropdown Cabang --}}
                    <button type="button" onclick="toggleAttendanceBranchDropdown()"
                        class="flex items-center gap-2 text-slate-700 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 text-xs font-bold outline-none cursor-pointer transition-colors">
                        <span class="truncate max-w-[150px]">{{ $selectedBranchAttendanceLabel }}</span>
                        <i id="attBranchDropdownIcon" class="fas fa-chevron-down text-[9px] text-slate-400 transition-transform duration-200"></i>
                    </button>

                    {{-- [BARU] Popup Menu Cabang (Gelap di Dark Mode, Putih di Light Mode) --}}
                    <div id="attBranchDropdownMenu"
                        class="hidden absolute left-0 top-full mt-3 w-64 max-h-64 overflow-y-auto attendance-dropdown-scroll rounded-2xl bg-white dark:bg-[#13151d] border border-slate-200 dark:border-slate-800 shadow-2xl shadow-slate-900/15 dark:shadow-black/80 py-1.5 z-50">
                        <button type="button" onclick="selectAttendanceBranch('')"
                            class="w-full px-4 py-2 text-left text-xs flex items-center justify-between transition-colors cursor-pointer {{ !request('branch_id') ? 'bg-indigo-600 text-white font-black' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/90 font-bold' }}">
                            <span>Semua Cabang</span>
                            @if(!request('branch_id'))
                                <i class="fas fa-check text-[10px]"></i>
                            @endif
                        </button>
                        @foreach($branches as $branch)
                            <button type="button" onclick="selectAttendanceBranch('{{ $branch->id }}')"
                                class="w-full px-4 py-2 text-left text-xs flex items-center justify-between transition-colors cursor-pointer {{ (string)request('branch_id') === (string)$branch->id ? 'bg-indigo-600 text-white font-black' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/90 font-bold' }}">
                                <span class="truncate pr-2">{{ strtoupper($branch->name) }}</span>
                                @if((string)request('branch_id') === (string)$branch->id)
                                    <i class="fas fa-check text-[10px] shrink-0"></i>
                                @endif
                            </button>
                        @endforeach
                    </div>

                    {{-- Select Asli Cabang (Tetap Dipertahankan & Tersinkronisasi) --}}
                    <select name="branch_id" id="nativeAttBranchSelect" onchange="this.form.submit()" class="sr-only dark:[color-scheme:dark] bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-100">
                        <option value="" class="bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-100">Semua Cabang</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" class="bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-100" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ strtoupper($branch->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <span class="text-[10px] font-black text-slate-400 uppercase">Dari:</span>
                <input type="date" name="start_date" value="{{ request('start_date', date('Y-m-26', strtotime('-1 month'))) }}" onchange="this.form.submit()" 
                    class="!bg-transparent dark:[color-scheme:dark] text-slate-700 dark:text-slate-200 text-xs font-bold outline-none cursor-pointer">
                
                <span class="text-[10px] font-black text-slate-400 uppercase ml-1">S/D:</span>
                <input type="date" name="end_date" value="{{ request('end_date', date('Y-m-25')) }}" onchange="this.form.submit()" 
                    class="!bg-transparent dark:[color-scheme:dark] text-slate-700 dark:text-slate-200 text-xs font-bold outline-none cursor-pointer">
                
                {{-- Tombol Cari (Optional for mobile fallback, enter key on search input also triggers submit) --}}
                <button type="submit" class="hidden md:flex ml-1 bg-slate-200 dark:bg-slate-800 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg px-2.5 py-1.5 transition-colors text-xs cursor-pointer">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            {{-- Tombol Export Excel --}}
            <a href="{{ route('admin.transaksi.attendance.export', ['start_date' => request('start_date'), 'end_date' => request('end_date'), 'branch_id' => request('branch_id')]) }}" 
                class="bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30 px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 dark:hover:bg-emerald-600 hover:text-white dark:hover:text-white transition-all flex items-center gap-2 shadow-sm dark:shadow-none">
                <i class="fas fa-file-excel"></i> Export Format
            </a>

            {{-- Tombol Cetak PDF --}}
            <a href="{{ route('admin.transaksi.attendance.exportPdf', ['start_date' => request('start_date'), 'end_date' => request('end_date'), 'branch_id' => request('branch_id')]) }}" target="_blank"
                class="bg-rose-50 dark:bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-500/30 px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-600 dark:hover:bg-rose-600 hover:text-white dark:hover:text-white transition-all flex items-center gap-2 shadow-sm dark:shadow-none">
                <i class="fas fa-file-pdf"></i> Cetak PDF
            </a>

            {{-- Tombol Import Modal Trigger --}}
            <button type="button" onclick="document.getElementById('modalImportExcel').classList.remove('hidden')" 
                class="bg-indigo-600 text-white px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all flex items-center gap-2 shadow-sm shadow-indigo-200 dark:shadow-indigo-950/50 cursor-pointer">
                <i class="fas fa-upload"></i> Import Data
            </button>

            {{-- Tombol Reset Data Periode Ini --}}
            <form action="{{ route('admin.transaksi.attendance.resetMonthly') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus seluruh data absensi pada rentang periode ini? Data yang dihapus tidak dapat dikembalikan.')" class="inline">
                @csrf
                <input type="hidden" name="start_date" value="{{ request('start_date', date('Y-m-26', strtotime('-1 month'))) }}">
                <input type="hidden" name="end_date" value="{{ request('end_date', date('Y-m-25')) }}">
                <button type="submit" class="bg-rose-50 dark:bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-500/30 px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-600 dark:hover:bg-rose-600 hover:text-white dark:hover:text-white transition-all flex items-center gap-2 shadow-sm dark:shadow-none cursor-pointer">
                    <i class="fas fa-trash-alt"></i> Reset Periode
                </button>
            </form>

            {{-- Tombol Simpan --}}
            <button type="button" onclick="document.getElementById('absensiForm').submit()" 
                class="bg-slate-900 dark:bg-slate-800 text-white px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 dark:hover:bg-indigo-600 border border-transparent dark:border-slate-700 transition-all flex items-center gap-2 shadow-md dark:shadow-none cursor-pointer">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </div>

    {{-- 2. STATS CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        {{-- Total Pegawai --}}
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-none flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest">Total Pegawai</p>
                <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1">{{ isset($users) ? count($users) : 0 }}</h3>
                <span class="inline-block mt-2 text-[10px] font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/15 border border-transparent dark:border-indigo-500/30 px-2 py-0.5 rounded-md">
                    @if(request('search')) Filter Pencarian @else Aktif Terdaftar @endif
                </span>
            </div>
            <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center text-lg shadow-sm dark:shadow-none border border-indigo-100 dark:border-indigo-500/30">
                <i class="fas fa-users"></i>
            </div>
        </div>

        {{-- Cabang Ditampilkan (Dinamis - Teks Full Tanpa Terpotong) --}}
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-none flex items-center justify-between">
            <div class="pr-2 min-w-0 flex-1">
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest">Cabang Ditampilkan</p>
                <h3 class="text-xs sm:text-sm font-black text-emerald-600 dark:text-emerald-400 mt-1 uppercase leading-snug break-words" title="{{ request('branch_id') ? $branches->firstWhere('id', request('branch_id'))->name : 'Semua Cabang' }}">
                    {{ request('branch_id') ? $branches->firstWhere('id', request('branch_id'))->name : 'Semua Cabang' }}
                </h3>
                <span class="inline-block mt-2 text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/15 border border-transparent dark:border-emerald-500/30 px-2 py-0.5 rounded-md">Filter Aktif</span>
            </div>
            <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center text-lg shadow-sm dark:shadow-none border border-emerald-100 dark:border-emerald-500/30 shrink-0">
                <i class="fas fa-store"></i>
            </div>
        </div>

        {{-- Periode Tabulasi --}}
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-none flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest">Rentang Tabulasi</p>
                <h3 class="text-sm font-black text-slate-800 dark:text-white mt-1 uppercase">
                    {{ \Carbon\Carbon::parse(request('start_date', date('Y-m-26', strtotime('-1 month'))))->translatedFormat('d M Y') }} - 
                    {{ \Carbon\Carbon::parse(request('end_date', date('Y-m-25')))->translatedFormat('d M Y') }}
                </h3>
                <span class="inline-block mt-2 text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/15 border border-transparent dark:border-amber-500/30 px-2 py-0.5 rounded-md">SOP Perusahaan</span>
            </div>
            <div class="w-12 h-12 bg-amber-50 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center text-lg shadow-sm dark:shadow-none border border-amber-100 dark:border-amber-500/30">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>

        {{-- Status Sinkronisasi --}}
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-none flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest">Status Data</p>
                <h3 class="text-xl font-black text-purple-600 dark:text-purple-400 mt-1">Sinkron</h3>
                <span class="inline-block mt-2 text-[10px] font-bold text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-500/15 border border-transparent dark:border-purple-500/30 px-2 py-0.5 rounded-md">Database OK</span>
            </div>
            <div class="w-12 h-12 bg-purple-50 dark:bg-purple-500/15 text-purple-600 dark:text-purple-400 rounded-2xl flex items-center justify-center text-lg shadow-sm dark:shadow-none border border-purple-100 dark:border-purple-500/30">
                <i class="fas fa-sync-alt"></i>
            </div>
        </div>

    </div>

    {{-- 3. SPREADSHEET (Dinamis Berdasarkan Rentang Tanggal) --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm dark:shadow-none border border-slate-200 dark:border-slate-800 overflow-hidden relative">
        
        {{-- Floating Save Alert --}}
        <div id="unsavedAlert" class="hidden absolute top-4 right-4 z-50 bg-amber-500 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg animate-pulse">
            <i class="fas fa-exclamation-triangle mr-1"></i> Ada perubahan belum disimpan
        </div>

        <form id="absensiForm" action="{{ route('admin.transaksi.attendance.storeKolektif') }}" method="POST">
            @csrf
            <input type="hidden" name="start_date" value="{{ request('start_date', date('Y-m-26', strtotime('-1 month'))) }}">
            <input type="hidden" name="end_date" value="{{ request('end_date', date('Y-m-25')) }}">

            <div class="overflow-x-auto excel-container custom-scrollbar pb-4">
                {{-- MIN-WIDTH DITAMBAHKAN AGAR KOLOM IN & OUT TIDAK TERHIMPIT --}}
                <table class="w-max text-left text-sm text-slate-600 dark:text-slate-300 border-collapse min-w-[3200px]">
                    
                    @php
                        $startDate = \Carbon\Carbon::parse(request('start_date', date('Y-m-26', strtotime('-1 month'))));
                        $endDate = \Carbon\Carbon::parse(request('end_date', date('Y-m-25')));
                        $periodDates = \Carbon\CarbonPeriod::create($startDate, $endDate);
                    @endphp

                    {{-- HEADER TABEL (Berlapis 2 untuk IN dan OUT) --}}
                    <thead class="bg-slate-900 dark:bg-slate-950 text-white sticky top-0 z-20">
                        <tr>
                            <th rowspan="2" class="px-6 py-4 text-[10px] font-black uppercase tracking-widest sticky left-0 z-30 bg-slate-900 dark:bg-slate-950 w-72 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.3)] align-middle border-b border-slate-700/50 dark:border-slate-800">
                                Identitas Karyawan
                            </th>
                            @foreach($periodDates as $date)
                                @php $isWeekend = $date->isWeekend(); @endphp
                                <th colspan="2" class="px-2 py-2 text-[10px] font-black text-center border-b border-slate-700/50 dark:border-slate-800 border-r border-slate-700/50 dark:border-slate-800 {{ $isWeekend ? 'bg-rose-900/50 text-rose-200' : '' }}">
                                    {{ $date->format('d M') }}
                                </th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach($periodDates as $date)
                                @php $isWeekend = $date->isWeekend(); @endphp
                                <th class="px-1 py-1.5 text-[8px] font-bold text-center tracking-widest text-emerald-400 bg-slate-800 dark:bg-slate-900 border-r border-slate-700/30 dark:border-slate-800 {{ $isWeekend ? 'bg-rose-900/40 text-rose-300' : '' }}">
                                    IN
                                </th>
                                <th class="px-1 py-1.5 text-[8px] font-bold text-center tracking-widest text-amber-400 bg-slate-800 dark:bg-slate-900 border-r border-slate-700/50 dark:border-slate-800 {{ $isWeekend ? 'bg-rose-900/40 text-rose-300' : '' }}">
                                    OUT
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    {{-- BODY TABEL --}}
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @forelse($users ?? [] as $user)
                        @php
                            // [TAMBAHAN BARU] Ambil jadwal jam masuk shift karyawan (default 08:00)
                            $rawShiftStart = $user->shift->start_time ?? $user->shift->jam_masuk ?? $user->shift->clock_in ?? '08:00:00';
                            $shiftStartStr = \Carbon\Carbon::parse($rawShiftStart)->format('H:i');
                        @endphp
                        <tr class="hover:bg-indigo-50/30 dark:hover:bg-slate-800/40 transition-colors group">
                            
                            {{-- Kolom Kiri Karyawan (Sticky) --}}
                            <td class="px-6 py-3 sticky left-0 z-10 bg-white dark:bg-slate-900 group-hover:bg-indigo-50/80 dark:group-hover:bg-slate-800/95 transition-colors shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] dark:shadow-[2px_0_8px_-2px_rgba(0,0,0,0.6)] border-b border-slate-100 dark:border-slate-800">
                                <div class="flex items-center gap-4">
                                    <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-300 font-black text-[11px] shrink-0 border border-slate-200 dark:border-slate-700">
                                        {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-black text-slate-800 dark:text-white text-xs uppercase tracking-tight truncate w-48" title="{{ $user->name }}">
                                            {{ $user->name }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 dark:text-slate-400 font-bold uppercase tracking-widest mt-0.5">
                                            ID: {{ $user->nik ?? '000000' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Kolom Input Absensi Masuk (IN) & Keluar (OUT) --}}
                            @foreach($periodDates as $date)
                                @php
                                    $dateStr = $date->format('Y-m-d');
                                    $isWeekend = $date->isWeekend();
                                    
                                    $existingLog = null;
                                    if(isset($attendanceRecords) && isset($attendanceRecords[$user->id][$dateStr])) {
                                        $existingLog = $attendanceRecords[$user->id][$dateStr];
                                    }

                                    $inVal = '';
                                    $outVal = '';
                                    $hiddenVal = '';
                                    $isLateEntry = false;
                                    
                                    if ($existingLog) {
                                        if ($existingLog->clock_in) {
                                            $inVal = \Carbon\Carbon::parse($existingLog->clock_in)->format('H:i');
                                            $hiddenVal = $inVal;

                                            // [TAMBAHAN BARU] Cek apakah karyawan terlambat masuk
                                            $isLateEntry = (bool)($existingLog->is_late ?? false)
                                                || strtolower(trim($existingLog->status ?? '')) === 'telat'
                                                || strtolower(trim($existingLog->status ?? '')) === 'terlambat'
                                                || trim($inVal) > $shiftStartStr;
                                            
                                            if ($existingLog->clock_out) {
                                                $outVal = \Carbon\Carbon::parse($existingLog->clock_out)->format('H:i');
                                                $hiddenVal .= ' ' . $outVal; // Format gabungan untuk disubmit
                                            }
                                        } else if (in_array(strtolower($existingLog->status), ['izin', 'sakit', 'cuti', 'alpha', 'alpha/mangkir'])) {
                                            $inVal = strtoupper(substr($existingLog->status, 0, 1));
                                            $hiddenVal = $inVal;

                                            // [TAMBAHAN BARU] Status Alpha juga ditandai merah
                                            if ($inVal === 'A') {
                                                $isLateEntry = true;
                                            }
                                        }
                                    }

                                    // [BARU] Warna & penanda merah mencolok jika terlambat masuk (tidak tertimpa putih oleh CSS global Dark Mode)
                                    $inColorClass = $isLateEntry
                                        ? 'is-late !text-rose-600 dark:!text-rose-400 bg-rose-50/80 dark:bg-rose-500/15 ring-1 ring-rose-300/70 dark:ring-rose-500/40 focus:bg-rose-50 dark:focus:bg-rose-500/25 focus:ring-rose-500'
                                        : 'is-ontime !text-emerald-700 dark:!text-emerald-400 focus:bg-emerald-50 dark:focus:bg-emerald-500/20 focus:ring-emerald-500';
                                @endphp
                                
                                {{-- CELL JAM MASUK --}}
                                <td class="p-1 {{ $isWeekend ? 'bg-rose-50/30 dark:bg-rose-950/25' : '' }} text-center border-r border-slate-100 dark:border-slate-800/70 border-dashed relative align-middle">
                                    <input type="text" 
                                        data-shift="{{ $shiftStartStr }}"
                                        @if($isLateEntry) title="Terlambat (Jadwal Shift: {{ $shiftStartStr }})" @endif
                                        class="vis-in w-11 py-1 text-center text-xs font-bold {{ $inColorClass }} !bg-transparent border-0 rounded-md outline-none focus:ring-1 transition-all placeholder-slate-300 dark:placeholder-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800/70"
                                        placeholder="-" value="{{ $inVal }}" autocomplete="off">
                                </td>

                                {{-- CELL JAM KELUAR --}}
                                <td class="p-1 {{ $isWeekend ? 'bg-rose-50/30 dark:bg-rose-950/25' : '' }} text-center border-r border-slate-200/60 dark:border-slate-800 relative align-middle">
                                    <input type="text" 
                                        class="vis-out w-11 py-1 text-center text-xs font-bold !text-amber-700 dark:!text-amber-400 !bg-transparent border-0 rounded-md outline-none focus:bg-amber-50 dark:focus:bg-amber-500/20 focus:ring-1 focus:ring-amber-500 transition-all placeholder-slate-300 dark:placeholder-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800/70"
                                        placeholder="-" value="{{ $outVal }}" autocomplete="off">
                                    
                                    {{-- Hidden input penyimpan format "IN OUT" agar sistem controller tidak rusak --}}
                                    <input type="hidden" class="hidden-attendance" name="attendance[{{ $user->id }}][{{ $dateStr }}]" value="{{ $hiddenVal }}">
                                </td>
                            @endforeach
                        </tr>
                        @empty
                        <tr>
                            <td colspan="65" class="px-6 py-16 text-center text-slate-400">
                                <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800/60 text-slate-300 dark:text-slate-600 rounded-3xl flex items-center justify-center text-2xl mx-auto mb-3 border-2 border-dashed border-slate-200 dark:border-slate-700">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <p class="font-black text-slate-700 dark:text-slate-300 uppercase tracking-tight text-sm">Belum Ada Data Karyawan</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Silakan sesuaikan filter atau tambahkan data karyawan terlebih dahulu.</p>
                                @if(request('search'))
                                    <a href="{{ route('admin.transaksi.attendance.index') }}" class="inline-block mt-4 text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/15 px-4 py-2 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-500/25 transition-colors">
                                        Reset Pencarian
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Panduan Pengisian --}}
            <div class="p-5 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-200 dark:border-slate-800 flex flex-wrap items-center gap-6">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest"><i class="fas fa-info-circle text-indigo-500 mr-1"></i> Panduan Format:</span>
                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 flex flex-wrap gap-2">
                    <span class="text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/15 px-2 py-0.5 rounded border border-emerald-100 dark:border-emerald-500/30">08:00 (In)</span>
                    <span class="text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/15 px-2 py-0.5 rounded border border-rose-100 dark:border-rose-500/30">08:05 (In Terlambat)</span>
                    <span class="text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/15 px-2 py-0.5 rounded border border-amber-100 dark:border-amber-500/30">17:00 (Out)</span>
                </span>
                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400"><strong class="text-slate-800 dark:text-slate-200">C</strong> = Cuti (Isi di Kolom IN)</span>
                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400"><strong class="text-slate-800 dark:text-slate-200">I</strong> = Izin</span>
                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400"><strong class="text-slate-800 dark:text-slate-200">S</strong> = Sakit</span>
                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400"><strong class="text-rose-600 dark:text-rose-400">A</strong> = Alpha</span>
            </div>
        </form>
    </div>
</div>

{{-- 4. MODAL UPLOAD IMPORT EXCEL --}}
<div id="modalImportExcel" class="hidden fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 animate-fadeIn">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 max-w-lg w-full overflow-hidden">
        <div class="bg-slate-900 dark:bg-slate-950 px-6 py-5 flex justify-between items-center text-white border-b border-transparent dark:border-slate-800">
            <h3 class="font-black text-xs uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-file-import text-indigo-400"></i> Import Log Absensi Excel
            </h3>
            <button type="button" onclick="document.getElementById('modalImportExcel').classList.add('hidden')" class="text-slate-400 hover:text-white transition cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form action="{{ route('admin.transaksi.attendance.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf
            <input type="hidden" name="start_date" value="{{ request('start_date', date('Y-m-26', strtotime('-1 month'))) }}">
            <input type="hidden" name="end_date" value="{{ request('end_date', date('Y-m-25')) }}">

            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pilih File Excel (.xlsx / .csv) <span class="text-rose-500">*</span></label>
                <input type="file" name="file_excel" id="fileExcelInput" accept=".xlsx, .xls, .csv" required 
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 outline-none file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-indigo-50 dark:file:bg-indigo-500/20 file:text-indigo-600 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-500/30 cursor-pointer">
            </div>

            <div class="bg-slate-900 dark:bg-slate-950 text-white rounded-2xl p-5 space-y-4 border border-slate-800 shadow-inner">
                <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-800 pb-2 flex items-center justify-between">
                    <span class="flex items-center gap-2"><i class="fas fa-chart-pie text-indigo-400"></i> Preview / Hasil Validasi Import</span>
                    <span id="previewLoading" class="hidden text-indigo-400 text-[9px] animate-pulse"><i class="fas fa-spinner fa-spin mr-1"></i> Memindai file...</span>
                </h4>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div id="prevMatched" class="text-2xl font-black text-emerald-400">--</div>
                        <p class="text-[10px] font-bold text-slate-300 uppercase tracking-wider mt-0.5">Karyawan Cocok</p>
                    </div>
                    <div>
                        <div id="prevAttendance" class="text-2xl font-black text-white">--</div>
                        <p class="text-[10px] font-bold text-slate-300 uppercase tracking-wider mt-0.5">Data Kehadiran Terbaca</p>
                    </div>
                    <div>
                        <div id="prevReview" class="text-2xl font-black text-amber-400">0</div>
                        <p class="text-[10px] font-bold text-slate-300 uppercase tracking-wider mt-0.5">Data Perlu Diperiksa</p>
                    </div>
                    <div>
                        <div id="prevUnmatched" class="text-2xl font-black text-rose-400">0</div>
                        <p class="text-[10px] font-bold text-slate-300 uppercase tracking-wider mt-0.5">ID Tidak Ditemukan</p>
                    </div>
                </div>
                <p class="text-[9px] text-slate-400 italic pt-1 border-t border-slate-800/80">Statistik otomatis dihitung setelah file Excel diunggah.</p>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalImportExcel').classList.add('hidden')" 
                    class="flex-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 dark:hover:bg-slate-700 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                    class="flex-1 bg-indigo-600 text-white py-3 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-indigo-200 dark:shadow-none hover:bg-indigo-700 transition active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fas fa-upload"></i> Proses Import
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 12px; width: 12px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 8px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; border: 3px solid #f8fafc; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* [BARU] Perbaikan Dark Mode untuk Scrollbar, Select, Date Input, dan Sel Spreadsheet */
    html.dark .custom-scrollbar::-webkit-scrollbar-track,
    body.dark .custom-scrollbar::-webkit-scrollbar-track,
    .dark .custom-scrollbar::-webkit-scrollbar-track {
        background: #0f172a;
    }
    html.dark .custom-scrollbar::-webkit-scrollbar-thumb,
    body.dark .custom-scrollbar::-webkit-scrollbar-thumb,
    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #334155;
        border: 3px solid #0f172a;
    }
    html.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover,
    body.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover,
    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #475569;
    }

    html.dark select,
    body.dark select,
    .dark select,
    html.dark input[type="date"],
    body.dark input[type="date"],
    .dark input[type="date"] {
        color-scheme: dark;
    }

    html.dark select option,
    body.dark select option,
    .dark select option {
        background-color: #0f172a !important;
        color: #f1f5f9 !important;
    }

    /* Mencegah kotak putih bawaan CSS global muncul pada sel input IN & OUT */
    .excel-container input.vis-in,
    .excel-container input.vis-out {
        background-color: transparent !important;
        box-shadow: none !important;
    }

    /* [BARU] Paksa warna teks & indikator merah terlambat agar tidak tertimpa CSS input global Dark Mode */
    .excel-container input.vis-in.is-late {
        color: #e11d48 !important;
        background-color: rgba(244, 63, 94, 0.12) !important;
    }
    html.dark .excel-container input.vis-in.is-late,
    body.dark .excel-container input.vis-in.is-late,
    .dark .excel-container input.vis-in.is-late {
        color: #fb7185 !important;
        background-color: rgba(244, 63, 94, 0.16) !important;
    }

    .excel-container input.vis-in.is-ontime {
        color: #047857 !important;
    }
    html.dark .excel-container input.vis-in.is-ontime,
    body.dark .excel-container input.vis-in.is-ontime,
    .dark .excel-container input.vis-in.is-ontime {
        color: #34d399 !important;
    }

    .excel-container input.vis-out {
        color: #b45309 !important;
    }
    html.dark .excel-container input.vis-out,
    body.dark .excel-container input.vis-out,
    .dark .excel-container input.vis-out {
        color: #fbbf24 !important;
    }

    .excel-container input.vis-in.cell-dirty,
    .excel-container input.vis-out.cell-dirty {
        background-color: rgba(245, 158, 11, 0.16) !important;
    }

    .attendance-dropdown-scroll::-webkit-scrollbar { width: 6px; }
    .attendance-dropdown-scroll::-webkit-scrollbar-track { background: transparent; }
    .attendance-dropdown-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
    .dark .attendance-dropdown-scroll::-webkit-scrollbar-thumb,
    html.dark .attendance-dropdown-scroll::-webkit-scrollbar-thumb { background: #334155; }
</style>
@endsection

@section('scripts')
<script>
    // [BARU] Custom Dropdown Cabang Anti-Bocor
    function toggleAttendanceBranchDropdown() {
        const menu = document.getElementById('attBranchDropdownMenu');
        const icon = document.getElementById('attBranchDropdownIcon');
        if (menu) {
            const isHidden = menu.classList.contains('hidden');
            menu.classList.toggle('hidden', !isHidden);
            icon?.classList.toggle('rotate-180', isHidden);
        }
    }

    function selectAttendanceBranch(branchId) {
        const nativeSelect = document.getElementById('nativeAttBranchSelect');
        if (nativeSelect) {
            nativeSelect.value = branchId;
            document.getElementById('attendanceMonitorFilterForm').submit();
        }
    }

    document.addEventListener('click', function(e) {
        const branchWrap = document.getElementById('attBranchDropdownWrapper');
        if (branchWrap && !branchWrap.contains(e.target)) {
            document.getElementById('attBranchDropdownMenu')?.classList.add('hidden');
            document.getElementById('attBranchDropdownIcon')?.classList.remove('rotate-180');
        }
    });

    // Preview AJAX Upload
    document.getElementById('fileExcelInput').addEventListener('change', function(e) {
        let file = e.target.files[0];
        if (!file) return;

        let formData = new FormData();
        formData.append('file_excel', file);
        formData.append('_token', '{{ csrf_token() }}');

        document.getElementById('previewLoading').classList.remove('hidden');
        document.getElementById('prevMatched').innerText = '...';
        document.getElementById('prevAttendance').innerText = '...';

        fetch("{{ route('admin.transaksi.attendance.preview') }}", {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('previewLoading').classList.add('hidden');
            if (data.success) {
                document.getElementById('prevMatched').innerText = data.matched_employees;
                document.getElementById('prevAttendance').innerText = data.attendance_count;
                document.getElementById('prevReview').innerText = data.needs_review;
                document.getElementById('prevUnmatched').innerText = data.unmatched_ids;
            } else {
                alert('Gagal memindai file: ' + data.message);
            }
        })
        .catch(error => {
            document.getElementById('previewLoading').classList.add('hidden');
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memindai file Excel.');
        });
    });

    // Handle Sinkronisasi Input (IN & OUT) ke Hidden Input
    document.querySelectorAll('.vis-in, .vis-out').forEach(input => {
        input.addEventListener('input', function() {
            document.getElementById('unsavedAlert').classList.remove('hidden');
            this.classList.add('cell-dirty'); 
            
            const td = this.closest('td');
            let inInput, outInput, hiddenInput;
            
            // Logika Mencari Pasangan Kolom
            if (this.classList.contains('vis-in')) {
                inInput = this;
                const nextTd = td.nextElementSibling;
                outInput = nextTd.querySelector('.vis-out');
                hiddenInput = nextTd.querySelector('.hidden-attendance');
            } else {
                outInput = this;
                const prevTd = td.previousElementSibling;
                inInput = prevTd.querySelector('.vis-in');
                hiddenInput = td.querySelector('.hidden-attendance');
            }

            let inVal = inInput.value.trim();
            let outVal = outInput.value.trim();

            // [TAMBAHAN BARU] Ubah warna kolom IN menjadi merah secara real-time jika terlambat masuk
            if (this.classList.contains('vis-in')) {
                const shiftStart = this.dataset.shift || '08:00';
                const timeMatch = inVal.match(/^([01]\d|2[0-3]):([0-5]\d)$/);
                const isLateInput = (timeMatch && inVal > shiftStart) || inVal.toUpperCase() === 'A';

                if (isLateInput) {
                    this.classList.remove('is-ontime', 'text-emerald-700', '!text-emerald-700', 'dark:text-emerald-400', 'dark:!text-emerald-400', 'focus:bg-emerald-50', 'dark:focus:bg-emerald-500/20', 'focus:ring-emerald-500');
                    this.classList.add('is-late', 'text-rose-600', '!text-rose-600', 'dark:text-rose-400', 'dark:!text-rose-400', 'bg-rose-50/80', 'dark:bg-rose-500/15', 'ring-1', 'ring-rose-300/70', 'dark:ring-rose-500/40', 'focus:bg-rose-50', 'dark:focus:bg-rose-500/25', 'focus:ring-rose-500');
                    this.title = `Terlambat (Jadwal Shift: ${shiftStart})`;
                } else {
                    this.classList.remove('is-late', 'text-rose-600', '!text-rose-600', 'dark:text-rose-400', 'dark:!text-rose-400', 'bg-rose-50/80', 'dark:bg-rose-500/15', 'ring-1', 'ring-rose-300/70', 'dark:ring-rose-500/40', 'focus:bg-rose-50', 'dark:focus:bg-rose-500/25', 'focus:ring-rose-500');
                    this.classList.add('is-ontime', 'text-emerald-700', '!text-emerald-700', 'dark:text-emerald-400', 'dark:!text-emerald-400', 'focus:bg-emerald-50', 'dark:focus:bg-emerald-500/20', 'focus:ring-emerald-500');
                    this.removeAttribute('title');
                }
            }
            
            // Update value yang sesungguhnya dikirim ke server (Controller)
            if (inVal && outVal) {
                hiddenInput.value = inVal + ' ' + outVal;
            } else if (inVal) {
                hiddenInput.value = inVal;
            } else if (outVal) {
                hiddenInput.value = outVal;
            } else {
                hiddenInput.value = '';
            }
        });
    });

    // Keyboard Navigation di Dalam Grid Absensi (Tetap Bekerja Normal Lintas 2 Kolom)
    document.querySelector('.excel-container').addEventListener('keydown', function(e) {
        if (['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
            const activeInput = document.activeElement;
            if (activeInput.tagName !== 'INPUT' || activeInput.type === 'hidden') return;

            const currentCell = activeInput.closest('td');
            const currentRow = currentCell.closest('tr');
            let nextInput = null;

            switch(e.key) {
                case 'ArrowRight': 
                    if (activeInput.selectionEnd === activeInput.value.length) {
                        nextInput = currentCell.nextElementSibling?.querySelector('input[type="text"]'); 
                    }
                    break;
                case 'ArrowLeft': 
                    if (activeInput.selectionStart === 0) {
                        nextInput = currentCell.previousElementSibling?.querySelector('input[type="text"]'); 
                    }
                    break;
                case 'ArrowDown':
                    const cellIdxD = Array.from(currentRow.children).indexOf(currentCell);
                    nextInput = currentRow.nextElementSibling?.children[cellIdxD]?.querySelector('input[type="text"]');
                    break;
                case 'ArrowUp':
                    const cellIdxU = Array.from(currentRow.children).indexOf(currentCell);
                    nextInput = currentRow.previousElementSibling?.children[cellIdxU]?.querySelector('input[type="text"]');
                    break;
            }
            if (nextInput) {
                e.preventDefault();
                nextInput.focus();
                nextInput.setSelectionRange(nextInput.value.length, nextInput.value.length);
            }
        }
    });
</script>
@endsection