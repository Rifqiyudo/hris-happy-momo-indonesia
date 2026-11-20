@extends('admin.layouts.master')
@section('title', 'Master Tunjangan')

@section('content')
{{-- Load AlpineJS --}}
<script src="//unpkg.com/alpinejs" defer></script>

{{-- ========================================================================= --}}
{{-- PENGATURAN TAMBAHAN UNTUK SELECT2 (Fitur Search pada Dropdown Karyawan)   --}}
{{-- ========================================================================= --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Kustomisasi Select2 agar menyatu dengan desain Tailwind Modern */
    .select2-container--default .select2-selection--single {
        height: 3rem !important; /* Disesuaikan dengan py-3 tailwind */
        border-radius: 0.75rem !important; /* rounded-xl */
        border: 1px solid #e2e8f0 !important;
        display: flex !important;
        align-items: center !important;
        padding-left: 0.5rem !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        font-weight: 700 !important;
        font-size: 0.75rem !important; /* text-xs */
        color: #334155 !important;
        background-color: #ffffff !important;
        transition: all 0.3s ease;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 3rem !important;
        right: 1rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #334155 !important;
        line-height: normal !important;
    }
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default.select2-container--open .select2-selection--single {
        outline: none !important;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1) !important;
    }
    .select2-dropdown {
        border-radius: 1rem !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        overflow: hidden !important;
        margin-top: 0.5rem;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border-radius: 0.5rem !important;
        border: 1px solid #e2e8f0 !important;
        padding: 0.75rem !important;
        outline: none !important;
        font-size: 0.75rem !important;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1) !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #6366f1 !important;
        color: white !important;
    }
    .select2-results__option {
        padding: 0.75rem 1rem !important;
        font-size: 0.75rem !important;
        font-weight: 700 !important;
    }
    .select2-results__group {
        background-color: #f8fafc;
        color: #64748b;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 900;
        padding: 0.5rem 1rem;
    }

    /* [BARU] Dukungan Dark Mode Penuh untuk Select2, Scrollbar & SweetAlert2 */
    html.dark .select2-container--default .select2-selection--single,
    body.dark .select2-container--default .select2-selection--single,
    .dark .select2-container--default .select2-selection--single {
        background-color: #020617 !important;
        border-color: #1e293b !important;
        color: #f1f5f9 !important;
        box-shadow: none !important;
    }
    html.dark .select2-container--default.select2-container--open .select2-selection--single,
    body.dark .select2-container--default.select2-container--open .select2-selection--single,
    .dark .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important;
    }
    html.dark .select2-container--default .select2-selection--single .select2-selection__rendered,
    body.dark .select2-container--default .select2-selection--single .select2-selection__rendered,
    .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #f1f5f9 !important;
    }
    html.dark .select2-container--default .select2-selection--single .select2-selection__placeholder,
    body.dark .select2-container--default .select2-selection--single .select2-selection__placeholder,
    .dark .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #64748b !important;
    }
    html.dark .select2-container--default .select2-selection--single .select2-selection__clear,
    body.dark .select2-container--default .select2-selection--single .select2-selection__clear,
    .dark .select2-container--default .select2-selection--single .select2-selection__clear {
        color: #94a3b8 !important;
        margin-right: 6px;
    }
    html.dark .select2-dropdown,
    body.dark .select2-dropdown,
    .dark .select2-dropdown {
        background-color: #0f172a !important;
        border-color: #1e293b !important;
        box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.85) !important;
    }
    html.dark .select2-search--dropdown,
    body.dark .select2-search--dropdown,
    .dark .select2-search--dropdown {
        background-color: #0f172a !important;
        padding: 8px !important;
    }
    html.dark .select2-container--default .select2-search--dropdown .select2-search__field,
    body.dark .select2-container--default .select2-search--dropdown .select2-search__field,
    .dark .select2-container--default .select2-search--dropdown .select2-search__field {
        background-color: #020617 !important;
        border-color: #334155 !important;
        color: #f8fafc !important;
    }
    html.dark .select2-results__group,
    body.dark .select2-results__group,
    .dark .select2-results__group {
        background-color: #020617 !important;
        color: #94a3b8 !important;
        border-top: 1px solid #1e293b;
        border-bottom: 1px solid #1e293b;
    }
    html.dark .select2-results__option,
    body.dark .select2-results__option,
    .dark .select2-results__option {
        color: #cbd5e1 !important;
    }
    html.dark .select2-results__option[aria-selected=true],
    body.dark .select2-results__option[aria-selected=true],
    .dark .select2-results__option[aria-selected=true] {
        background-color: #1e293b !important;
        color: #ffffff !important;
    }
    html.dark .select2-container--default .select2-results__option--highlighted[aria-selected],
    body.dark .select2-container--default .select2-results__option--highlighted[aria-selected],
    .dark .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #4f46e5 !important;
        color: #ffffff !important;
    }

    .allowance-table-scroll::-webkit-scrollbar { height: 8px; }
    .allowance-table-scroll::-webkit-scrollbar-track { background: transparent; }
    .allowance-table-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
    html.dark .allowance-table-scroll::-webkit-scrollbar-thumb,
    body.dark .allowance-table-scroll::-webkit-scrollbar-thumb,
    .dark .allowance-table-scroll::-webkit-scrollbar-thumb {
        background: #334155;
    }

    html.dark .swal2-popup,
    body.dark .swal2-popup,
    .dark .swal2-popup {
        background: #0f172a !important;
        color: #f1f5f9 !important;
        border: 1px solid #1e293b !important;
        border-radius: 1.5rem !important;
    }
    html.dark .swal2-title,
    body.dark .swal2-title,
    .dark .swal2-title {
        color: #ffffff !important;
    }
    html.dark .swal2-html-container,
    body.dark .swal2-html-container,
    .dark .swal2-html-container {
        color: #94a3b8 !important;
    }
</style>

<div x-data="{ activeTab: 'fixed', searchQuery: '' }" class="space-y-6 animate-fadeIn pb-20">

    {{-- [BARU] BREADCRUMB NAVIGASI --}}
    <nav class="flex px-1" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
            <li><a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Home</a></li>
            <li><i class="fas fa-chevron-right text-[8px] opacity-50"></i></li>
            <li><span>Master Data</span></li>
            <li><i class="fas fa-chevron-right text-[8px] opacity-50"></i></li>
            <li class="text-slate-600 dark:text-slate-300">Master Tunjangan</li>
        </ol>
    </nav>

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex items-center gap-3.5">
            {{-- [BARU] Ikon Header Modern --}}
            <div class="w-11 h-11 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-md shadow-indigo-200 dark:shadow-indigo-950/60 shrink-0">
                <i class="fas fa-coins text-base"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-slate-800 dark:text-white tracking-tight">Komponen Tunjangan</h1>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Atur jenis pendapatan tambahan di luar gaji pokok.</p>
            </div>
        </div>

        {{-- FOLDER TABS --}}
        <div class="bg-white dark:bg-slate-900 p-1.5 rounded-2xl border border-gray-200 dark:border-slate-800 shadow-sm dark:shadow-none flex items-center gap-1">
            <button @click="activeTab = 'fixed'" 
                :class="{ 'bg-indigo-50 dark:bg-indigo-600 text-indigo-700 dark:text-white font-bold shadow-sm': activeTab === 'fixed', 'text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200': activeTab !== 'fixed' }"
                class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 cursor-pointer">
                <i class="fas fa-wallet"></i>
                <span>Tunjangan Tetap</span>
                {{-- [BARU] Badge Counter Tab Fixed --}}
                <span :class="activeTab === 'fixed' ? 'bg-indigo-600/15 dark:bg-white/20 text-indigo-700 dark:text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'"
                    class="px-2 py-0.5 rounded-md text-[10px] font-black">
                    {{ $allowances->where('type', 'fixed')->count() }}
                </span>
            </button>
            <button @click="activeTab = 'daily'" 
                :class="{ 'bg-indigo-50 dark:bg-indigo-600 text-indigo-700 dark:text-white font-bold shadow-sm': activeTab === 'daily', 'text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200': activeTab !== 'daily' }"
                class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 cursor-pointer">
                <i class="fas fa-hand-holding-usd"></i>
                <span>Harian / Variabel</span>
                {{-- [BARU] Badge Counter Tab Daily --}}
                <span :class="activeTab === 'daily' ? 'bg-indigo-600/15 dark:bg-white/20 text-indigo-700 dark:text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'"
                    class="px-2 py-0.5 rounded-md text-[10px] font-black">
                    {{ $allowances->where('type', 'daily')->count() }}
                </span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- KOLOM KIRI: TABEL DATA --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-slate-900 rounded-[2rem] shadow-sm dark:shadow-none border border-gray-100 dark:border-slate-800 overflow-hidden min-h-[500px] flex flex-col">
                
                {{-- INFO HEADER & LIVE SEARCH --}}
                <div class="p-5 border-b border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-950/70 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div class="flex items-center gap-3">
                        <h3 class="font-black text-slate-700 dark:text-white text-sm uppercase tracking-wide">Daftar Komponen</h3>
                        <div x-show="activeTab === 'fixed'" class="text-[10px] bg-indigo-100 dark:bg-indigo-500/15 text-indigo-700 dark:text-indigo-400 px-3 py-1 rounded-lg font-black uppercase tracking-widest border border-indigo-200 dark:border-indigo-500/30">Fixed Allowance</div>
                        <div x-show="activeTab === 'daily'" class="text-[10px] bg-amber-100 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400 px-3 py-1 rounded-lg font-black uppercase tracking-widest border border-amber-200 dark:border-amber-500/30" style="display: none;">Daily Allowance</div>
                    </div>

                    {{-- [BARU] Live Search Komponen Tunjangan --}}
                    <div class="relative w-full sm:w-60">
                        <i class="fas fa-search absolute left-3.5 top-2.5 text-slate-400 dark:text-slate-500 text-xs"></i>
                        <input type="text"
                            x-model="searchQuery"
                            placeholder="Cari tunjangan / target..."
                            class="w-full pl-9 pr-8 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-indigo-400 dark:hover:border-indigo-500/60 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                        <button type="button" x-show="searchQuery.length > 0" @click="searchQuery = ''" style="display: none;"
                            class="absolute right-2.5 top-2 text-slate-400 hover:text-rose-500 text-xs cursor-pointer" title="Reset pencarian">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto flex-1 allowance-table-scroll">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                        <thead class="bg-white dark:bg-slate-950/60 text-slate-400 dark:text-slate-400 font-black uppercase text-[10px] tracking-[0.2em] border-b border-gray-100 dark:border-slate-800">
                            <tr>
                                <th class="px-6 py-5">Nama Tunjangan & Target</th>
                                <th class="px-6 py-5">Nominal</th>
                                <th class="px-6 py-5 text-center">Status Pajak</th>
                                <th class="px-6 py-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-slate-800/80">
                            @forelse($allowances as $item)
                            @php
                                $targetText = !empty($item->user_id)
                                    ? ('staff ' . (optional($item->user)->name ?? ''))
                                    : (!empty($item->position_id)
                                        ? ('jabatan ' . (optional($item->position)->name ?? ''))
                                        : 'semua karyawan global');
                            @endphp
                            <tr data-search="{{ strtolower($item->name . ' ' . $targetText) }}"
                                x-show="activeTab === '{{ $item->type }}' && (!searchQuery || $el.dataset.search.includes(searchQuery.toLowerCase()))"
                                class="hover:bg-slate-50/80 dark:hover:bg-slate-800/45 transition group">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-sm dark:shadow-none shrink-0
                                            {{ $item->type == 'fixed' ? 'bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/30' : 'bg-amber-50 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-500/30' }}">
                                            <i class="fas {{ $item->type == 'fixed' ? 'fa-wallet' : 'fa-stopwatch' }}"></i>
                                        </div>
                                        <div>
                                            <div class="font-black text-slate-800 dark:text-white tracking-tight group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">{{ $item->name }}</div>
                                            <div class="text-[9px] font-bold text-slate-400 dark:text-slate-400 mt-1 uppercase tracking-widest">
                                                {{-- LOGIKA DISPLAY SUPER TARGET (DIPERBARUI) --}}
                                                @if(!empty($item->user_id))
                                                    Target: <span class="text-indigo-500 dark:text-indigo-400"><i class="fas fa-user mr-1"></i> STAFF: {{ optional($item->user)->name ?? 'Dihapus' }}</span>
                                                @elseif(!empty($item->position_id))
                                                    Target: <span class="text-indigo-500 dark:text-indigo-400"><i class="fas fa-briefcase mr-1"></i> JABATAN: {{ optional($item->position)->name ?? 'Dihapus' }}</span>
                                                @else
                                                    Target: <span class="text-emerald-500 dark:text-emerald-400"><i class="fas fa-globe mr-1"></i> SEMUA KARYAWAN</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 font-mono font-bold text-slate-700 dark:text-slate-200 whitespace-nowrap">
                                    Rp {{ number_format($item->amount, 0, ',', '.') }}
                                    <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest block mt-0.5 font-sans">
                                        / {{ $item->type == 'fixed' ? 'Bulan' : 'Kehadiran' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-center whitespace-nowrap">
                                    @if($item->is_taxable)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/30 shadow-sm dark:shadow-none">
                                            <i class="fas fa-check-circle"></i> Kena Pajak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-300 border border-slate-200 dark:border-slate-700 shadow-sm dark:shadow-none">
                                            <i class="fas fa-times-circle"></i> Non-Pajak
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-center whitespace-nowrap">
                                    <div class="flex justify-center gap-2">
                                        {{-- Tombol Edit --}}
                                        <button type="button" onclick='editAllowance(@json($item))' 
                                            class="w-8 h-8 rounded-lg bg-white dark:bg-slate-800/90 border border-gray-200 dark:border-slate-700 text-amber-500 dark:text-amber-400 hover:bg-amber-500 dark:hover:bg-amber-500 hover:text-white dark:hover:text-white hover:border-amber-500 transition flex items-center justify-center shadow-sm dark:shadow-none cursor-pointer"
                                            title="Edit Tunjangan">
                                            <i class="fas fa-pencil-alt text-[10px]"></i>
                                        </button>
                                        {{-- Tombol Hapus --}}
                                        <button type="button" onclick="confirmDelete('{{ $item->id }}', '{{ $item->name }}')" 
                                            class="w-8 h-8 rounded-lg bg-white dark:bg-slate-800/90 border border-gray-200 dark:border-slate-700 text-rose-500 dark:text-rose-400 hover:bg-rose-500 dark:hover:bg-rose-600 hover:text-white dark:hover:text-white hover:border-rose-500 transition flex items-center justify-center shadow-sm dark:shadow-none cursor-pointer"
                                            title="Hapus Tunjangan">
                                            <i class="fas fa-trash-alt text-[10px]"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $item->id }}" action="{{ route('admin.master.allowance.destroy', $item->id) }}" method="POST" class="hidden">
                                        @csrf @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                    
                    {{-- Empty State --}}
                    <div x-show="activeTab === 'fixed' && {{ $allowances->where('type', 'fixed')->count() }} === 0" class="p-16 text-center text-slate-400 dark:text-slate-500" style="display: none;">
                        <i class="fas fa-inbox text-5xl mb-4 opacity-20"></i>
                        <p class="text-[10px] font-black uppercase tracking-widest">Belum ada tunjangan tetap.</p>
                    </div>
                    <div x-show="activeTab === 'daily' && {{ $allowances->where('type', 'daily')->count() }} === 0" class="p-16 text-center text-slate-400 dark:text-slate-500" style="display: none;">
                        <i class="fas fa-inbox text-5xl mb-4 opacity-20"></i>
                        <p class="text-[10px] font-black uppercase tracking-widest">Belum ada tunjangan harian.</p>
                    </div>

                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: FORM STICKY --}}
        <div class="lg:col-span-1">
            <div id="allowanceFormCard" class="bg-white dark:bg-slate-900 rounded-[2rem] shadow-xl shadow-slate-200/40 dark:shadow-none border border-slate-100 dark:border-slate-800 sticky top-6 transition-all duration-300">
                <div class="bg-slate-50 dark:bg-slate-950/90 px-6 py-5 border-b border-slate-100 dark:border-slate-800 rounded-t-[2rem] flex items-center justify-between gap-2">
                    <h3 class="font-black text-slate-800 dark:text-white flex items-center gap-3 uppercase tracking-tight text-xs" id="formTitle">
                        <i class="fas fa-plus-circle text-indigo-500 dark:text-indigo-400 text-lg"></i> Tambah Tunjangan
                    </h3>
                    {{-- [BARU] Badge Indikator Mode Form --}}
                    <span id="allowanceModeBadge" class="px-2.5 py-1 rounded-lg bg-indigo-500/15 border border-indigo-500/30 text-[9px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400">
                        Baru
                    </span>
                </div>
                
                <div class="p-6">
                    <form id="allowanceForm" action="{{ route('admin.master.allowance.store') }}" method="POST">
                        @csrf
                        
                        {{-- DIV INJECT METHOD PUT --}}
                        <div id="methodField"></div> 

                        <div class="space-y-5">
                            
                            {{-- Nama --}}
                            <div>
                                <label class="block text-[10px] font-black text-slate-800 dark:text-slate-300 uppercase tracking-widest mb-1.5">Nama Komponen <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" id="nameInput" required 
                                    class="w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-indigo-400 dark:hover:border-indigo-500/60 rounded-xl px-4 py-3 text-xs font-bold text-slate-700 dark:text-slate-100 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition shadow-sm dark:shadow-none placeholder-slate-300 dark:placeholder-slate-500" 
                                    placeholder="Contoh: Tunjangan Transport">
                            </div>

                            {{-- MULTI TARGET DROPDOWN (DIPERBARUI DENGAN SELECT2 ID) --}}
                            <div>
                                <label class="block text-[10px] font-black text-slate-800 dark:text-slate-300 uppercase tracking-widest mb-1.5">Berlaku Untuk <span class="text-rose-500">*</span></label>
                                <select name="target_id" id="targetInput" class="w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-xs font-bold text-slate-700 dark:text-slate-100 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition shadow-sm dark:shadow-none cursor-pointer" style="width: 100%;">
                                    <option value="">-- SEMUA KARYAWAN (GLOBAL) --</option>
                                    
                                    {{-- FITUR BARU: Opsi ALL STAFF --}}
                                    <option value="ALL_STAFF" style="color: #6366f1; font-weight: 900;">
                                        🌟 JABATAN: ALL STAFF (Kecuali Koordinator, SPV, Manager, Direktur)
                                    </option>
                                    
                                    <optgroup label="Target Berdasarkan Jabatan">
                                        @foreach(\App\Models\Position::orderBy('name')->get() as $pos)
                                            <option value="pos_{{ $pos->id }}">JABATAN: {{ strtoupper($pos->name) }}</option>
                                        @endforeach
                                    </optgroup>

                                    <optgroup label="Target Spesifik Staff/Karyawan">
                                        {{-- Menarik data user aktif langsung agar tidak terjadi undefined error --}}
                                        @foreach(\App\Models\User::where('is_active', true)->orderBy('name')->get() as $usr)
                                            <option value="usr_{{ $usr->id }}">STAFF: {{ strtoupper($usr->name) }} ({{ $usr->nik }})</option>
                                        @endforeach
                                    </optgroup>

                                </select>
                                <p class="text-[9px] font-bold text-slate-400 dark:text-slate-500 mt-1.5 uppercase tracking-tighter">Pilih Jabatan atau Karyawan spesifik.</p>
                            </div>

                            {{-- Tipe --}}
                            <div>
                                <label class="block text-[10px] font-black text-slate-800 dark:text-slate-300 uppercase tracking-widest mb-1.5">Tipe Hitungan <span class="text-rose-500">*</span></label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="type" id="typeFixed" value="fixed" class="peer sr-only" checked>
                                        <div class="rounded-xl border border-slate-200 dark:border-slate-800 p-3 text-center transition-all bg-white dark:bg-slate-950 text-slate-700 dark:text-slate-300 group-hover:border-indigo-300 dark:group-hover:border-indigo-500/60 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-500/15 peer-checked:text-indigo-700 dark:peer-checked:text-indigo-300 shadow-sm dark:shadow-none">
                                            <div class="text-[11px] font-black uppercase tracking-tight">Tetap (Fixed)</div>
                                            <div class="text-[8px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-0.5 peer-checked:text-indigo-400">Bulanan</div>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="type" id="typeDaily" value="daily" class="peer sr-only">
                                        <div class="rounded-xl border border-slate-200 dark:border-slate-800 p-3 text-center transition-all bg-white dark:bg-slate-950 text-slate-700 dark:text-slate-300 group-hover:border-amber-300 dark:group-hover:border-amber-500/60 peer-checked:border-amber-500 peer-checked:bg-amber-50 dark:peer-checked:bg-amber-500/15 peer-checked:text-amber-700 dark:peer-checked:text-amber-300 shadow-sm dark:shadow-none">
                                            <div class="text-[11px] font-black uppercase tracking-tight">Harian (Daily)</div>
                                            <div class="text-[8px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-0.5 peer-checked:text-amber-400">Per Kehadiran</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Nominal --}}
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block text-[10px] font-black text-slate-800 dark:text-slate-300 uppercase tracking-widest">Nominal Default (Rp) <span class="text-rose-500">*</span></label>
                                    {{-- [BARU] Pratinjau Nominal Format Rupiah --}}
                                    <span id="amountPreview" class="text-[10px] font-mono font-black text-indigo-600 dark:text-indigo-400">Rp 0</span>
                                </div>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-black text-slate-400 dark:text-slate-500">Rp</span>
                                    <input type="number" name="amount" id="amountInput" required 
                                        class="w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-indigo-400 dark:hover:border-indigo-500/60 rounded-xl pl-10 pr-4 py-3 text-sm font-black text-slate-800 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition shadow-sm dark:shadow-none" 
                                        placeholder="0">
                                </div>
                            </div>

                            {{-- PPh 21 Checkbox --}}
                            <label class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800 rounded-xl cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800/70 transition mt-2">
                                <input id="taxInput" name="is_taxable" type="checkbox" value="1" checked class="mt-0.5 w-4 h-4 rounded border-gray-300 dark:border-slate-700 dark:bg-slate-900 text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <p class="text-[11px] font-black text-slate-800 dark:text-slate-200 uppercase tracking-tight">Komponen Kena Pajak</p>
                                    <p class="text-[9px] font-bold text-slate-400 dark:text-slate-400 mt-0.5 uppercase tracking-widest leading-relaxed">Centang jika ini masuk hitungan PPh 21.</p>
                                </div>
                            </label>

                            {{-- Buttons --}}
                            <div class="flex items-center gap-3 pt-6 border-t border-slate-100 dark:border-slate-800">
                                <button type="button" id="btnCancel" onclick="resetForm()" class="hidden flex-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-300 py-3.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-slate-700 transition shadow-sm dark:shadow-none cursor-pointer">Batal</button>
                                <button type="submit" id="btnSubmit" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-indigo-700 dark:hover:bg-indigo-500 shadow-xl shadow-indigo-100 dark:shadow-indigo-950/60 transition active:scale-95 cursor-pointer">Simpan Data</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- SCRIPT --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // [BARU] Helper Format Pratinjau Rupiah
    function updateAmountPreview() {
        const input = document.getElementById('amountInput');
        const preview = document.getElementById('amountPreview');
        if (!input || !preview) return;
        const val = parseFloat(input.value) || 0;
        preview.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
    }

    // INISIALISASI SELECT2 SAAT HALAMAN DIMUAT
    $(document).ready(function() {
        $('#targetInput').select2({
            placeholder: "-- SEMUA KARYAWAN (GLOBAL) --",
            allowClear: true,
            language: {
                noResults: function() {
                    return "Karyawan atau Jabatan tidak ditemukan";
                }
            }
        });

        const amountInput = document.getElementById('amountInput');
        if (amountInput) {
            amountInput.addEventListener('input', updateAmountPreview);
        }
    });

    function editAllowance(data) {
        // 1. Ganti Judul
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit text-amber-500 text-lg"></i> Update Tunjangan';
        
        // [BARU] Perbarui Badge & Highlight Kartu Form
        const badge = document.getElementById('allowanceModeBadge');
        if (badge) {
            badge.innerText = 'Edit';
            badge.className = 'px-2.5 py-1 rounded-lg bg-amber-500/15 border border-amber-500/30 text-[9px] font-black uppercase tracking-widest text-amber-500 dark:text-amber-400';
        }
        const formCard = document.getElementById('allowanceFormCard');
        if (formCard) {
            formCard.classList.add('ring-2', 'ring-amber-500/40');
        }

        // 2. Isi Inputan Dasar
        document.getElementById('nameInput').value = data.name;
        document.getElementById('amountInput').value = data.amount;
        document.getElementById('taxInput').checked = data.is_taxable;
        updateAmountPreview();

        // 3. LOGIKA SET TARGET DROPDOWN TERINTEGRASI SELECT2 (DIPERBARUI)
        let targetVal = '';
        if (data.user_id) {
            targetVal = 'usr_' + data.user_id;
        } else if (data.position_id) {
            targetVal = 'pos_' + data.position_id;
        }
        
        // Update nilai pada element select dan trigger event change agar UI Select2 memperbarui diri
        $('#targetInput').val(targetVal).trigger('change');

        // 4. Set Radio Button Tipe
        if (data.type === 'fixed') {
            document.getElementById('typeFixed').checked = true;
        } else {
            document.getElementById('typeDaily').checked = true;
        }

        // 5. Ubah Action Form URL
        let baseUrl = "{{ route('admin.master.allowance.index') }}"; 
        document.getElementById('allowanceForm').action = baseUrl + '/' + data.id;

        // 6. INJECT METHOD PUT
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        // 7. Ubah Tampilan Tombol
        let btnSubmit = document.getElementById('btnSubmit');
        btnSubmit.classList.replace('bg-indigo-600', 'bg-amber-500');
        btnSubmit.classList.replace('hover:bg-indigo-700', 'hover:bg-amber-600');
        btnSubmit.classList.replace('dark:hover:bg-indigo-500', 'dark:hover:bg-amber-500');
        btnSubmit.classList.replace('shadow-indigo-100', 'shadow-amber-100');
        btnSubmit.classList.replace('dark:shadow-indigo-950/60', 'dark:shadow-amber-950/60');
        btnSubmit.innerText = 'Update Data';
        
        document.getElementById('btnCancel').classList.remove('hidden');

        // 8. Scroll ke Atas
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function resetForm() {
        // 1. Reset UI
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus-circle text-indigo-500 dark:text-indigo-400 text-lg"></i> Tambah Tunjangan';
        document.getElementById('allowanceForm').reset();
        document.getElementById('allowanceForm').action = "{{ route('admin.master.allowance.store') }}";
        updateAmountPreview();

        // [BARU] Kembalikan Badge & Ring Kartu Form
        const badge = document.getElementById('allowanceModeBadge');
        if (badge) {
            badge.innerText = 'Baru';
            badge.className = 'px-2.5 py-1 rounded-lg bg-indigo-500/15 border border-indigo-500/30 text-[9px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400';
        }
        const formCard = document.getElementById('allowanceFormCard');
        if (formCard) {
            formCard.classList.remove('ring-2', 'ring-amber-500/40');
        }
        
        // Mengembalikan pilihan Select2 ke default (Kosong/Global)
        $('#targetInput').val('').trigger('change');

        // 2. HAPUS METHOD PUT (Kembali ke POST untuk Create)
        document.getElementById('methodField').innerHTML = '';

        // 3. Reset Tombol
        let btnSubmit = document.getElementById('btnSubmit');
        btnSubmit.classList.replace('bg-amber-500', 'bg-indigo-600');
        btnSubmit.classList.replace('hover:bg-amber-600', 'hover:bg-indigo-700');
        btnSubmit.classList.replace('dark:hover:bg-amber-500', 'dark:hover:bg-indigo-500');
        btnSubmit.classList.replace('shadow-amber-100', 'shadow-indigo-100');
        btnSubmit.classList.replace('dark:shadow-amber-950/60', 'dark:shadow-indigo-950/60');
        btnSubmit.innerText = 'Simpan Data';
        
        document.getElementById('btnCancel').classList.add('hidden');
    }

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Tunjangan?',
            text: "Komponen " + name + " akan dihapus secara permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            borderRadius: '1rem'
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('delete-form-' + id).submit();
        })
    }
</script>
@endsection