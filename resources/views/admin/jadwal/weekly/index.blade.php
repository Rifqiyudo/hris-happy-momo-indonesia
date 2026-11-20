@extends('admin.layouts.master')
@section('title', 'Pola Kerja Mingguan')

@section('content')
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

    $todayDayNumber = \Carbon\Carbon::now()->dayOfWeekIso; // 1 (Senin) - 7 (Minggu)
    $firstWorkShift = collect($shifts ?? [])->where('is_holiday', false)->first() ?? collect($shifts ?? [])->first();

    // Fallback otomatis 7 hari jika $schedules dari database masih kosong
    $schedules = collect($schedules ?? []);
    if ($schedules->isEmpty()) {
        $defaultWorkShift = $firstWorkShift;
        $schedules = collect($defaultDays)->map(function ($name, $num) use ($defaultWorkShift) {
            return (object) [
                'id'          => null,
                'day_of_week' => $num,
                'day_number'  => $num,
                'day_name'    => $name,
                'shift_id'    => $num <= 6 ? ($defaultWorkShift->id ?? null) : null,
                'is_off'      => $num > 6,
            ];
        })->values();
    }

    // [BARU] Urutkan Senin (1) s/d Minggu (7) & cek apakah database sudah memiliki minimal 1 hari kerja aktif
    $schedules = $schedules->sortBy(function ($item) {
        return (int) ($item->day_of_week ?? $item->day_number ?? 99);
    })->values();

    $hasAnyConfiguredWorkDay = $schedules->contains(function ($item) {
        $off = (bool) ($item->is_off ?? $item->is_holiday ?? false);
        return !$off && !empty($item->shift_id);
    });

    // [BARU] Jika semua baris DB terlanjur bernilai shift_id = null / is_off = true, terapkan default Opsi A (Senin-Sabtu Kerja, Minggu Libur)
    $isAutoDefaultPreview = false;
    if (!$hasAnyConfiguredWorkDay && $firstWorkShift) {
        $isAutoDefaultPreview = true;
        $schedules = $schedules->map(function ($item, $idx) use ($defaultDays, $firstWorkShift) {
            $dayNum = (int) ($item->day_of_week ?? $item->day_number ?? ($idx + 1));
            $isSunday = ($dayNum >= 7);
            return (object) [
                'id'          => $item->id ?? null,
                'day_of_week' => $dayNum,
                'day_number'  => $dayNum,
                'day_name'    => $item->day_name ?? ($defaultDays[$dayNum] ?? 'Hari ' . $dayNum),
                'shift_id'    => $isSunday ? null : $firstWorkShift->id,
                'is_off'      => $isSunday,
            ];
        })->values();
    }

    // [BARU] Ambil informasi jadwal khusus HARI INI untuk ditampilkan pada Header
    $shiftsById = collect($shifts ?? [])->keyBy('id');
    $todayItem = $schedules->first(function ($item) use ($todayDayNumber) {
        return (int) ($item->day_of_week ?? $item->day_number ?? 0) === $todayDayNumber;
    });
    $todayIsOff = $todayItem ? (bool) ($todayItem->is_off ?? $todayItem->is_holiday ?? false) : ($todayDayNumber === 7);
    $todayShiftObj = (!$todayIsOff && $todayItem && !empty($todayItem->shift_id))
        ? $shiftsById->get($todayItem->shift_id)
        : null;
@endphp

<div class="max-w-7xl mx-auto pb-20 px-4 pt-6 space-y-8">

    {{-- HEADER & PRESET CEPAT POLA MINGGUAN --}}
    <div class="bg-white dark:bg-slate-900 p-6 md:p-8 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm dark:shadow-none flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-44 h-44 bg-indigo-50 dark:bg-indigo-500/10 rounded-full blur-3xl opacity-60 pointer-events-none"></div>

        <div class="relative z-10 flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-200 dark:shadow-indigo-950/50 shrink-0">
                <i class="fas fa-calendar-week text-xl"></i>
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <h1 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white tracking-tight">Pola Kerja Mingguan</h1>
                    {{-- [BARU] Badge Status Hari Berjalan --}}
                    @if(!$todayIsOff && $todayShiftObj)
                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">
                            Hari Ini ({{ $defaultDays[$todayDayNumber] }}): {{ $todayShiftObj->name }} ({{ \Carbon\Carbon::parse($todayShiftObj->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($todayShiftObj->end_time)->format('H:i') }})
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-rose-50 dark:bg-rose-500/15 text-rose-600 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30">
                            Hari Ini ({{ $defaultDays[$todayDayNumber] }}): Libur / Off Duty
                        </span>
                    @endif
                </div>
                <p class="text-xs md:text-sm text-slate-400 dark:text-slate-400 font-medium mt-1">Atur jadwal shift default (Senin–Minggu) sebagai acuan absensi dan rotasi shift karyawan.</p>
            </div>
        </div>

        {{-- Tombol Preset Cepat --}}
        @if($firstWorkShift)
        <div class="relative z-10 flex flex-wrap items-center gap-2 w-full lg:w-auto">
            <span class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mr-1">Preset Cepat:</span>
            <button type="button" onclick="applyWeeklyPreset('6days', '{{ $firstWorkShift->id }}')"
                class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white border border-emerald-600 text-[10px] font-black uppercase tracking-wider transition shadow-sm cursor-pointer">
                <i class="fas fa-calendar-check mr-1.5"></i> 6 Hari (Sen–Sab)
            </button>
            <button type="button" onclick="applyWeeklyPreset('5days', '{{ $firstWorkShift->id }}')"
                class="px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-500/20 text-slate-700 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-300 border border-slate-200 dark:border-slate-700 text-[10px] font-black uppercase tracking-wider transition cursor-pointer">
                <i class="fas fa-briefcase mr-1.5 text-indigo-500"></i> 5 Hari (Sen–Jum)
            </button>
        </div>
        @endif
    </div>

    {{-- [BARU] NOTIFIKASI BANNER SAAT POLA OTOMATIS DISIAPKAN (SENIN-SABTU AKTIF) --}}
    @if($isAutoDefaultPreview && $firstWorkShift)
    <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 rounded-2xl p-4 md:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-start sm:items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0">
                <i class="fas fa-info text-xs"></i>
            </div>
            <div>
                <p class="text-xs font-black text-amber-900 dark:text-amber-200 uppercase tracking-tight">Pola Default 6 Hari Kerja (Senin–Sabtu) Siap Disimpan</p>
                <p class="text-[11px] font-medium text-amber-700 dark:text-amber-300/90 mt-0.5">
                    Jadwal Senin–Sabtu telah dipetakan otomatis ke shift <strong>{{ $firstWorkShift->name }}</strong> dan Minggu berstatus Libur. Klik tombol <strong>Simpan Pola</strong> untuk menyimpan permanen ke database.
                </p>
            </div>
        </div>
    </div>
    @endif

    <form id="weeklyScheduleForm" action="{{ route('admin.jadwal.weekly.update') }}" method="POST" class="space-y-6">
        @csrf

        {{-- PANEL SINKRONISASI KE SHIFT KARYAWAN --}}
        <div class="bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-500/25 rounded-2xl p-4 md:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start sm:items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                    <i class="fas fa-users-cog text-xs"></i>
                </div>
                <div>
                    <p class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">Integrasi Pola dengan Shift Karyawan (Opsi A)</p>
                    <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Pilih apakah perubahan pola ini juga langsung memperbarui shift kerja karyawan aktif.</p>
                </div>
            </div>
            <div class="w-full md:w-96 shrink-0">
                <select name="sync_mode" class="w-full text-xs font-bold text-slate-700 dark:text-slate-100 bg-white dark:bg-slate-900 border border-indigo-200 dark:border-indigo-500/30 rounded-xl px-3.5 py-2.5 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition">
                    <option value="today" selected>Terapkan shift HARI INI ({{ $defaultDays[$todayDayNumber] }}) ke karyawan aktif</option>
                    <option value="primary">Terapkan shift kerja utama pola ini ke karyawan aktif</option>
                    <option value="none">Hanya simpan pola mingguan default</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($schedules as $item)
                @php
                    $dayNumber = $item->day_of_week ?? $item->day_number ?? ($loop->index + 1);
                    $dayName   = $item->day_name ?? ($defaultDays[$dayNumber] ?? 'Hari ' . $dayNumber);
                    $isOff     = (bool) ($item->is_off ?? $item->is_holiday ?? false);
                    $isToday   = ($dayNumber == $todayDayNumber);
                    $selectedShift = (!$isOff && !empty($item->shift_id)) ? $shiftsById->get($item->shift_id) : null;
                @endphp

                {{-- KARTU HARI (.day-card) --}}
                <div class="day-card bg-white dark:bg-slate-900 rounded-[2.5rem] p-6 border {{ $isToday ? 'border-indigo-400 dark:border-indigo-500/60 ring-2 ring-indigo-500/15' : 'border-slate-100 dark:border-slate-800' }} shadow-xl shadow-slate-200/40 dark:shadow-none hover:border-indigo-200 dark:hover:border-indigo-500/40 transition-all duration-300 flex flex-col justify-between gap-5 relative overflow-hidden">
                    
                    <input type="hidden" name="schedules[{{ $dayNumber }}][id]" value="{{ $item->id ?? '' }}">
                    <input type="hidden" name="schedules[{{ $dayNumber }}][day_of_week]" value="{{ $dayNumber }}">
                    <input type="hidden" name="schedules[{{ $dayNumber }}][day_name]" value="{{ $dayName }}">

                    {{-- Header Kartu Hari --}}
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl {{ $isToday ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200 dark:shadow-none' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }} font-black text-xs flex items-center justify-center">
                                {{ $dayNumber }}
                            </div>
                            <div>
                                <h3 class="text-base font-black text-slate-800 dark:text-white tracking-tight">{{ $dayName }}</h3>
                                @if($isToday)
                                    <span class="text-[9px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest">Hari Ini</span>
                                @else
                                    <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Jadwal Default</span>
                                @endif
                            </div>
                        </div>

                        {{-- Status Badge (.status-badge) --}}
                        @if($isOff)
                            <span class="status-badge text-[9px] font-black uppercase px-2.5 py-1 rounded-lg bg-rose-100 dark:bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-transparent dark:border-rose-500/30">
                                Libur
                            </span>
                        @else
                            <span class="status-badge text-[9px] font-black uppercase px-2.5 py-1 rounded-lg bg-emerald-100 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-transparent dark:border-emerald-500/30">
                                Kerja
                            </span>
                        @endif
                    </div>

                    {{-- Dropdown Pilihan Shift (.shift-select) --}}
                    <div>
                        <label class="block text-[9px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest mb-2">Template Shift</label>
                        <div class="relative">
                            <select name="schedules[{{ $dayNumber }}][shift_id]"
                                {{ $isOff ? 'disabled' : '' }}
                                class="shift-select w-full bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-700 rounded-2xl pl-4 pr-9 py-3 text-xs font-bold text-slate-700 dark:text-slate-100 outline-none focus:bg-white dark:focus:bg-slate-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all appearance-none cursor-pointer {{ $isOff ? 'opacity-50 grayscale pointer-events-none' : '' }}">
                                <option value="" data-time="OFF DUTY">-- Pilih Shift Kerja --</option>
                                @foreach($shifts ?? [] as $shift)
                                    @if(!($shift->is_holiday ?? false))
                                        @php
                                            $timeRange = \Carbon\Carbon::parse($shift->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($shift->end_time)->format('H:i');
                                        @endphp
                                        <option value="{{ $shift->id }}"
                                                data-time="{{ $timeRange }}"
                                                {{ ($item->shift_id ?? '') == $shift->id ? 'selected' : '' }}>
                                            {{ $shift->name }} ({{ $timeRange }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3.5 pointer-events-none text-slate-400 dark:text-slate-500">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>

                        {{-- [BARU] Indikator Jam Kerja Aktif / Off Duty --}}
                        <div class="mt-2.5 flex items-center justify-between px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-950/50 border border-slate-100 dark:border-slate-800/80">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Jam Operasional</span>
                            <span class="shift-time-preview text-[10px] font-black {{ (!$isOff && $selectedShift) ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500' }}">
                                @if(!$isOff && $selectedShift)
                                    {{ \Carbon\Carbon::parse($selectedShift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($selectedShift->end_time)->format('H:i') }}
                                @else
                                    OFF DUTY
                                @endif
                            </span>
                        </div>
                    </div>

                    {{-- Toggle Hari Libur (.is-off-check) --}}
                    <label class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50/80 dark:bg-slate-950/60 border border-slate-100 dark:border-slate-800 cursor-pointer hover:bg-slate-100/70 dark:hover:bg-slate-800/60 transition-colors">
                        <div class="flex items-center gap-2.5">
                            <i class="fas fa-bed text-xs text-rose-500 dark:text-rose-400"></i>
                            <span class="text-[11px] font-black text-slate-700 dark:text-slate-200 uppercase tracking-tight">Tandai Libur</span>
                        </div>
                        <input type="checkbox"
                               name="schedules[{{ $dayNumber }}][is_off]"
                               value="1"
                               {{ $isOff ? 'checked' : '' }}
                               class="is-off-check w-4 h-4 rounded border-slate-300 dark:border-slate-600 dark:bg-slate-800 text-rose-600 focus:ring-rose-500 cursor-pointer">
                    </label>
                </div>
            @endforeach

            {{-- Perbaikan Tailwind: min-h-[180px] menjadi min-h-45 --}}
            <div class="lg:col-span-1">
                <button type="button" id="btnSubmit" class="w-full h-full min-h-45 min-h-[220px] bg-slate-900 dark:bg-indigo-600 text-white rounded-[2.5rem] font-black uppercase text-xs tracking-widest hover:bg-indigo-600 dark:hover:bg-indigo-500 transition-all duration-300 shadow-2xl shadow-slate-300 dark:shadow-indigo-950/50 hover:shadow-indigo-200 active:scale-95 flex flex-col items-center justify-center gap-4 group cursor-pointer">
                    <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-save text-xl"></i>
                    </div>
                    <span>Simpan Pola</span>
                </button>
            </div>
        </div>
    </form>
</div>

{{-- 4. LOGIC JAVASCRIPT --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // PERBAIKAN: Tangkap data Blade di luar DOMContentLoaded atau di awal
    // Ini menghilangkan error "Decorators are not valid" di VS Code
    const sessionSuccess = "{{ session('success') }}";
    const sessionError = "{{ session('error') }}";

    // [BARU] Helper pembaruan teks jam kerja real-time di kartu hari
    function updateCardTimePreview(card) {
        const check = card.querySelector('.is-off-check');
        const select = card.querySelector('.shift-select');
        const preview = card.querySelector('.shift-time-preview');
        if (!preview || !select) return;

        if (check && check.checked) {
            preview.textContent = 'OFF DUTY';
            preview.className = 'shift-time-preview text-[10px] font-black text-slate-400 dark:text-slate-500';
            return;
        }

        const selectedOption = select.options[select.selectedIndex];
        const timeText = selectedOption ? selectedOption.getAttribute('data-time') : null;
        if (select.value && timeText) {
            preview.textContent = timeText;
            preview.className = 'shift-time-preview text-[10px] font-black text-indigo-600 dark:text-indigo-400';
        } else {
            preview.textContent = 'BELUM DIPILIH';
            preview.className = 'shift-time-preview text-[10px] font-black text-amber-500';
        }
    }

    // Fungsi Preset Cepat (5 Hari / 6 Hari Kerja)
    function applyWeeklyPreset(type, defaultShiftId) {
        const cards = document.querySelectorAll('.day-card');
        cards.forEach((card, index) => {
            const dayNum = index + 1;
            const check = card.querySelector('.is-off-check');
            const select = card.querySelector('.shift-select');
            if (!check || !select) return;

            const shouldBeOff = (type === '5days') ? (dayNum > 5) : (dayNum > 6);
            check.checked = shouldBeOff;
            check.dispatchEvent(new Event('change'));

            if (!shouldBeOff && defaultShiftId) {
                select.value = defaultShiftId;
            }
            updateCardTimePreview(card);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const checkBoxes = document.querySelectorAll('.is-off-check');
        const shiftSelects = document.querySelectorAll('.shift-select');
        const form = document.getElementById('weeklyScheduleForm');
        const btnSubmit = document.getElementById('btnSubmit');
        const isDark = document.documentElement.classList.contains('dark');

        // [BARU] Update jam operasional real-time saat dropdown shift diganti
        shiftSelects.forEach(select => {
            select.addEventListener('change', function() {
                const card = this.closest('.day-card');
                if (card) updateCardTimePreview(card);
            });
        });

        // Logic Checkbox (Tetap sama + dukungan dark mode + update preview jam)
        checkBoxes.forEach(check => {
            check.addEventListener('change', function() {
                const card = this.closest('.day-card');
                const select = card.querySelector('.shift-select');
                const badge = card.querySelector('.status-badge');
                if (this.checked) {
                    select.disabled = true;
                    select.classList.add('opacity-50', 'grayscale', 'pointer-events-none');
                    select.value = "";
                    badge.textContent = 'Libur';
                    badge.className = 'status-badge text-[9px] font-black uppercase px-2.5 py-1 rounded-lg bg-rose-100 dark:bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-transparent dark:border-rose-500/30';
                } else {
                    select.disabled = false;
                    select.classList.remove('opacity-50', 'grayscale', 'pointer-events-none');
                    badge.textContent = 'Kerja';
                    badge.className = 'status-badge text-[9px] font-black uppercase px-2.5 py-1 rounded-lg bg-emerald-100 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-transparent dark:border-emerald-500/30';
                }
                updateCardTimePreview(card);
            });
        });

        // SweetAlert Confirmation
        btnSubmit.addEventListener('click', function() {
            // Validasi agar hari kerja wajib memilih shift
            let missingDay = null;
            document.querySelectorAll('.day-card').forEach(card => {
                const check = card.querySelector('.is-off-check');
                const select = card.querySelector('.shift-select');
                const title = card.querySelector('h3')?.textContent?.trim();
                if (check && !check.checked && select && !select.value && !missingDay) {
                    missingDay = title;
                }
            });

            if (missingDay) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Shift Belum Dipilih',
                    text: 'Hari ' + missingDay + ' berstatus Kerja, silakan pilih template shift atau centang Tandai Libur.',
                    background: isDark ? '#0f172a' : '#ffffff',
                    color: isDark ? '#f8fafc' : '#1e293b',
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }

            Swal.fire({
                title: 'Simpan Perubahan?',
                text: "Pola jadwal ini akan menjadi acuan default absensi.",
                icon: 'question',
                background: isDark ? '#0f172a' : '#ffffff',
                color: isDark ? '#f8fafc' : '#1e293b',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: isDark ? '#334155' : '#94a3b8',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        background: isDark ? '#0f172a' : '#ffffff',
                        color: isDark ? '#f8fafc' : '#1e293b',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    form.submit();
                }
            });
        });

        // PERBAIKAN: Gunakan variabel JS yang sudah diisi data Blade
        if (sessionSuccess) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: sessionSuccess,
                background: isDark ? '#0f172a' : '#ffffff',
                color: isDark ? '#f8fafc' : '#1e293b',
                showConfirmButton: false,
                timer: 2500
            });
        }

        if (sessionError) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: sessionError,
                background: isDark ? '#0f172a' : '#ffffff',
                color: isDark ? '#f8fafc' : '#1e293b'
            });
        }
    });
</script>
@endsection