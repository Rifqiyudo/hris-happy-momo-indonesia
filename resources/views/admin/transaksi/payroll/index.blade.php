@extends('admin.layouts.master')
@section('title', 'Data Penggajian - PT. Happy Momo Indonesia')

@section('content')
<div class="max-w-7xl mx-auto pb-20 px-4 animate-fadeIn">
    
    {{-- 1. BREADCRUMB --}}
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
            <li><a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Home</a></li>
            <li><i class="fas fa-chevron-right text-[8px] opacity-50"></i></li>
            <li class="text-slate-600 dark:text-slate-300">Transaksi Payroll</li>
        </ol>
    </nav>

    {{-- 2. HEADER & FILTER SECTION --}}
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-8">
        <div>
            <div class="flex items-center gap-4 mb-2">
                <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-200 dark:shadow-indigo-950/60">
                    <i class="fas fa-file-invoice-dollar text-xl"></i>
                </div>
                <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tighter">Monitoring Payroll</h1>
            </div>
            <p class="text-slate-400 dark:text-slate-400 text-sm font-medium ml-16">Rekapitulasi Gaji Periode: <span class="font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wide">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</span></p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-4 w-full lg:w-auto">
            
            {{-- Form Filter Periode & Pencarian Nama (Auto-Submit) --}}
            <form action="{{ route('admin.transaksi.payroll.index') }}" method="GET" class="relative w-full sm:w-auto flex flex-wrap sm:flex-nowrap items-center gap-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl px-5 py-3.5 shadow-sm dark:shadow-none hover:border-indigo-300 dark:hover:border-indigo-500/60 transition-colors">
                
                {{-- [FITUR BARU] Input Search Nama / NIK Karyawan --}}
                <div class="flex items-center gap-2 border-b sm:border-b-0 sm:border-r border-slate-200 dark:border-slate-800 pb-2 sm:pb-0 sm:pr-4 w-full sm:w-52">
                    <i class="fas fa-search text-slate-400 dark:text-slate-500 text-xs"></i>
                    <input type="text"
                        name="search"
                        id="searchPayrollInput"
                        value="{{ $search ?? request('search') }}"
                        placeholder="Cari nama / NIK..."
                        autocomplete="off"
                        class="bg-transparent text-xs font-bold text-slate-700 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none w-full">

                    @if(!empty($search ?? request('search')))
                        <a href="{{ route('admin.transaksi.payroll.index', ['month' => $month, 'year' => $year]) }}"
                            title="Hapus Pencarian"
                            class="text-slate-400 dark:text-slate-500 hover:text-rose-500 dark:hover:text-rose-400 transition-colors text-xs">
                            <i class="fas fa-times-circle"></i>
                        </a>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    <i class="fas fa-calendar-alt text-slate-400 dark:text-slate-500"></i>
                    
                    <select name="month" onchange="this.form.submit()" class="bg-transparent text-xs font-black text-slate-700 dark:text-slate-100 focus:outline-none cursor-pointer uppercase appearance-none pr-4">
                        @foreach($months as $mNum => $mName)
                            <option value="{{ $mNum }}" class="bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100" {{ $month == $mNum ? 'selected' : '' }}>
                                {{ $mName }}
                            </option>
                        @endforeach
                    </select>

                    <div class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-700"></div>

                    <select name="year" onchange="this.form.submit()" class="bg-transparent text-xs font-black text-slate-700 dark:text-slate-100 focus:outline-none cursor-pointer uppercase appearance-none">
                        @foreach($years as $y)
                            <option value="{{ $y }}" class="bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                    
                    <i class="fas fa-chevron-down text-slate-300 dark:text-slate-500 text-[10px] ml-1"></i>
                </div>

                {{-- [FITUR BARU] Tombol Submit Pencarian --}}
                <button type="submit" title="Cari Karyawan" class="ml-auto sm:ml-1 bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-500/20 text-slate-500 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 border border-transparent dark:border-slate-700 rounded-xl px-2.5 py-1.5 text-xs transition-colors cursor-pointer">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            {{-- Tombol Generate Gaji Baru --}}
            <form action="{{ route('admin.transaksi.payroll.generate-mass') }}" method="POST" onsubmit="return confirm('Generate ulang seluruh payroll periode ini secara otomatis?')" class="w-full sm:w-auto">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">
                <button type="submit" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 dark:hover:bg-indigo-500 text-white px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-xl shadow-indigo-200 dark:shadow-indigo-950/60 flex items-center justify-center gap-3 active:scale-95 whitespace-nowrap cursor-pointer">
                    <i class="fas fa-sync-alt animate-spin-slow"></i> Generate Gaji Baru
                </button>
            </form>
        </div>
    </div>

    {{-- 3. WIDGET SUMMARY (QUICK STATS UNTUK HRD) --}}
    @if(isset($payrolls) && count($payrolls) > 0)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm dark:shadow-none flex items-center gap-5 relative overflow-hidden">
            <div class="absolute -right-6 -top-6 w-20 h-20 bg-emerald-50 dark:bg-emerald-500/10 rounded-full opacity-50"></div>
            <div class="w-14 h-14 rounded-2xl bg-emerald-100 dark:bg-emerald-500/15 border border-transparent dark:border-emerald-500/25 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl shrink-0 z-10">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="z-10">
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest mb-1">Total Gaji Bulan Ini</p>
                <h3 id="statTotalGaji" class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">Rp {{ number_format($totalGaji ?? $payrolls->sum('net_salary') ?? 0, 0, ',', '.') }}</h3>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm dark:shadow-none flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-sky-100 dark:bg-sky-500/15 border border-transparent dark:border-sky-500/25 text-sky-600 dark:text-sky-400 flex items-center justify-center text-2xl shrink-0">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest mb-1">Total Karyawan</p>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight"><span id="statTotalKaryawan">{{ $totalKaryawan ?? $payrolls->total() ?? 0 }}</span> <span class="text-sm font-bold text-slate-400 dark:text-slate-500 lowercase tracking-normal">Orang</span></h3>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm dark:shadow-none flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-amber-100 dark:bg-amber-500/15 border border-transparent dark:border-amber-500/25 text-amber-600 dark:text-amber-400 flex items-center justify-center text-2xl shrink-0">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest mb-1">Status Pending / Draft</p>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight"><span id="statTotalDraft">{{ $totalDraft ?? $payrolls->where('status', 'draft')->count() ?? 0 }}</span> <span class="text-sm font-bold text-slate-400 dark:text-slate-500 lowercase tracking-normal">Slip</span></h3>
            </div>
        </div>
    </div>
    @endif

    {{-- 4. TABEL DATA PENGGAJIAN --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-none overflow-hidden flex flex-col min-h-[450px]">
        @if(isset($payrolls) && count($payrolls) > 0)
            {{-- [BARU] Toolbar Filter Status Cepat (Semua / Published / Draft) --}}
            <div class="px-8 py-4 bg-slate-50/50 dark:bg-slate-950/60 border-b border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">
                    <i class="fas fa-layer-group text-indigo-500"></i>
                    <span>Daftar Slip Gaji</span>
                </div>
                <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-900 p-1 rounded-xl border border-slate-200/70 dark:border-slate-800" id="statusFilterGroup">
                    <button type="button" data-status-filter="all" class="status-filter-btn active px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest bg-white dark:bg-indigo-600 text-slate-800 dark:text-white shadow-sm transition-all cursor-pointer">
                        Semua
                    </button>
                    <button type="button" data-status-filter="published" class="status-filter-btn px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white transition-all cursor-pointer">
                        Published
                    </button>
                    <button type="button" data-status-filter="draft" class="status-filter-btn px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white transition-all cursor-pointer">
                        Draft
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto payroll-table-scroll">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 dark:bg-slate-950/80 border-b border-slate-200 dark:border-slate-800">
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest whitespace-nowrap">Karyawan</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest text-right whitespace-nowrap">Gaji Pokok</th>
                            <th class="px-6 py-5 text-[10px] font-black text-emerald-500 dark:text-emerald-400 uppercase tracking-widest text-right whitespace-nowrap">+ Tunjangan</th>
                            <th class="px-6 py-5 text-[10px] font-black text-rose-500 dark:text-rose-400 uppercase tracking-widest text-right whitespace-nowrap">Potongan</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-800 dark:text-indigo-300 uppercase tracking-widest text-right bg-indigo-50/50 dark:bg-indigo-500/10 whitespace-nowrap">Gaji Bersih</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest text-center whitespace-nowrap">Status</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest text-right whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-sm">
                        @foreach($payrolls as $item)
                        <tr class="payroll-row hover:bg-slate-50/80 dark:hover:bg-slate-800/45 transition-colors duration-200 group"
                            data-search="{{ strtolower(($item->user->name ?? '') . ' ' . ($item->user->nik ?? '') . ' ' . ($item->user->position->name ?? '')) }}"
                            data-status="{{ ($item->status == 'paid' || $item->status == 'published') ? 'published' : 'draft' }}"
                            data-net="{{ $item->net_salary ?? 0 }}"
                            data-draft="{{ $item->status == 'draft' ? 1 : 0 }}">
                            
                            {{-- Profil Karyawan --}}
                            <td class="px-8 py-4 align-middle">
                                <div class="flex items-center gap-4">
                                    <div class="relative shrink-0">
                                        @if(isset($item->user->avatar) && $item->user->avatar)
                                            <img src="{{ asset('storage/' . $item->user->avatar) }}" class="w-11 h-11 rounded-2xl object-cover border-2 border-white dark:border-slate-900 shadow-sm dark:shadow-none group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-11 h-11 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-black text-sm shadow-sm shadow-indigo-100 dark:shadow-none group-hover:scale-105 transition-transform duration-300 border-2 border-white dark:border-slate-900">
                                                {{ substr($item->user->name, 0, 2) }}
                                            </div>
                                        @endif
                                        <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 {{ $item->status == 'paid' || $item->status == 'published' ? 'bg-emerald-500' : 'bg-amber-400' }} border-2 border-white dark:border-slate-900 rounded-full shadow-sm"></div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black uppercase text-slate-800 dark:text-white tracking-tight leading-none mb-1.5">{{ $item->user->name }}</p>
                                        <p class="text-[9px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-widest">
                                            {{ $item->total_days_worked }} Hari Kerja &bull; {{ $item->user->position->name ?? 'Staff' }}
                                            @if(!empty($item->user->nik)) &bull; ID: {{ $item->user->nik }} @endif
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Gaji Pokok (Right Aligned) --}}
                            <td class="px-6 py-4 align-middle text-sm font-bold text-slate-600 dark:text-slate-300 text-right whitespace-nowrap">
                                Rp {{ number_format($item->base_salary, 0, ',', '.') }}
                            </td>

                            {{-- Total Tunjangan (Right Aligned) --}}
                            <td class="px-6 py-4 align-middle text-sm font-black text-emerald-500 dark:text-emerald-400 text-right whitespace-nowrap" title="Tunjangan: Rp {{ number_format($item->total_allowance, 0, ',', '.') }}">
                                + Rp {{ number_format($item->total_allowance, 0, ',', '.') }}
                            </td>

                            {{-- Total Potongan (Right Aligned) --}}
                            <td class="px-6 py-4 align-middle text-sm font-black text-rose-500 dark:text-rose-400 text-right whitespace-nowrap">
                                - Rp {{ number_format($item->total_deduction, 0, ',', '.') }}
                            </td>

                            {{-- Gaji Bersih (THP) (Right Aligned dengan background highlight) --}}
                            <td class="px-6 py-4 align-middle text-right bg-indigo-50/30 dark:bg-indigo-500/[0.06] group-hover:bg-indigo-50/80 dark:group-hover:bg-indigo-500/15 transition-colors whitespace-nowrap">
                                <span class="text-sm font-black text-indigo-700 dark:text-indigo-300 tracking-tight">
                                    Rp {{ number_format($item->net_salary, 0, ',', '.') }}
                                </span>
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-6 py-4 align-middle text-center whitespace-nowrap">
                                @if($item->status == 'paid' || $item->status == 'published')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/30 shadow-sm dark:shadow-none">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Published
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest bg-amber-50 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-500/30 shadow-sm dark:shadow-none">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                        Draft
                                    </span>
                                @endif
                            </td>

                            {{-- Tombol Aksi --}}
                            <td class="px-8 py-4 align-middle text-right whitespace-nowrap">
                                <div class="flex justify-end items-center gap-2">
                                    
                                    {{-- Lihat Detail Slip --}}
                                    <a href="{{ route('admin.transaksi.payroll.show', $item->id) }}" class="w-9 h-9 flex items-center justify-center bg-slate-50 dark:bg-slate-800 text-slate-400 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/15 rounded-xl border border-slate-200 dark:border-slate-700 dark:hover:border-indigo-500/40 transition-all shadow-sm dark:shadow-none" title="Detail Slip">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>

                                    @if($item->status == 'draft')
                                        {{-- Publish Button --}}
                                        <form action="{{ route('admin.transaksi.payroll.publish', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center justify-center h-9 px-4 bg-emerald-500 dark:bg-emerald-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-emerald-600 dark:hover:bg-emerald-500 transition-all shadow-sm shadow-emerald-200 dark:shadow-emerald-950/50 active:scale-95 gap-2 cursor-pointer">
                                                <i class="fas fa-paper-plane"></i> Publish
                                            </button>
                                        </form>

                                        {{-- Delete Button --}}
                                        <form action="{{ route('admin.transaksi.payroll.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data payroll ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-9 h-9 flex items-center justify-center bg-white dark:bg-slate-800 text-rose-400 hover:text-white hover:bg-rose-500 dark:hover:bg-rose-600 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-rose-500 transition-all shadow-sm dark:shadow-none cursor-pointer" title="Hapus">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
                                    @else
                                        {{-- Cetak Slip Button --}}
                                        <a href="{{ route('admin.transaksi.payroll.print-slip', $item->id) }}" target="_blank" class="inline-flex items-center justify-center h-9 px-4 bg-slate-800 dark:bg-slate-800 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-slate-700 dark:hover:bg-indigo-600 transition-all shadow-sm shadow-slate-300 dark:shadow-none gap-2 border border-slate-900 dark:border-slate-700 dark:hover:border-indigo-500">
                                            <i class="fas fa-print"></i> Cetak Slip
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach

                        {{-- [FITUR BARU] Baris Kosong Dinamis Saat Live-Search Tidak Menemukan Kecocokan --}}
                        <tr id="emptyLiveSearchRow" class="hidden">
                            <td colspan="7" class="px-8 py-16 text-center">
                                <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800/70 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-slate-100 dark:border-slate-700">
                                    <i class="fas fa-search text-2xl text-slate-300 dark:text-slate-500"></i>
                                </div>
                                <p class="text-sm font-black text-slate-700 dark:text-slate-200 uppercase tracking-tight">Karyawan Tidak Ditemukan</p>
                                <p class="text-xs text-slate-400 dark:text-slate-400 mt-1">Tekan <span class="font-bold text-indigo-600 dark:text-indigo-400">Enter</span> untuk mencari ke seluruh halaman atau periksa kembali ejaan nama/NIK.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if(method_exists($payrolls, 'links') && $payrolls->hasPages())
                <div class="px-8 py-5 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-100 dark:border-slate-800 mt-auto payroll-pagination-wrapper">
                    {{ $payrolls->appends(request()->query())->links() }}
                </div>
            @endif

        @else
            {{-- 5. EMPTY STATE MODERN --}}
            @if(!empty($search ?? request('search')))
                {{-- [FITUR BARU] Empty State Khusus Jika Hasil Pencarian Nama Kosong --}}
                <div class="flex-1 flex flex-col items-center justify-center p-12 text-center my-auto">
                    <div class="w-24 h-24 bg-slate-50 dark:bg-slate-800/70 rounded-[2rem] flex items-center justify-center mb-6 shadow-inner dark:shadow-none border-2 border-slate-100 dark:border-slate-700 border-dashed">
                        <i class="fas fa-user-slash text-4xl text-slate-300 dark:text-slate-500"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white uppercase tracking-tighter mb-2">Karyawan Tidak Ditemukan</h3>
                    <p class="text-sm font-medium text-slate-400 dark:text-slate-400 mb-8 max-w-md leading-relaxed">
                        Tidak ada data payroll yang cocok dengan pencarian <span class="font-bold text-indigo-500 dark:text-indigo-400">"{{ $search ?? request('search') }}"</span> pada periode <span class="font-bold text-slate-600 dark:text-slate-200 uppercase">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</span>.
                    </p>
                    <a href="{{ route('admin.transaksi.payroll.index', ['month' => $month, 'year' => $year]) }}" class="bg-indigo-600 text-white px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-500 transition-all shadow-xl shadow-indigo-200 dark:shadow-indigo-950/60 flex items-center justify-center gap-3 active:scale-95">
                        <i class="fas fa-undo"></i> Tampilkan Semua Karyawan
                    </a>
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center p-12 text-center my-auto">
                    <div class="w-24 h-24 bg-slate-50 dark:bg-slate-800/70 rounded-[2rem] flex items-center justify-center mb-6 shadow-inner dark:shadow-none border-2 border-slate-100 dark:border-slate-700 border-dashed">
                        <i class="fas fa-receipt text-4xl text-slate-300 dark:text-slate-500"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white uppercase tracking-tighter mb-2">Gaji Belum Digenerate</h3>
                    <p class="text-sm font-medium text-slate-400 dark:text-slate-400 mb-8 max-w-md leading-relaxed">
                        Data penggajian untuk periode <span class="font-bold text-indigo-500 dark:text-indigo-400 uppercase">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</span> belum tersedia di sistem.
                    </p>
                    
                    {{-- Tombol Generate Gaji Baru pada Empty State --}}
                    <form action="{{ route('admin.transaksi.payroll.generate-mass') }}" method="POST">
                        @csrf
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <button type="submit" class="bg-indigo-600 text-white px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-500 transition-all shadow-xl shadow-indigo-200 dark:shadow-indigo-950/60 flex items-center justify-center gap-3 active:scale-95 cursor-pointer">
                            <i class="fas fa-magic"></i> Proses Gaji Sekarang
                        </button>
                    </form>
                </div>
            @endif
        @endif
    </div>
</div>

<style>
    .animate-spin-slow { animation: spin 4s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    
    /* Menyembunyikan panah default select di beberapa browser */
    select {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }

    /* [BARU] Dukungan Dark Mode untuk Scrollbar Tabel & Pagination Laravel */
    .payroll-table-scroll::-webkit-scrollbar { height: 8px; }
    .payroll-table-scroll::-webkit-scrollbar-track { background: transparent; }
    .payroll-table-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
    html.dark .payroll-table-scroll::-webkit-scrollbar-thumb,
    body.dark .payroll-table-scroll::-webkit-scrollbar-thumb,
    .dark .payroll-table-scroll::-webkit-scrollbar-thumb {
        background: #334155;
    }

    html.dark .payroll-pagination-wrapper nav[role="navigation"] a,
    body.dark .payroll-pagination-wrapper nav[role="navigation"] a,
    .dark .payroll-pagination-wrapper nav[role="navigation"] a,
    html.dark .payroll-pagination-wrapper nav[role="navigation"] span[aria-disabled="true"] > span,
    body.dark .payroll-pagination-wrapper nav[role="navigation"] span[aria-disabled="true"] > span,
    .dark .payroll-pagination-wrapper nav[role="navigation"] span[aria-disabled="true"] > span {
        background-color: #0f172a !important;
        border-color: #1e293b !important;
        color: #cbd5e1 !important;
    }
    html.dark .payroll-pagination-wrapper nav[role="navigation"] a:hover,
    body.dark .payroll-pagination-wrapper nav[role="navigation"] a:hover,
    .dark .payroll-pagination-wrapper nav[role="navigation"] a:hover {
        background-color: #1e293b !important;
        color: #ffffff !important;
    }
    html.dark .payroll-pagination-wrapper nav[role="navigation"] span[aria-current="page"] > span,
    body.dark .payroll-pagination-wrapper nav[role="navigation"] span[aria-current="page"] > span,
    .dark .payroll-pagination-wrapper nav[role="navigation"] span[aria-current="page"] > span {
        background-color: #4f46e5 !important;
        border-color: #4f46e5 !important;
        color: #ffffff !important;
    }
</style>
@endsection

@section('scripts')
<script>
    // [FITUR BARU] Live-Search Instan Nama / NIK Karyawan & Sinkronisasi Widget Summary
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchPayrollInput');
        const rows = document.querySelectorAll('.payroll-row');
        const emptyLiveRow = document.getElementById('emptyLiveSearchRow');
        const statusFilterBtns = document.querySelectorAll('.status-filter-btn');

        const statTotalGaji = document.getElementById('statTotalGaji');
        const statTotalKaryawan = document.getElementById('statTotalKaryawan');
        const statTotalDraft = document.getElementById('statTotalDraft');

        if (!searchInput || rows.length === 0) return;

        let activeStatusFilter = 'all';

        function formatRupiah(angka) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
        }

        function applyPayrollFilters() {
            const keyword = searchInput.value.toLowerCase().trim();
            let visibleCount = 0;
            let sumNet = 0;
            let sumDraft = 0;

            rows.forEach(row => {
                const searchData = row.dataset.search || '';
                const rowStatus = row.dataset.status || 'draft';
                const isMatchKeyword = searchData.includes(keyword);
                const isMatchStatus = (activeStatusFilter === 'all') || (rowStatus === activeStatusFilter);

                if (isMatchKeyword && isMatchStatus) {
                    row.classList.remove('hidden');
                    visibleCount++;
                    sumNet += parseFloat(row.dataset.net || 0);
                    sumDraft += parseInt(row.dataset.draft || 0, 10);
                } else {
                    row.classList.add('hidden');
                }
            });

            if (emptyLiveRow) {
                if (visibleCount === 0) {
                    emptyLiveRow.classList.remove('hidden');
                } else {
                    emptyLiveRow.classList.add('hidden');
                }
            }

            if (statTotalGaji) statTotalGaji.innerText = formatRupiah(sumNet);
            if (statTotalKaryawan) statTotalKaryawan.innerText = visibleCount;
            if (statTotalDraft) statTotalDraft.innerText = sumDraft;
        }

        searchInput.addEventListener('input', applyPayrollFilters);

        // [BARU] Event Listener Filter Cepat Status (Semua / Published / Draft)
        statusFilterBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                activeStatusFilter = this.dataset.statusFilter;

                statusFilterBtns.forEach(b => {
                    b.classList.remove('active', 'bg-white', 'dark:bg-indigo-600', 'text-slate-800', 'dark:text-white', 'shadow-sm');
                    b.classList.add('text-slate-500', 'dark:text-slate-400');
                });

                this.classList.add('active', 'bg-white', 'dark:bg-indigo-600', 'text-slate-800', 'dark:text-white', 'shadow-sm');
                this.classList.remove('text-slate-500', 'dark:text-slate-400');

                applyPayrollFilters();
            });
        });
    });
</script>
@endsection