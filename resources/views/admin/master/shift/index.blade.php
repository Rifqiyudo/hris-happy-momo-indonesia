@extends('admin.layouts.master')
@section('title', 'Manajemen Shift & Jadwal')

@section('content')
{{-- Load AlpineJS untuk Interaksi Tab & Modal --}}
<script src="//unpkg.com/alpinejs" defer></script>

{{-- [BARU] Inisialisasi 7 Hari Baku & Pemetaan Data Pola Mingguan agar Tidak Pernah Kosong --}}
@php
    $defaultDays = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu',
    ];
    $patternMap = collect($weeklyPattern ?? [])->keyBy('day_number');

    // [BARU] Normalisasi key agar mendukung kolom day_number maupun day_of_week dari database
    $normalizedPatternMap = collect($weeklyPattern ?? [])->mapWithKeys(function ($item) {
        $key = $item->day_number ?? $item->day_of_week ?? $item->day ?? null;
        return $key !== null ? [(int) $key => $item] : [];
    });

    $todayDayNumber = \Carbon\Carbon::now()->dayOfWeekIso; // 1 (Senin) - 7 (Minggu)
    $todayPattern = $normalizedPatternMap->get($todayDayNumber) ?? $patternMap->get($todayDayNumber);
    $todayShift = $todayPattern ? $todayPattern->shift : null;
    $firstWorkShift = $shifts->where('is_holiday', false)->first() ?? $shifts->first();
    $firstHolidayShift = $shifts->where('is_holiday', true)->first();

    // [BARU] Cek apakah database sudah memiliki minimal 1 hari kerja aktif (bukan null / libur semua)
    $hasConfiguredWorkShift = $normalizedPatternMap->contains(function ($item) {
        $isOff = (bool) ($item->is_off ?? $item->is_holiday ?? false);
        $shiftIsHoliday = (bool) ($item->shift->is_holiday ?? false);
        return !$isOff && !empty($item->shift_id) && !$shiftIsHoliday;
    });

    // [BARU] Jika database belum punya mapping shift kerja sama sekali, aktifkan default Senin-Sabtu Kerja & Minggu Libur
    if (!$hasConfiguredWorkShift && $firstWorkShift) {
        $todayShift = ($todayDayNumber <= 6) ? $firstWorkShift : $firstHolidayShift;
    }
@endphp

<div class="max-w-7xl mx-auto pb-20 px-4 pt-6">
    
    {{-- X-DATA: Mengatur State Tab dan Modal (DIPERBARUI DENGAN SESSION FLASH) --}}
    <div x-data="{ activeTab: '{{ session('active_tab', 'master') }}', showPatternModal: false }" class="space-y-8">

        {{-- [BARU] NOTIFIKASI FLASH MESSAGE SAAT SIMPAN / SINKRONISASI --}}
        @if(session('success'))
        <div x-data="{ showAlert: true }" x-show="showAlert" x-transition
             class="bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-300 px-6 py-4 rounded-2xl flex items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0">
                    <i class="fas fa-check text-xs"></i>
                </div>
                <span class="text-xs font-black uppercase tracking-wide">{{ session('success') }}</span>
            </div>
            <button type="button" @click="showAlert = false" class="text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-200">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
        @endif

        @if(session('error'))
        <div x-data="{ showAlert: true }" x-show="showAlert" x-transition
             class="bg-rose-50 dark:bg-rose-500/15 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-300 px-6 py-4 rounded-2xl flex items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0">
                    <i class="fas fa-exclamation-triangle text-xs"></i>
                </div>
                <span class="text-xs font-black uppercase tracking-wide">{{ session('error') }}</span>
            </div>
            <button type="button" @click="showAlert = false" class="text-rose-500 hover:text-rose-700 dark:hover:text-rose-200">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
        @endif

        {{-- [BARU] NOTIFIKASI VALIDASI INPUT FORM LARAVEL --}}
        @if($errors->any())
        <div x-data="{ showAlert: true }" x-show="showAlert" x-transition
             class="bg-rose-50 dark:bg-rose-500/15 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-300 px-6 py-4 rounded-2xl flex items-start justify-between gap-4 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0 mt-0.5">
                    <i class="fas fa-exclamation-circle text-xs"></i>
                </div>
                <div>
                    <p class="text-xs font-black uppercase tracking-wide">Periksa Kembali Input Anda:</p>
                    <ul class="mt-1 list-disc list-inside text-xs font-semibold space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" @click="showAlert = false" class="text-rose-500 hover:text-rose-700 dark:hover:text-rose-200">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
        @endif

        {{-- ========================================================= --}}
        {{-- 1. HEADER & NAVIGASI TAB --}}
        {{-- ========================================================= --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 bg-white dark:bg-slate-900 p-6 md:p-8 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm dark:shadow-none relative overflow-hidden">
            {{-- Efek Latar Belakang --}}
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-indigo-50 dark:bg-indigo-500/10 rounded-full blur-3xl opacity-60 pointer-events-none"></div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-4 mb-2">
                    <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-200 dark:shadow-indigo-950/50">
                        <i class="fas fa-business-time text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white tracking-tight">Pengaturan Jam Kerja</h1>
                        <p class="text-xs md:text-sm text-slate-400 dark:text-slate-400 font-medium mt-1">Kelola template shift master dan pola kerja mingguan default.</p>
                    </div>
                </div>
            </div>
            
            {{-- TAB NAVIGATION MODERN --}}
            <div class="relative z-10 bg-slate-50/80 dark:bg-slate-950/80 backdrop-blur-md p-1.5 rounded-2xl border border-slate-200/60 dark:border-slate-800 shadow-sm dark:shadow-none flex items-center w-full md:w-auto">
                <button @click="activeTab = 'master'" 
                    :class="{ 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 font-black shadow-sm ring-1 ring-slate-900/5 dark:ring-slate-700': activeTab === 'master', 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 font-bold': activeTab !== 'master' }"
                    class="flex-1 md:flex-none px-6 py-2.5 rounded-xl text-[11px] uppercase tracking-widest transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-clock"></i> <span>Master Shift</span>
                </button>
                <button @click="activeTab = 'pattern'" 
                    :class="{ 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 font-black shadow-sm ring-1 ring-slate-900/5 dark:ring-slate-700': activeTab === 'pattern', 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 font-bold': activeTab !== 'pattern' }"
                    class="flex-1 md:flex-none px-6 py-2.5 rounded-xl text-[11px] uppercase tracking-widest transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-calendar-week"></i> <span>Pola Mingguan</span>
                </button>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- 2. KONTEN TAB: MASTER SHIFT (CRUD SHIFT) --}}
        {{-- ========================================================= --}}
        <div x-show="activeTab === 'master'" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            {{-- BAGIAN KIRI: DAFTAR SHIFT --}}
            <div class="lg:col-span-8">
                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-xl shadow-slate-200/40 dark:shadow-none border border-slate-100 dark:border-slate-800 overflow-hidden flex flex-col min-h-[500px]">
                    <div class="p-8 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-slate-900/60">
                        <div>
                            <h3 class="font-black text-slate-800 dark:text-white text-lg tracking-tight">Daftar Template Shift</h3>
                            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-widest mt-1">Total: <span class="text-indigo-500 dark:text-indigo-400">{{ $shifts->count() }} Template Aktif</span></p>
                        </div>
                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            {{-- [BARU] Pencarian Cepat Template Shift --}}
                            <div class="relative flex-1 sm:w-56">
                                <i class="fas fa-search text-xs text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                                <input type="text" id="searchShiftInput" onkeyup="filterShiftTable()" placeholder="Cari template shift..."
                                    class="w-full bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800 rounded-xl pl-9 pr-3 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 outline-none focus:border-indigo-500 transition">
                            </div>
                            <div class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 dark:text-slate-400 border border-slate-100 dark:border-slate-700 shrink-0">
                                <i class="fas fa-list-ul"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto flex-1 p-2">
                        <table id="shiftTable" class="w-full text-left text-sm text-slate-600 dark:text-slate-300 border-separate border-spacing-y-2.5 px-4 sm:px-6">
                            <thead class="text-slate-400 dark:text-slate-400 font-black uppercase text-[9px] tracking-[0.2em]">
                                <tr>
                                    <th class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">Nama Shift</th>
                                    <th class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">Jam Kerja</th>
                                    <th class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">Istirahat</th>
                                    <th class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($shifts as $shift)
                                <tr class="shift-row group transition-all duration-200 hover:-translate-y-0.5">
                                    <td class="px-6 py-4 bg-slate-50/60 dark:bg-slate-950/60 group-hover:bg-indigo-50/40 dark:group-hover:bg-indigo-950/30 rounded-l-2xl border-y border-l border-slate-100/80 dark:border-slate-800/90 group-hover:border-indigo-100 dark:group-hover:border-indigo-500/30 transition-colors">
                                        <div class="flex items-center gap-4">
                                            <div class="w-2 h-12 rounded-full shadow-sm shrink-0" style="background-color: {{ $shift->color ?? '#6366f1' }}"></div>
                                            <div>
                                                <div class="font-black text-slate-800 dark:text-slate-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors text-sm tracking-tight">{{ $shift->name }}</div>
                                                <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                                    {{-- [BARU] Badge Status Hari Libur --}}
                                                    @if($shift->is_holiday)
                                                        <span class="text-[8px] bg-rose-500/15 text-rose-500 dark:text-rose-400 border border-rose-500/30 px-2 py-0.5 rounded-md font-bold uppercase tracking-widest flex items-center gap-1">
                                                            <i class="fas fa-bed"></i> Libur
                                                        </span>
                                                    @endif
                                                    @if($shift->is_night_shift)
                                                        <span class="text-[8px] bg-slate-800 dark:bg-indigo-500/20 text-white dark:text-indigo-300 border border-transparent dark:border-indigo-500/30 px-2 py-0.5 rounded-md font-bold uppercase tracking-widest shadow-sm dark:shadow-none flex items-center gap-1">
                                                            <i class="fas fa-moon text-amber-300"></i> Night
                                                        </span>
                                                    @endif
                                                    <span class="text-[8px] bg-white dark:bg-slate-800/90 text-slate-500 dark:text-slate-300 border border-slate-200 dark:border-slate-700 px-2 py-0.5 rounded-md font-bold uppercase tracking-widest shadow-sm dark:shadow-none">
                                                        Tol: {{ $shift->late_tolerance }}m
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 bg-slate-50/60 dark:bg-slate-950/60 group-hover:bg-indigo-50/40 dark:group-hover:bg-indigo-950/30 border-y border-slate-100/80 dark:border-slate-800/90 group-hover:border-indigo-100 dark:group-hover:border-indigo-500/30 transition-colors">
                                        @if($shift->is_holiday)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-400 text-[10px] font-black uppercase tracking-widest">
                                                <i class="fas fa-bed"></i> Off Duty
                                            </span>
                                        @else
                                            <div class="font-mono text-xs font-bold flex flex-col gap-1.5">
                                                <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/15 w-fit px-2.5 py-0.5 rounded-md border border-emerald-100 dark:border-emerald-500/30">
                                                    <i class="fas fa-sign-in-alt text-[10px]"></i> {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}
                                                </div>
                                                <div class="flex items-center gap-2 text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/15 w-fit px-2.5 py-0.5 rounded-md border border-rose-100 dark:border-rose-500/30">
                                                    <i class="fas fa-sign-out-alt text-[10px]"></i> {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                                                </div>
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 bg-slate-50/60 dark:bg-slate-950/60 group-hover:bg-indigo-50/40 dark:group-hover:bg-indigo-950/30 border-y border-slate-100/80 dark:border-slate-800/90 group-hover:border-indigo-100 dark:group-hover:border-indigo-500/30 transition-colors">
                                        @if($shift->break_start)
                                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-500/30 text-[10px] font-black uppercase tracking-widest whitespace-nowrap">
                                                <i class="fas fa-mug-hot"></i> {{ \Carbon\Carbon::parse($shift->break_start)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->break_end)->format('H:i') }}
                                            </div>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800/90 text-slate-400 dark:text-slate-400 border border-slate-200 dark:border-slate-700 text-[9px] font-black uppercase tracking-widest whitespace-nowrap">
                                                <i class="fas fa-ban"></i> Non-break
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 bg-slate-50/60 dark:bg-slate-950/60 group-hover:bg-indigo-50/40 dark:group-hover:bg-indigo-950/30 rounded-r-2xl border-y border-r border-slate-100/80 dark:border-slate-800/90 group-hover:border-indigo-100 dark:group-hover:border-indigo-500/30 transition-colors text-center">
                                        <div class="flex justify-center gap-2">
                                            <button type="button" onclick='editShift(@json($shift))' 
                                                class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-amber-500 dark:text-amber-400 hover:bg-amber-500 dark:hover:bg-amber-500 hover:text-white dark:hover:text-white hover:border-amber-500 dark:hover:border-amber-500 transition-all flex items-center justify-center shadow-sm dark:shadow-none hover:shadow-amber-200 dark:hover:shadow-none">
                                                <i class="fas fa-pencil-alt text-xs"></i>
                                            </button>
                                            <button type="button" onclick="confirmDelete('{{ $shift->id }}', '{{ $shift->name }}')" 
                                                class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-rose-500 dark:text-rose-400 hover:bg-rose-500 dark:hover:bg-rose-500 hover:text-white dark:hover:text-white hover:border-rose-500 dark:hover:border-rose-500 transition-all flex items-center justify-center shadow-sm dark:shadow-none hover:shadow-rose-200 dark:hover:shadow-none">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </div>
                                        <form id="delete-form-{{ $shift->id }}" action="{{ route('admin.master.jadwal.shift.destroy', $shift->id) }}" method="POST" class="hidden">
                                            @csrf @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-16 text-center">
                                        <div class="w-20 h-20 mx-auto bg-slate-50 dark:bg-slate-800/60 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-clock text-3xl text-slate-300 dark:text-slate-500"></i>
                                        </div>
                                        <h4 class="text-sm font-black text-slate-700 dark:text-slate-200 uppercase tracking-tight">Belum ada Template</h4>
                                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Silakan buat template shift baru di form samping.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- BAGIAN KANAN: FORM INPUT SHIFT --}}
            <div class="lg:col-span-4">
                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-xl shadow-slate-200/40 dark:shadow-none border border-slate-100 dark:border-slate-800 sticky top-8 overflow-hidden">
                    <div class="bg-slate-50 dark:bg-slate-900/60 px-8 py-6 border-b border-slate-100 dark:border-slate-800 relative overflow-hidden">
                        {{-- Ornamen Bulat Kecil --}}
                        <div class="absolute -right-4 -top-4 w-16 h-16 bg-indigo-500/10 rounded-full blur-xl pointer-events-none"></div>
                        <h3 class="font-black text-slate-800 dark:text-white flex items-center gap-3 text-lg tracking-tight relative z-10" id="formTitle">
                            <i class="fas fa-plus-circle text-indigo-500 dark:text-indigo-400 text-xl"></i> Buat Shift Baru
                        </h3>
                    </div>
                    
                    <div class="p-8">
                        <form id="shiftForm" action="{{ route('admin.master.jadwal.shift.store') }}" method="POST">
                            @csrf
                            <div id="methodField"></div>

                            <div class="space-y-5">
                                {{-- NAMA SHIFT --}}
                                <div>
                                    <label class="block text-[10px] font-black text-slate-800 dark:text-slate-300 uppercase tracking-widest mb-1.5">Nama Shift <span class="text-rose-500">*</span></label>
                                    <input type="text" name="name" id="nameInput" required 
                                        class="w-full bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-100 outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm dark:shadow-none placeholder:text-slate-400 dark:placeholder:text-slate-500" 
                                        placeholder="Contoh: Regular Office">
                                </div>

                                {{-- JAM MASUK & PULANG --}}
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-emerald-50/50 dark:bg-emerald-500/10 p-3.5 rounded-2xl border border-emerald-100 dark:border-emerald-500/25">
                                        <label class="block text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-1.5">Masuk <span class="text-rose-500">*</span></label>
                                        <input type="time" name="start_time" id="startInput" required 
                                            class="w-full bg-white dark:bg-slate-950/80 border border-emerald-200 dark:border-emerald-500/30 rounded-xl px-3 py-2 text-sm font-bold text-slate-700 dark:text-slate-100 dark:[color-scheme:dark] outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm dark:shadow-none">
                                    </div>
                                    <div class="bg-rose-50/50 dark:bg-rose-500/10 p-3.5 rounded-2xl border border-rose-100 dark:border-rose-500/25">
                                        <label class="block text-[10px] font-black text-rose-600 dark:text-rose-400 uppercase tracking-widest mb-1.5">Pulang <span class="text-rose-500">*</span></label>
                                        <input type="time" name="end_time" id="endInput" required 
                                            class="w-full bg-white dark:bg-slate-950/80 border border-rose-200 dark:border-rose-500/30 rounded-xl px-3 py-2 text-sm font-bold text-slate-700 dark:text-slate-100 dark:[color-scheme:dark] outline-none focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all shadow-sm dark:shadow-none">
                                    </div>
                                </div>

                                {{-- JAM ISTIRAHAT --}}
                                <div class="bg-slate-50 dark:bg-slate-950/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-800">
                                    <label class="block text-[10px] font-black text-slate-500 dark:text-slate-300 uppercase tracking-widest mb-3 flex items-center gap-2">
                                        <i class="fas fa-mug-hot text-amber-500"></i> Jam Istirahat <span class="text-[8px] font-bold text-slate-400 dark:text-slate-500 normal-case tracking-normal">(Opsional)</span>
                                    </label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="relative">
                                            <span class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1 ml-1">Mulai</span>
                                            <input type="time" name="break_start" id="breakStartInput" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 dark:text-slate-100 dark:[color-scheme:dark] outline-none focus:border-indigo-500 transition-all shadow-sm dark:shadow-none">
                                        </div>
                                        <div class="relative">
                                            <span class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1 ml-1">Selesai</span>
                                            <input type="time" name="break_end" id="breakEndInput" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 dark:text-slate-100 dark:[color-scheme:dark] outline-none focus:border-indigo-500 transition-all shadow-sm dark:shadow-none">
                                        </div>
                                    </div>
                                </div>

                                {{-- TOLERANSI & WARNA --}}
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-black text-slate-800 dark:text-slate-300 uppercase tracking-widest mb-1.5">Toleransi (Menit)</label>
                                        <input type="number" name="late_tolerance" id="tolInput" value="0" min="0"
                                            class="w-full bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-100 outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm dark:shadow-none">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-slate-800 dark:text-slate-300 uppercase tracking-widest mb-1.5">Warna Label</label>
                                        <div class="relative w-full h-[46px] rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-sm dark:shadow-none cursor-pointer">
                                            <input type="color" name="color" id="colorInput" value="#4f46e5" class="absolute inset-0 w-full h-full scale-150 cursor-pointer border-0 p-0">
                                        </div>
                                    </div>
                                </div>

                                {{-- NIGHT SHIFT TOGGLE --}}
                                <label class="flex items-start gap-3 p-4 bg-white dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 rounded-2xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all shadow-sm dark:shadow-none mt-2">
                                    <input type="checkbox" name="is_night_shift" id="nightInput" value="1" class="mt-0.5 w-4 h-4 rounded border-gray-300 dark:border-slate-600 dark:bg-slate-800 text-indigo-600 focus:ring-indigo-500">
                                    <div>
                                        <p class="text-[11px] font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">Shift Lintas Hari (Night Shift)</p>
                                        <p class="text-[9px] font-bold text-slate-400 dark:text-slate-400 mt-0.5 uppercase tracking-widest leading-relaxed">Centang jika jam pulang melewati tengah malam.</p>
                                    </div>
                                </label>

                                {{-- [BARU] HOLIDAY / OFF DUTY TOGGLE --}}
                                <label class="flex items-start gap-3 p-4 bg-white dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 rounded-2xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all shadow-sm dark:shadow-none">
                                    <input type="checkbox" name="is_holiday" id="holidayInput" value="1" onchange="toggleHolidayFields()" class="mt-0.5 w-4 h-4 rounded border-gray-300 dark:border-slate-600 dark:bg-slate-800 text-rose-600 focus:ring-rose-500">
                                    <div>
                                        <p class="text-[11px] font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">Tandai Sebagai Hari Libur (Off Duty)</p>
                                        <p class="text-[9px] font-bold text-slate-400 dark:text-slate-400 mt-0.5 uppercase tracking-widest leading-relaxed">Gunakan untuk template libur / tidak ada jam kerja.</p>
                                    </div>
                                </label>

                                {{-- TOMBOL SUBMIT --}}
                                <div class="flex items-center gap-3 pt-6 border-t border-slate-100 dark:border-slate-800">
                                    <button type="button" id="btnCancel" onclick="resetForm()" class="hidden flex-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-300 py-3.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-slate-700 transition shadow-sm dark:shadow-none">Batal</button>
                                    <button type="submit" id="btnSubmit" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-indigo-700 shadow-xl shadow-indigo-100 dark:shadow-indigo-950/50 transition active:scale-95">Simpan Data</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- 3. KONTEN TAB: POLA MINGGUAN --}}
        {{-- ========================================================= --}}
        <div x-show="activeTab === 'pattern'" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             style="display: none;" 
             class="bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-xl shadow-slate-200/40 dark:shadow-none border border-slate-100 dark:border-slate-800 overflow-hidden relative">
            
            <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-50 dark:bg-indigo-500/10 rounded-full blur-3xl opacity-50 pointer-events-none -mt-20 -mr-20"></div>

            <div class="p-8 border-b border-slate-100 dark:border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative z-10">
                <div>
                    <div class="flex flex-wrap items-center gap-3 mb-1">
                        <h3 class="font-black text-slate-800 dark:text-white text-2xl tracking-tight">Pola Kerja Mingguan Default</h3>
                        {{-- [BARU] Badge Status Hari Ini --}}
                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ ($todayShift && !($todayShift->is_holiday ?? false)) ? 'bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30' : 'bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-500/30' }}">
                            Hari Ini ({{ $defaultDays[$todayDayNumber] }}):
                            @if($todayShift && !($todayShift->is_holiday ?? false))
                                {{ $todayShift->name }} ({{ \Carbon\Carbon::parse($todayShift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($todayShift->end_time)->format('H:i') }})
                            @else
                                Hari Libur / Off Duty
                            @endif
                        </span>
                    </div>
                    <p class="text-sm font-medium text-slate-400 dark:text-slate-400">Pola ini otomatis digunakan sebagai rujukan dasar jadwal operasional perusahaan & shift karyawan.</p>
                </div>
                <button @click="showPatternModal = true" class="w-full md:w-auto bg-indigo-600 text-white px-8 py-3.5 rounded-xl text-[11px] font-black uppercase tracking-[0.2em] hover:bg-indigo-700 transition shadow-xl shadow-indigo-100 dark:shadow-indigo-950/50 flex items-center justify-center gap-3 active:scale-95">
                    <i class="fas fa-sliders-h text-sm"></i> Sesuaikan Pola
                </button>
            </div>

            {{-- [BARU] Banner Konfirmasi Cepat 1-Klik saat DB belum memiliki mapping shift kerja (Senin-Sabtu Kerja, Minggu Libur) --}}
            @if(!$hasConfiguredWorkShift && $firstWorkShift)
            <div class="mx-8 mt-6 p-4 md:p-5 rounded-2xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 flex flex-col lg:flex-row lg:items-center justify-between gap-4 relative z-10">
                <div class="flex items-start sm:items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0">
                        <i class="fas fa-calendar-check text-xs"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black text-amber-900 dark:text-amber-200 uppercase tracking-tight">Pola 6 Hari Kerja (Senin–Sabtu) Siap Diterapkan</p>
                        <p class="text-[11px] font-medium text-amber-700 dark:text-amber-300/90 mt-0.5">
                            Senin–Sabtu dipetakan otomatis ke shift <strong>{{ $firstWorkShift->name }}</strong> dan Minggu berstatus Libur. Klik tombol di samping untuk mengunci permanen ke database.
                        </p>
                    </div>
                </div>
                <form action="{{ route('admin.master.jadwal.shift.pattern.update') }}" method="POST" class="shrink-0">
                    @csrf @method('PUT')
                    @foreach($defaultDays as $dNum => $dName)
                        <input type="hidden" name="patterns[{{ $dNum }}]" value="{{ $dNum <= 6 ? $firstWorkShift->id : ($firstHolidayShift->id ?? '') }}">
                    @endforeach
                    <input type="hidden" name="sync_mode" value="today">
                    <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-black uppercase tracking-widest transition shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fas fa-check-circle"></i> Simpan Permanen Sekarang
                    </button>
                </form>
            </div>
            @endif
            
            <div class="p-8 relative z-10">
                {{-- [DIPERBARUI] Selalu render 7 hari (Senin - Minggu) secara aman meski tabel database belum terisi --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 xl:grid-cols-7 gap-4">
                    @foreach($defaultDays as $dayNum => $dayName)
                        @php
                            $pattern = $normalizedPatternMap->get($dayNum) ?? $patternMap->get($dayNum);
                            $assignedShift = $pattern ? $pattern->shift : null;

                            // [BARU] Jika database belum punya mapping shift kerja sama sekali, default Senin-Sabtu = Kerja, Minggu = Libur
                            if (!$hasConfiguredWorkShift && $firstWorkShift) {
                                $assignedShift = ($dayNum <= 6) ? $firstWorkShift : $firstHolidayShift;
                            }

                            $isHoliday = !$assignedShift || ($assignedShift->is_holiday ?? false) || ($pattern->is_off ?? false);
                            $isToday = ($dayNum === $todayDayNumber);
                        @endphp
                        <div @click="showPatternModal = true"
                             title="Klik untuk menyesuaikan jadwal hari {{ $dayName }}"
                             class="cursor-pointer relative bg-slate-50/50 dark:bg-slate-950/60 hover:bg-white dark:hover:bg-slate-800/70 p-6 rounded-[2rem] border {{ $isToday ? 'border-indigo-400 dark:border-indigo-500/60 ring-2 ring-indigo-500/15' : 'border-slate-100 dark:border-slate-800' }} hover:border-indigo-200 dark:hover:border-indigo-500/40 shadow-sm dark:shadow-none hover:shadow-xl hover:shadow-indigo-100/40 dark:hover:shadow-none transition-all duration-300 flex flex-col items-center justify-center text-center group min-h-[220px]">
                            
                            {{-- [BARU] Penanda Hari Berjalan --}}
                            @if($isToday)
                                <span class="absolute top-3.5 px-2.5 py-0.5 rounded-full bg-indigo-600 text-white text-[8px] font-black uppercase tracking-widest shadow-sm">
                                    Hari Ini
                                </span>
                            @endif

                            <span class="text-[10px] font-black {{ $isToday ? 'text-indigo-600 dark:text-indigo-400 mt-3' : 'text-slate-400 dark:text-slate-400' }} group-hover:text-indigo-500 dark:group-hover:text-indigo-400 uppercase tracking-[0.3em] mb-5 transition-colors">{{ $dayName }}</span>
                            
                            @if($isHoliday)
                                <div class="w-16 h-16 rounded-3xl bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-300 dark:text-slate-500 flex items-center justify-center mb-4 shadow-inner dark:shadow-none group-hover:rotate-12 transition-transform duration-500">
                                    <i class="fas fa-bed text-2xl"></i>
                                </div>
                                <span class="text-xs font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest">{{ $assignedShift->name ?? 'Hari Libur' }}</span>
                                <div class="mt-2 text-[9px] font-bold text-slate-400 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-lg">OFF DUTY</div>
                            @else
                                <div class="w-16 h-16 rounded-3xl text-white flex items-center justify-center mb-4 shadow-lg shadow-indigo-200 dark:shadow-none transform group-hover:scale-110 group-hover:-rotate-6 transition-all duration-500 relative"
                                     style="background-color: {{ $assignedShift->color ?? '#6366f1' }}">
                                     <div class="absolute inset-0 bg-white/20 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                    <i class="fas fa-briefcase text-2xl relative z-10"></i>
                                </div>
                                <span class="text-sm font-black text-slate-800 dark:text-slate-100 line-clamp-1 uppercase tracking-tight">{{ $assignedShift->name }}</span>
                                <div class="mt-2 text-[10px] font-black text-indigo-600 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-500/15 border border-indigo-100 dark:border-indigo-500/30 px-3 py-1.5 rounded-xl uppercase tracking-widest shadow-sm dark:shadow-none">
                                    {{ \Carbon\Carbon::parse($assignedShift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($assignedShift->end_time)->format('H:i') }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- [BARU] Panduan Alur Kerja & Keterkaitan dengan Shift Karyawan --}}
                <div class="mt-6 p-5 rounded-2xl bg-slate-50/80 dark:bg-slate-950/60 border border-slate-100 dark:border-slate-800 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="flex items-start sm:items-center gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-500/15 border border-indigo-100 dark:border-indigo-500/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                            <i class="fas fa-route text-sm"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">Alur Kerja Pola Mingguan &rarr; Shift Karyawan</p>
                            <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-0.5">
                                1. Buat template di <strong>Master Shift</strong> &rarr; 2. Petakan hari kerja Senin–Minggu di <strong>Sesuaikan Pola</strong> &rarr; 3. Pilih opsi sinkronisasi saat menyimpan untuk memperbarui jadwal shift karyawan aktif.
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="showPatternModal = true" class="px-5 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest hover:bg-indigo-50 dark:hover:bg-slate-700 transition shrink-0">
                        <i class="fas fa-sync-alt mr-1.5"></i> Atur & Sinkronkan
                    </button>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- 4. MODAL UBAH POLA (GLASSMORPHISM) --}}
        {{-- ========================================================= --}}
        <div x-show="showPatternModal" style="display: none;" 
            class="fixed inset-0 z-[100] overflow-y-auto" 
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            
            {{-- Backdrop --}}
            <div x-show="showPatternModal" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="showPatternModal = false"
                 class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                {{-- Modal Panel --}}
                <div x-show="showPatternModal" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-[2.5rem] bg-white dark:bg-slate-900 text-left shadow-2xl shadow-indigo-900/20 dark:shadow-black/60 transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-100 dark:border-slate-800">
                    
                    <form id="patternModalForm" action="{{ route('admin.master.jadwal.shift.pattern.update') }}" method="POST">
                        @csrf @method('PUT')
                        
                        <div class="bg-white dark:bg-slate-900 px-8 pb-6 pt-8 relative overflow-hidden">
                            <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>
                            
                            <div class="flex items-start justify-between mb-6 border-b border-slate-100 dark:border-slate-800 pb-6 relative z-10">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-200 dark:shadow-indigo-950/50">
                                        <i class="fas fa-calendar-alt text-2xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-slate-800 dark:text-white tracking-tight" id="modal-title">Konfigurasi Pola</h3>
                                        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-widest mt-1">Tentukan mapping shift harian (Senin - Minggu)</p>
                                    </div>
                                </div>
                                <button type="button" @click="showPatternModal = false" class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-800 text-slate-400 dark:text-slate-400 hover:bg-rose-50 dark:hover:bg-rose-500/15 hover:text-rose-500 dark:hover:text-rose-400 transition-colors flex items-center justify-center">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            {{-- [BARU] Tombol Preset Cepat Pola Mingguan --}}
                            @if($firstWorkShift)
                            <div class="mb-5 flex flex-wrap items-center justify-between gap-2 p-3 rounded-2xl bg-slate-50 dark:bg-slate-950/70 border border-slate-100 dark:border-slate-800 relative z-10">
                                <span class="text-[9px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest ml-1">Preset Cepat:</span>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" onclick="applyPatternPreset('6days', '{{ $firstWorkShift->id }}', '{{ $firstHolidayShift->id ?? '' }}')"
                                            class="px-3 py-1.5 rounded-xl bg-emerald-600 text-white text-[9px] font-black uppercase tracking-wider hover:bg-emerald-700 transition shadow-sm">
                                        6 Hari (Sen–Sab)
                                    </button>
                                    <button type="button" onclick="applyPatternPreset('5days', '{{ $firstWorkShift->id }}', '{{ $firstHolidayShift->id ?? '' }}')"
                                            class="px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[9px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-wider hover:bg-indigo-50 dark:hover:bg-slate-700 transition">
                                        5 Hari (Sen–Jum)
                                    </button>
                                </div>
                            </div>
                            @endif

                            {{-- [DIPERBARUI] Selalu menampilkan 7 Hari (Senin - Minggu) --}}
                            <div class="space-y-3.5 max-h-[46vh] overflow-y-auto pr-2 custom-scrollbar relative z-10">
                                @foreach($defaultDays as $dayNum => $dayName)
                                    @php
                                        $p = $normalizedPatternMap->get($dayNum) ?? $patternMap->get($dayNum);
                                        $selectedShiftId = $p ? $p->shift_id : '';
                                        if (!$hasConfiguredWorkShift && $firstWorkShift) {
                                            $selectedShiftId = ($dayNum <= 6) ? $firstWorkShift->id : ($firstHolidayShift->id ?? '');
                                        }
                                        $selectedShiftObj = $selectedShiftId ? $shifts->firstWhere('id', $selectedShiftId) : null;
                                        $rowIsHoliday = !$selectedShiftObj || ($selectedShiftObj->is_holiday ?? false);
                                    @endphp
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 bg-slate-50/50 dark:bg-slate-950/60 hover:bg-slate-50 dark:hover:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800 transition-colors group">
                                        <div class="flex items-center justify-between sm:justify-start gap-3 w-full sm:w-2/5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-[10px] font-black text-slate-400 dark:text-slate-300 group-hover:text-indigo-500 dark:group-hover:text-indigo-400 transition-colors shadow-sm dark:shadow-none">
                                                    {{ substr($dayName, 0, 1) }}
                                                </div>
                                                <div>
                                                    <span class="text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-widest">{{ $dayName }}</span>
                                                    @if($dayNum === $todayDayNumber)
                                                        <span class="block text-[8px] font-black text-indigo-500 dark:text-indigo-400 uppercase tracking-widest">Hari Ini</span>
                                                    @endif
                                                </div>
                                            </div>
                                            {{-- [BARU] Badge Status Dinamis Kerja / Libur --}}
                                            <span id="pattern-badge-{{ $dayNum }}" class="text-[8px] font-black uppercase px-2 py-0.5 rounded-md {{ $rowIsHoliday ? 'bg-rose-100 dark:bg-rose-500/15 text-rose-600 dark:text-rose-400' : 'bg-emerald-100 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400' }}">
                                                {{ $rowIsHoliday ? 'Libur' : 'Kerja' }}
                                            </span>
                                        </div>
                                        
                                        <div class="w-full sm:w-3/5 relative">
                                            <select name="patterns[{{ $dayNum }}]" id="pattern-select-{{ $dayNum }}" onchange="updateModalDayBadge({{ $dayNum }})" class="w-full text-xs font-bold text-slate-700 dark:text-slate-100 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 bg-white dark:bg-slate-900 py-3 pl-4 pr-10 shadow-sm dark:shadow-none cursor-pointer appearance-none transition-all">
                                                <option value="" data-holiday="1" {{ empty($selectedShiftId) ? 'selected' : '' }}>-- HARI LIBUR / OFF DUTY --</option>
                                                @foreach($shifts as $s)
                                                    <option value="{{ $s->id }}" data-holiday="{{ $s->is_holiday ? '1' : '0' }}" {{ $selectedShiftId == $s->id ? 'selected' : '' }}>
                                                        {{ strtoupper($s->name) }} 
                                                        @if(!$s->is_holiday)
                                                            ({{ \Carbon\Carbon::parse($s->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($s->end_time)->format('H:i') }})
                                                        @else
                                                            (LIBUR)
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="absolute inset-y-0 right-0 flex items-center px-3.5 pointer-events-none text-slate-400 dark:text-slate-500">
                                                <i class="fas fa-chevron-down text-xs"></i>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- [BARU] Opsi Sinkronisasi Pola ke Shift Karyawan --}}
                            <div class="mt-5 p-4 bg-indigo-50/60 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-500/30 rounded-2xl relative z-10">
                                <label class="block text-[10px] font-black text-indigo-900 dark:text-indigo-200 uppercase tracking-widest mb-2">
                                    <i class="fas fa-users-cog mr-1.5 text-indigo-500"></i> Sinkronisasi ke Data Shift Karyawan
                                </label>
                                <select name="sync_mode" class="w-full text-xs font-bold text-slate-700 dark:text-slate-100 border border-indigo-200 dark:border-indigo-500/30 rounded-xl bg-white dark:bg-slate-900 py-2.5 px-3.5 outline-none focus:border-indigo-500 transition-all">
                                    <option value="today" selected>Terapkan jadwal pola HARI INI ({{ $defaultDays[$todayDayNumber] }}) ke semua karyawan aktif</option>
                                    <option value="default_work">Tetapkan shift kerja utama dari pola ini ke semua karyawan aktif</option>
                                    <option value="none">Hanya simpan pola mingguan default (tanpa mengubah shift karyawan)</option>
                                </select>
                            </div>
                        </div>

                        <div class="bg-slate-50 dark:bg-slate-950/60 px-8 py-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" @click="showPatternModal = false" class="w-full sm:w-auto px-8 py-3.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-300 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-100 dark:hover:bg-slate-700 transition shadow-sm dark:shadow-none">
                                Batalkan
                            </button>
                            <button type="submit" class="w-full sm:w-auto px-10 py-3.5 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-indigo-700 shadow-xl shadow-indigo-100 dark:shadow-indigo-950/50 transition active:scale-95 flex items-center justify-center gap-2">
                                <i class="fas fa-check-circle"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT JAVASCRIPT --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // [BARU] Helper normalisasi format waktu HH:mm untuk <input type="time">
    function normalizeTimeForInput(val) {
        if (!val) return '';
        const str = String(val);
        const match = str.match(/(\d{2}:\d{2})/);
        return match ? match[1] : str;
    }

    // --- 1. EDIT MODE ---
    function editShift(data) {
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit text-amber-500 text-xl"></i> Edit Template Shift';
        
        document.getElementById('nameInput').value = data.name;
        document.getElementById('startInput').value = normalizeTimeForInput(data.start_time);
        document.getElementById('endInput').value = normalizeTimeForInput(data.end_time);
        document.getElementById('breakStartInput').value = normalizeTimeForInput(data.break_start);
        document.getElementById('breakEndInput').value = normalizeTimeForInput(data.break_end);
        document.getElementById('tolInput').value = data.late_tolerance;
        document.getElementById('colorInput').value = data.color;
        document.getElementById('nightInput').checked = data.is_night_shift == 1;
        if (document.getElementById('holidayInput')) {
            document.getElementById('holidayInput').checked = data.is_holiday == 1;
        }

        let baseUrl = "{{ url('admin/master/jadwal/shift') }}";
        document.getElementById('shiftForm').action = baseUrl + '/' + data.id;
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        let btnSubmit = document.getElementById('btnSubmit');
        btnSubmit.classList.replace('bg-indigo-600', 'bg-amber-500');
        btnSubmit.classList.replace('hover:bg-indigo-700', 'hover:bg-amber-600');
        btnSubmit.classList.replace('shadow-indigo-100', 'shadow-amber-100');
        btnSubmit.classList.replace('dark:shadow-indigo-950/50', 'dark:shadow-amber-950/50');
        btnSubmit.innerText = 'Update Data';
        
        document.getElementById('btnCancel').classList.remove('hidden');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // --- 2. RESET FORM ---
    function resetForm() {
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus-circle text-indigo-500 dark:text-indigo-400 text-xl"></i> Buat Shift Baru';
        document.getElementById('shiftForm').reset();
        document.getElementById('shiftForm').action = "{{ route('admin.master.jadwal.shift.store') }}";
        document.getElementById('methodField').innerHTML = '';

        let btnSubmit = document.getElementById('btnSubmit');
        btnSubmit.classList.replace('bg-amber-500', 'bg-indigo-600');
        btnSubmit.classList.replace('hover:bg-amber-600', 'hover:bg-indigo-700');
        btnSubmit.classList.replace('shadow-amber-100', 'shadow-indigo-100');
        btnSubmit.classList.replace('dark:shadow-amber-950/50', 'dark:shadow-indigo-950/50');
        btnSubmit.innerText = 'Simpan Data';
        
        document.getElementById('btnCancel').classList.add('hidden');
    }

    // --- 3. DELETE ALERT ---
    function confirmDelete(id, name) {
        const isDark = document.documentElement.classList.contains('dark');
        Swal.fire({
            title: 'Hapus Shift?',
            text: "Template " + name + " akan dihapus secara permanen.",
            icon: 'warning',
            background: isDark ? '#0f172a' : '#ffffff',
            color: isDark ? '#f8fafc' : '#1e293b',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: isDark ? '#334155' : '#94a3b8',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-[2rem] border border-slate-200 dark:border-slate-800',
                confirmButton: 'rounded-xl px-6 py-3 text-[10px] font-black uppercase tracking-widest',
                cancelButton: 'rounded-xl px-6 py-3 text-[10px] font-black uppercase tracking-widest'
            }
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('delete-form-' + id).submit();
        })
    }

    // --- 4. [BARU] AUTO-FILL JAM 00:00 JIKA DITANDAI HARI LIBUR ---
    function toggleHolidayFields() {
        const isHoliday = document.getElementById('holidayInput').checked;
        if (isHoliday) {
            if (!document.getElementById('startInput').value) document.getElementById('startInput').value = '00:00';
            if (!document.getElementById('endInput').value) document.getElementById('endInput').value = '00:00';
        }
    }

    // --- 5. [BARU] UPDATE BADGE STATUS KERJA / LIBUR DI DALAM MODAL ---
    function updateModalDayBadge(dayNum) {
        const select = document.getElementById('pattern-select-' + dayNum);
        const badge = document.getElementById('pattern-badge-' + dayNum);
        if (!select || !badge) return;

        const selectedOpt = select.options[select.selectedIndex];
        const isHoliday = !select.value || (selectedOpt && selectedOpt.getAttribute('data-holiday') === '1');

        if (isHoliday) {
            badge.textContent = 'Libur';
            badge.className = 'text-[8px] font-black uppercase px-2 py-0.5 rounded-md bg-rose-100 dark:bg-rose-500/15 text-rose-600 dark:text-rose-400';
        } else {
            badge.textContent = 'Kerja';
            badge.className = 'text-[8px] font-black uppercase px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400';
        }
    }

    // --- 6. [BARU] PRESET CEPAT POLA MINGGUAN (5 HARI / 6 HARI) ---
    function applyPatternPreset(type, workShiftId, holidayShiftId) {
        for (let day = 1; day <= 7; day++) {
            const select = document.getElementById('pattern-select-' + day);
            if (!select) continue;
            if (type === '5days') {
                select.value = (day <= 5) ? workShiftId : holidayShiftId;
            } else if (type === '6days') {
                select.value = (day <= 6) ? workShiftId : holidayShiftId;
            }
            updateModalDayBadge(day);
        }
    }

    // --- 7. [BARU] FILTER PENCARIAN TEMPLATE SHIFT ---
    function filterShiftTable() {
        const input = document.getElementById('searchShiftInput');
        if (!input) return;
        const keyword = input.value.toLowerCase();
        document.querySelectorAll('#shiftTable .shift-row').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(keyword) ? '' : 'none';
        });
    }

    // --- 8. [BARU] LOADING INDICATOR SAAT SIMPAN POLA MODAL ---
    document.addEventListener('DOMContentLoaded', function() {
        const patternForm = document.getElementById('patternModalForm');
        if (patternForm) {
            patternForm.addEventListener('submit', function() {
                const isDark = document.documentElement.classList.contains('dark');
                Swal.fire({
                    title: 'Menyimpan Pola...',
                    text: 'Memperbarui pola kerja mingguan & sinkronisasi jadwal.',
                    background: isDark ? '#0f172a' : '#ffffff',
                    color: isDark ? '#f8fafc' : '#1e293b',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
            });
        }
    });
</script>

<style>
    /* Styling Scrollbar Modern untuk Modal */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* Dark Mode Scrollbar */
    .dark .custom-scrollbar::-webkit-scrollbar-track,
    html.dark .custom-scrollbar::-webkit-scrollbar-track { background: #0f172a; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb,
    html.dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover,
    html.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #475569; }
</style>
@endsection