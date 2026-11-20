@extends('layouts.app') 
@section('title', 'Riwayat Klaim (Reimbursement)')

@section('content')
{{-- [BARU] Anti-FOUC Theme Sync (Membaca status Dark/Light Mode dari localStorage sebelum elemen dirender) --}}
<script>
    (function () {
        const savedTheme = localStorage.getItem('employee_theme') || localStorage.getItem('theme') || 'light';
        const isDark = savedTheme === 'dark';
        document.documentElement.classList.toggle('dark', isDark);
        document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
    })();
</script>

{{-- [BARU] Bidirectional Light & Dark Mode Guard (Menetralkan Paksaan OS Dark Mode agar Halaman 100% Netral) --}}
<style>
    .theme-hidden { display: none !important; }

    /* Pastikan Header & Tombol CTA Tersusun Vertikal Rapi Tanpa Bertabrakan di Bingkai Mobile */
    .reimburse-header-stack {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .reimburse-cta-btn {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    /* ========================================================================= */
    /* [BARU] 1. LIGHT MODE GUARD (Memastikan Mode Terang Benar-Benar Terang)    */
    /* ========================================================================= */
    html:not(.dark) { color-scheme: light; }
    html:not(.dark) body { background-color: #f1f5f9 !important; color: #1e293b !important; }
    html:not(.dark) main { background-color: #f8fafc !important; color: #1e293b !important; }

    html:not(.dark) .reimburse-page .dark\:bg-slate-900 {
        background-color: #ffffff !important;
    }
    html:not(.dark) .reimburse-page .dark\:bg-slate-950\/80,
    html:not(.dark) .reimburse-page .dark\:bg-slate-800\/90 {
        background-color: #f8fafc !important;
    }
    html:not(.dark) .reimburse-page .dark\:bg-slate-800 {
        background-color: #f1f5f9 !important;
    }
    html:not(.dark) .reimburse-page .dark\:bg-indigo-500\/10,
    html:not(.dark) .reimburse-page .dark\:bg-indigo-500\/15 {
        background-color: #eef2ff !important;
    }
    html:not(.dark) .reimburse-page .dark\:bg-rose-500\/15 {
        background-color: #fff1f2 !important;
    }
    html:not(.dark) .reimburse-page .dark\:border-slate-800,
    html:not(.dark) .reimburse-page .dark\:border-slate-700,
    html:not(.dark) .reimburse-page .dark\:border-slate-700\/80,
    html:not(.dark) .reimburse-page .dark\:border-slate-700\/70 {
        border-color: #e2e8f0 !important;
    }
    html:not(.dark) .reimburse-page .dark\:border-indigo-500\/30,
    html:not(.dark) .reimburse-page .dark\:border-indigo-500\/35 {
        border-color: #e0e7ff !important;
    }
    html:not(.dark) .reimburse-page .dark\:text-white:not(.reimburse-cta-btn):not(.reimburse-cta-btn *) {
        color: #1e293b !important;
    }
    html:not(.dark) .reimburse-page .reimburse-cta-btn,
    html:not(.dark) .reimburse-page .reimburse-cta-btn * {
        color: #ffffff !important;
    }
    html:not(.dark) .reimburse-page .dark\:text-slate-300 {
        color: #475569 !important;
    }
    html:not(.dark) .reimburse-page .dark\:text-slate-400 {
        color: #64748b !important;
    }
    html:not(.dark) .reimburse-page .dark\:text-amber-400 {
        color: #d97706 !important;
    }
    html:not(.dark) .reimburse-page .dark\:text-emerald-400 {
        color: #059669 !important;
    }
    html:not(.dark) .reimburse-page .dark\:text-indigo-400 {
        color: #4f46e5 !important;
    }
    html:not(.dark) .reimburse-page .dark\:text-rose-400 {
        color: #f43f5e !important;
    }
    html:not(.dark) nav,
    html:not(.dark) .bottom-nav,
    html:not(.dark) [class*="fixed"][class*="bottom-0"] {
        background-color: rgba(255, 255, 255, 0.95) !important;
        border-color: #f1f5f9 !important;
    }
    html:not(.dark) nav .border-white,
    html:not(.dark) .bottom-nav .border-white,
    html:not(.dark) [class*="fixed"][class*="bottom-0"] .border-white {
        border-color: #ffffff !important;
    }

    /* ========================================================================= */
    /* [BARU] 2. DARK MODE GUARD (Memastikan Mode Malam Konsisten Tanpa Bocor)   */
    /* ========================================================================= */
    html.dark { color-scheme: dark; }
    html.dark body { background-color: #000000 !important; color: #f1f5f9 !important; }
    html.dark main { background-color: #020617 !important; color: #f1f5f9 !important; }

    html.dark .reimburse-page .dark\:bg-slate-900 {
        background-color: #0f172a !important;
    }
    html.dark .reimburse-page .dark\:bg-slate-950\/80 {
        background-color: rgba(2, 6, 23, 0.85) !important;
    }
    html.dark .reimburse-page .dark\:bg-slate-800,
    html.dark .reimburse-page .dark\:bg-slate-800\/90 {
        background-color: #1e293b !important;
    }
    html.dark .reimburse-page .dark\:bg-indigo-500\/10,
    html.dark .reimburse-page .dark\:bg-indigo-500\/15 {
        background-color: rgba(99, 102, 241, 0.15) !important;
    }
    html.dark .reimburse-page .dark\:bg-rose-500\/15 {
        background-color: rgba(244, 63, 94, 0.15) !important;
    }
    html.dark .reimburse-page .dark\:border-slate-800,
    html.dark .reimburse-page .dark\:border-slate-700,
    html.dark .reimburse-page .dark\:border-slate-700\/80,
    html.dark .reimburse-page .dark\:border-slate-700\/70 {
        border-color: #1e293b !important;
    }
    html.dark .reimburse-page .dark\:text-white {
        color: #f8fafc !important;
    }
    html.dark .reimburse-page .dark\:text-slate-300 {
        color: #cbd5e1 !important;
    }
    html.dark .reimburse-page .dark\:text-slate-400 {
        color: #94a3b8 !important;
    }
    html.dark .reimburse-page .dark\:text-amber-400 {
        color: #fbbf24 !important;
    }
    html.dark .reimburse-page .dark\:text-emerald-400 {
        color: #34d399 !important;
    }
    html.dark .reimburse-page .dark\:text-indigo-400 {
        color: #818cf8 !important;
    }
    html.dark .reimburse-page .dark\:text-rose-400 {
        color: #fb7185 !important;
    }

    /* Hilangkan Kebocoran Warna Terang pada Navigasi Bawah & Tombol Sidik Jari Tengah */
    html.dark nav,
    html.dark .bottom-nav,
    html.dark [class*="fixed"][class*="bottom-0"] {
        background-color: rgba(15, 23, 42, 0.96) !important;
        border-color: #1e293b !important;
        box-shadow: 0 -8px 25px rgba(0, 0, 0, 0.45) !important;
    }
    html.dark nav .border-white,
    html.dark .bottom-nav .border-white,
    html.dark [class*="fixed"][class*="bottom-0"] .border-white {
        border-color: #020617 !important;
    }
    html.dark nav .shadow-indigo-200,
    html.dark .bottom-nav .shadow-indigo-200,
    html.dark [class*="fixed"][class*="bottom-0"] .shadow-indigo-200 {
        --tw-shadow-color: rgba(30, 27, 75, 0.9) !important;
        box-shadow: 0 10px 25px -5px rgba(30, 27, 75, 0.9) !important;
    }
    html.dark nav .text-gray-400,
    html.dark nav .text-slate-400,
    html.dark .bottom-nav .text-gray-400,
    html.dark .bottom-nav .text-slate-400 {
        color: #64748b !important;
    }
    html.dark #loader-overlay {
        background: rgba(2, 6, 23, 0.88) !important;
    }

    /* Adaptasi Status Badge Dinamis dari Accessor Model ($item->status_badge) saat Dark Mode */
    html.dark .reimburse-page .bg-amber-50,
    html.dark .reimburse-page .bg-amber-100 {
        background-color: rgba(245, 158, 11, 0.15) !important;
        border-color: rgba(245, 158, 11, 0.3) !important;
        color: #fbbf24 !important;
    }
    html.dark .reimburse-page .bg-emerald-50,
    html.dark .reimburse-page .bg-emerald-100,
    html.dark .reimburse-page .bg-green-50 {
        background-color: rgba(16, 185, 129, 0.15) !important;
        border-color: rgba(16, 185, 129, 0.3) !important;
        color: #34d399 !important;
    }
    html.dark .reimburse-page .bg-rose-50,
    html.dark .reimburse-page .bg-rose-100,
    html.dark .reimburse-page .bg-red-50 {
        background-color: rgba(244, 63, 94, 0.15) !important;
        border-color: rgba(244, 63, 94, 0.3) !important;
        color: #fb7185 !important;
    }
</style>

<div class="reimburse-page space-y-4 px-4 pt-4 pb-24 max-w-4xl mx-auto transition-colors duration-300">
    {{-- 1. HEADER SECTION (Dirapikan agar Judul & Tombol Ajukan Klaim Baru Simetris & Tidak Bertabrakan) --}}
    <div class="bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm dark:shadow-none relative overflow-hidden transition-colors duration-300">
        <div class="absolute right-0 top-0 w-36 h-36 bg-indigo-50 dark:bg-indigo-500/10 rounded-bl-full pointer-events-none opacity-60"></div>

        {{-- [BARU] Baris Atas Header: Tombol Kembali & Switcher Mode Terang / Malam --}}
        <div class="relative z-10 flex items-center justify-between gap-3 mb-4">
            <a href="{{ route('employee.dashboard') }}"
               class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-800/90 border border-slate-200/70 dark:border-slate-700/80 text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                <i class="fas fa-arrow-left text-[10px]"></i>
                <span>Beranda</span>
            </a>

            <button type="button"
                    onclick="toggleReimburseListTheme(event)"
                    title="Ganti Mode Terang / Malam"
                    class="w-9 h-9 bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-amber-400 rounded-xl border border-slate-200/80 dark:border-slate-700 flex items-center justify-center active:scale-95 transition hover:border-indigo-300 dark:hover:border-indigo-500/50 cursor-pointer shrink-0">
                <i class="fas fa-moon text-xs theme-icon-moon"></i>
                <i class="fas fa-sun text-xs theme-icon-sun theme-hidden" style="display: none;"></i>
            </button>
        </div>

        {{-- [BARU] Judul, Deskripsi & Tombol Ajukan Klaim Baru Tersusun Vertikal Rapi --}}
        <div class="relative z-10 reimburse-header-stack">
            <div class="min-w-0">
                <h1 class="text-xl font-black text-slate-800 dark:text-white tracking-tight leading-snug">
                    Klaim &amp; Reimbursement
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1 leading-relaxed">
                    Kelola dan pantau pengajuan biaya operasional Anda dengan mudah.
                </p>
            </div>

            <a href="{{ route('employee.reimbursement.create') }}" 
               class="reimburse-cta-btn w-full py-3.5 px-5 bg-gradient-to-r from-indigo-600 via-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest border border-indigo-400/30 transition-all shadow-lg shadow-indigo-200 dark:shadow-indigo-950/80 active:scale-[0.98]">
                <i class="fas fa-plus-circle text-sm"></i>
                <span>Ajukan Klaim Baru</span>
            </a>
        </div>
    </div>

    {{-- 2. SUMMARY CARDS (Dibuat Lebih Ringkas, Proporsional, dan Tidak Terlalu Besar) --}}
    <div class="grid grid-cols-3 gap-2.5 sm:gap-3">
        <div class="bg-white dark:bg-slate-900 p-3.5 sm:p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm dark:shadow-none flex flex-col justify-center text-center sm:text-left transition-colors">
            <p class="text-[8px] sm:text-[9px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest mb-1">Total Klaim</p>
            <p class="text-base sm:text-lg font-black text-slate-800 dark:text-white">{{ $reimbursements->count() }} <span class="text-[10px] text-slate-400 dark:text-slate-500 font-bold">Item</span></p>
        </div>
        <div class="bg-white dark:bg-slate-900 p-3.5 sm:p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm dark:shadow-none flex flex-col justify-center text-center sm:text-left transition-colors">
            <p class="text-[8px] sm:text-[9px] font-black text-amber-500 dark:text-amber-400 uppercase tracking-widest mb-1">Status Pending</p>
            <p class="text-base sm:text-lg font-black text-amber-600 dark:text-amber-400">{{ $reimbursements->where('status', 'pending')->count() }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 p-3.5 sm:p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm dark:shadow-none flex flex-col justify-center text-center sm:text-left transition-colors">
            <p class="text-[8px] sm:text-[9px] font-black text-emerald-500 dark:text-emerald-400 uppercase tracking-widest mb-1">Total Cair</p>
            <p class="text-xs sm:text-sm font-black text-emerald-600 dark:text-emerald-400 truncate">
                Rp {{ number_format($reimbursements->where('status', 'approved')->sum('amount'), 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- 3. DATA LISTING (Responsive: Cards for Mobile, Table for Desktop) --}}
    @forelse($reimbursements as $item)
        @if($loop->first)
            {{-- TAMPILAN DESKTOP (Tabel Modern) --}}
            <div class="hidden md:block bg-white dark:bg-slate-900 rounded-[2rem] shadow-sm dark:shadow-none border border-slate-100 dark:border-slate-800 overflow-hidden transition-colors">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50/70 dark:bg-slate-950/80 border-b border-slate-100 dark:border-slate-800 text-slate-400 dark:text-slate-400">
                            <tr>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest">Detail Item</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest">Kategori</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest">Nominal</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-center">Status</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-800/80 text-sm">
        @endif

        {{-- BARIS TABEL DESKTOP --}}
        <tr class="hidden md:table-row hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition duration-150 group">
            <td class="px-6 py-5">
                <div class="flex flex-col">
                    <span class="font-black text-slate-800 dark:text-white text-sm group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">{{ $item->item_name }}</span>
                    <span class="text-[10px] text-slate-400 dark:text-slate-400 mt-1 font-bold">
                        <i class="far fa-calendar-alt mr-1 text-indigo-400"></i> {{ $item->date->format('d M Y') }}
                    </span>
                </div>
            </td>
            <td class="px-6 py-5">
                <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl text-[10px] font-black border border-slate-200 dark:border-slate-700 uppercase tracking-wider inline-block">
                    {{ $item->category }}
                </span>
            </td>
            <td class="px-6 py-5">
                <span class="text-sm font-black text-slate-800 dark:text-white tracking-tight">{{ $item->amount_rupiah }}</span>
            </td>
            <td class="px-6 py-5 text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-black border uppercase tracking-wider {{ $item->status_badge }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current opacity-75"></span>
                    {{ $item->status_label }}
                </span>
            </td>
            <td class="px-6 py-5 text-right">
                <div class="flex justify-end items-center gap-2">
                    <a href="{{ route('employee.reimbursement.show', $item->id) }}" 
                       class="w-9 h-9 bg-slate-50 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-500/20 text-slate-400 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-xl transition flex items-center justify-center shadow-sm dark:shadow-none border border-transparent dark:border-slate-700/70" title="Lihat Detail">
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                    @if($item->status == 'pending')
                    <form action="{{ route('employee.reimbursement.destroy', $item->id) }}" method="POST" class="inline delete-form">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-9 h-9 bg-slate-50 dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-500/20 text-slate-400 dark:text-slate-400 hover:text-rose-500 dark:hover:text-rose-400 rounded-xl transition flex items-center justify-center shadow-sm dark:shadow-none border border-transparent dark:border-slate-700/70 cursor-pointer" title="Batalkan Pengajuan" onclick="return confirm('Batalkan pengajuan klaim ini?')">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </td>
        </tr>

        @if($loop->last)
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- TAMPILAN MOBILE (Card List Tersusun Rapi Ke Bawah) --}}
        <div class="md:hidden bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm dark:shadow-none space-y-3 transition-colors">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-100 dark:border-indigo-500/30 shadow-inner dark:shadow-none">
                        <i class="fas fa-receipt text-sm"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-tight">{{ $item->item_name }}</h4>
                        <p class="text-[10px] text-slate-400 dark:text-slate-400 font-bold mt-0.5 flex items-center gap-1">
                            <i class="far fa-calendar-alt text-slate-300 dark:text-slate-500"></i> {{ $item->date->format('d M Y') }}
                        </p>
                    </div>
                </div>
                <div>
                    <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-md text-[9px] font-black uppercase tracking-wider border border-slate-200 dark:border-slate-700">
                        {{ $item->category }}
                    </span>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2.5 border-t border-slate-50 dark:border-slate-800">
                <div>
                    <span class="text-[9px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest block">Nominal</span>
                    <span class="text-xs font-black text-slate-900 dark:text-white">{{ $item->amount_rupiah }}</span>
                </div>
                <div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider border {{ $item->status_badge }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-75"></span>
                        {{ $item->status_label }}
                    </span>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-50 dark:border-slate-800">
                <a href="{{ route('employee.reimbursement.show', $item->id) }}" 
                   class="flex-1 py-2 bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 rounded-xl text-center text-[10px] font-black uppercase tracking-wider hover:bg-indigo-100 dark:hover:bg-indigo-500/25 border border-transparent dark:border-indigo-500/30 transition shadow-sm dark:shadow-none">
                    <i class="fas fa-eye mr-1"></i> Detail
                </a>
                @if($item->status == 'pending')
                <form action="{{ route('employee.reimbursement.destroy', $item->id) }}" method="POST" class="inline delete-form">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-3 py-2 bg-rose-50 dark:bg-rose-500/15 text-rose-500 dark:text-rose-400 rounded-xl text-xs font-black uppercase tracking-wider hover:bg-rose-100 dark:hover:bg-rose-500/25 border border-transparent dark:border-rose-500/30 transition shadow-sm dark:shadow-none cursor-pointer" onclick="return confirm('Batalkan pengajuan klaim ini?')">
                        <i class="fas fa-trash-alt text-[10px]"></i>
                    </button>
                </form>
                @endif
            </div>
        </div>

    @empty
        {{-- TAMPILAN KOSONG (EMPTY STATE) --}}
        <div class="bg-white dark:bg-slate-900 rounded-[2rem] p-10 border border-slate-100 dark:border-slate-800 text-center shadow-sm dark:shadow-none transition-colors">
            <div class="w-16 h-16 bg-indigo-50/50 dark:bg-indigo-500/15 rounded-full flex items-center justify-center mx-auto mb-3 border-2 border-dashed border-indigo-100 dark:border-indigo-500/35">
                <i class="fas fa-receipt text-xl text-indigo-300 dark:text-indigo-400"></i>
            </div>
            <h3 class="text-slate-800 dark:text-white font-black text-sm uppercase tracking-tight mb-1">Belum Ada Catatan</h3>
            <p class="text-slate-400 dark:text-slate-400 text-xs max-w-xs mx-auto leading-relaxed">
                Semua pengajuan reimbursement Anda akan tercatat secara rapi di halaman ini.
            </p>
        </div>
    @endforelse
</div>

{{-- [BARU] Sinkronisasi Ikon & Fungsi Toggle Dark/Light Mode --}}
<script>
    function syncReimburseListIcons() {
        const isDark = document.documentElement.classList.contains('dark');
        document.querySelectorAll('.theme-icon-sun').forEach(el => {
            el.classList.toggle('theme-hidden', !isDark);
            el.style.display = isDark ? 'inline-block' : 'none';
        });
        document.querySelectorAll('.theme-icon-moon').forEach(el => {
            el.classList.toggle('theme-hidden', isDark);
            el.style.display = isDark ? 'none' : 'inline-block';
        });
    }

    function toggleReimburseListTheme(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        if (typeof window.toggleEmployeeDarkMode === 'function') {
            window.toggleEmployeeDarkMode(event);
            syncReimburseListIcons();
            return;
        }
        const html = document.documentElement;
        const willBeDark = !html.classList.contains('dark');
        html.classList.toggle('dark', willBeDark);
        html.setAttribute('data-theme', willBeDark ? 'dark' : 'light');
        localStorage.setItem('employee_theme', willBeDark ? 'dark' : 'light');
        localStorage.setItem('theme', willBeDark ? 'dark' : 'light');
        syncReimburseListIcons();
    }

    document.addEventListener('DOMContentLoaded', syncReimburseListIcons);
    window.addEventListener('pageshow', syncReimburseListIcons);
</script>
@endsection