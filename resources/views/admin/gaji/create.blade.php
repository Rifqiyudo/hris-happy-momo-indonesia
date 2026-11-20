@extends('admin.layouts.master')
@section('title', 'Master Komponen Gaji Kolektif - Happy Momo')

{{-- TAMBAHAN: STYLES UNTUK SELECT2 (PENCARIAN NAMA KARYAWAN) --}}
@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Modifikasi UI Select2 agar menyatu dengan gaya Tailwind Happy Momo */
    .select2-container--default .select2-selection--single {
        height: 46px !important;
        border-radius: 1rem !important;
        border: 1px solid #e2e8f0 !important;
        display: flex;
        align-items: center;
        font-size: 0.75rem;
        font-weight: 700;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #334155 !important;
        padding-left: 1.25rem !important;
        padding-right: 2.25rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px !important;
        right: 12px !important;
    }
    .select2-dropdown {
        border-radius: 1rem !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
        font-size: 0.75rem;
        overflow: hidden;
    }
    .select2-search__field {
        border-radius: 0.5rem !important;
        padding: 8px 12px !important;
        outline: none !important;
    }
    .select2-search__field:focus {
        border: 1px solid #6366f1 !important;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2) !important;
    }
    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
        background-color: #6366f1 !important;
        color: white !important;
    }

    /* [BARU] Dukungan Dark Mode Penuh untuk Select2 (Anti-Bocor) */
    html.dark .select2-container--default .select2-selection--single,
    body.dark .select2-container--default .select2-selection--single,
    .dark .select2-container--default .select2-selection--single {
        background-color: #020617 !important;
        border-color: #1e293b !important;
        box-shadow: none !important;
    }
    html.dark .select2-container--default.select2-container--open .select2-selection--single,
    body.dark .select2-container--default.select2-container--open .select2-selection--single,
    .dark .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.18) !important;
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
        margin-right: 4px;
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
    html.dark .select2-search__field,
    body.dark .select2-search__field,
    .dark .select2-search__field {
        background-color: #020617 !important;
        border: 1px solid #334155 !important;
        color: #f8fafc !important;
    }
    html.dark .select2-results__option,
    body.dark .select2-results__option,
    .dark .select2-results__option {
        color: #cbd5e1 !important;
        font-weight: 700;
        padding: 10px 14px !important;
    }
    html.dark .select2-results__option[aria-selected=true],
    body.dark .select2-results__option[aria-selected=true],
    .dark .select2-results__option[aria-selected=true] {
        background-color: #1e293b !important;
        color: #ffffff !important;
    }
    html.dark .select2-results__option--highlighted.select2-results__option--selectable,
    body.dark .select2-results__option--highlighted.select2-results__option--selectable,
    .dark .select2-results__option--highlighted.select2-results__option--selectable {
        background-color: #4f46e5 !important;
        color: #ffffff !important;
    }
</style>
@endsection

@section('content')
<div class="max-w-7xl mx-auto pb-20 px-4 animate-fadeIn">
    {{-- 1. BREADCRUMB --}}
    <nav class="flex mb-6 px-2" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">
            <li><a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Home</a></li>
            <li><i class="fas fa-chevron-right text-[8px]"></i></li>
            <li><a href="{{ route('admin.gaji.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Gaji</a></li>
            <li><i class="fas fa-chevron-right text-[8px]"></i></li>
            <li class="text-slate-600 dark:text-slate-300">Master Komponen Kolektif</li>
        </ol>
    </nav>

    <div class="space-y-6">
        {{-- 2. SECTION UPLOAD CSV (IMPORT & EXPORT) - COMPACT VERSION --}}
        <div class="bg-white dark:bg-slate-900 rounded-[2rem] shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-800 overflow-hidden">
            <div class="p-6 border-b border-slate-50 dark:border-slate-800 bg-gradient-to-r from-white to-slate-50/50 dark:from-slate-900 dark:to-slate-950/70 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-500/15 border border-transparent dark:border-indigo-500/30 rounded-2xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 shadow-sm dark:shadow-none">
                        <i class="fas fa-file-import text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-black text-slate-700 dark:text-white tracking-tight uppercase leading-none">Export / Import CSV</h1>
                        <p class="text-slate-400 dark:text-slate-400 text-[10px] mt-1 font-bold uppercase tracking-tighter">Kelola data komponen gaji secara massal via Excel/CSV.</p>
                    </div>
                </div>
                
                {{-- TOMBOL EXPORT & DOWNLOAD TEMPLATE --}}
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                    <a href="{{ route('admin.gaji.export') }}" class="w-full sm:w-auto bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-400 px-5 py-2.5 rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-emerald-500 dark:hover:bg-emerald-600 hover:text-white dark:hover:text-white transition-all shadow-sm dark:shadow-none flex items-center justify-center gap-2">
                        <i class="fas fa-file-excel"></i> Export Data Gaji
                    </a>
                    <a href="{{ route('admin.gaji.download-template') }}" class="w-full sm:w-auto bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-200 px-5 py-2.5 rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-slate-700 transition shadow-sm dark:shadow-none flex items-center justify-center gap-2">
                        <i class="fas fa-download text-indigo-500 dark:text-indigo-400"></i> Download Template
                    </a>
                </div>
            </div>
            
            <div class="p-6 bg-slate-50/30 dark:bg-slate-950/40">
                <form action="{{ route('admin.gaji.import.process') }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row items-center gap-4">
                    @csrf
                    {{-- Area Upload yang dikecilkan --}}
                    <div class="relative w-full md:w-2/3 group">
                        <input type="file" name="file_csv" id="file_csv" required accept=".csv,.xlsx,.xls" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="w-full bg-white dark:bg-slate-950 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl py-3 px-5 flex items-center justify-between group-hover:border-indigo-400 dark:group-hover:border-indigo-500/70 group-hover:bg-indigo-50/30 dark:group-hover:bg-indigo-500/10 transition-all">
                            <div class="flex items-center gap-3 min-w-0">
                                <i class="fas fa-cloud-upload-alt text-indigo-400 text-lg"></i>
                                <span id="file-name-display" class="text-[11px] font-bold text-slate-400 dark:text-slate-400 italic truncate tracking-tight">Klik atau seret file .csv / .xlsx ke sini...</span>
                            </div>
                            <span class="bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-300 text-[8px] font-black px-2.5 py-1 rounded-lg uppercase group-hover:bg-indigo-600 group-hover:text-white transition-colors shrink-0">Pilih File</span>
                        </div>
                    </div>
                    <div class="w-full md:w-1/3">
                        <button type="submit" class="w-full bg-indigo-600 text-white px-6 py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-[0.15em] shadow-lg shadow-indigo-100 dark:shadow-indigo-950/60 hover:bg-indigo-500 transition transform active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fas fa-rocket"></i> Unggah Data Kolektif
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 3. SECTION INPUT MANUAL MULTIPLE ROWS --}}
        <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-800 overflow-hidden">
            <div class="p-8 border-b border-slate-50 dark:border-slate-800 bg-gradient-to-r from-white to-slate-50/50 dark:from-slate-900 dark:to-slate-950/70 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-xl font-black text-slate-700 dark:text-white tracking-tight uppercase">Input Manual Kolektif</h2>
                        {{-- [BARU] Badge Counter Jumlah Baris Aktif --}}
                        <span id="rowCountBadge" class="px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-500/15 border border-indigo-100 dark:border-indigo-500/30 text-[10px] font-black uppercase tracking-wider text-indigo-600 dark:text-indigo-400">
                            1 Baris
                        </span>
                    </div>
                    <p class="text-slate-400 dark:text-slate-400 text-[10px] font-bold uppercase tracking-tighter mt-1 italic">Input Gaji Pokok, Tunjangan, Lembur, dan BPJS secara kolektif.</p>
                </div>
                <div class="flex items-center gap-3 bg-emerald-50 dark:bg-emerald-500/15 px-5 py-2.5 rounded-2xl border border-emerald-100 dark:border-emerald-500/30 shadow-sm dark:shadow-none">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    <span class="text-[10px] font-black text-emerald-700 dark:text-emerald-400 uppercase tracking-tighter">Sistem Validasi Unit Kerja Aktif</span>
                </div>
            </div>

            <form action="{{ route('admin.gaji.store') }}" method="POST" id="salaryForm">
                @csrf
                <div class="overflow-x-auto custom-scrollbar">
                    {{-- Melebarkan tabel agar mengakomodasi kolom baru --}}
                    <table class="w-full text-left border-collapse" id="salaryTable" style="min-width: 1800px;">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-[0.15em] bg-slate-50/50 dark:bg-slate-950/80">
                                <th class="p-6 w-80 border-b border-slate-100 dark:border-slate-800 text-center">Karyawan & Unit Kerja</th>
                                <th class="p-6 w-48 border-b border-slate-100 dark:border-slate-800 text-center text-indigo-600 dark:text-indigo-400">Gaji Pokok & Tj. Jabatan</th>
                                <th class="p-6 w-48 border-b border-slate-100 dark:border-slate-800 text-center">Tj. Keluarga & THR</th>
                                <th class="p-6 w-48 border-b border-slate-100 dark:border-slate-800 text-center">Makan & Transport (Harian)</th>
                                
                                {{-- Judul Kolom Diupdate Menambahkan Lembur --}}
                                <th class="p-6 w-48 border-b border-slate-100 dark:border-slate-800 text-center text-emerald-600 dark:text-emerald-400">Bonus/Lembur/Lainnya</th>
                                
                                {{-- KOLOM BARU KHUSUS POTONGAN & TELAT --}}
                                <th class="p-6 w-48 border-b border-slate-100 dark:border-slate-800 text-center text-rose-600 dark:text-rose-400">Potongan & Denda</th>
                                
                                <th class="p-6 w-44 text-center bg-indigo-50/30 dark:bg-indigo-500/10 border-b border-indigo-100 dark:border-indigo-500/20 dark:text-indigo-300">BPJS JHT & KES (Rp)</th>
                                <th class="p-6 w-20 border-b border-slate-100 dark:border-slate-800 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="formRows">
                            <tr class="row-salary group hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="p-4 border-b border-slate-50 dark:border-slate-800/80">
                                    <div class="flex items-center gap-2.5">
                                        {{-- [BARU] Nomor Urut Baris --}}
                                        <span class="row-index w-7 h-7 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[10px] font-black text-slate-500 dark:text-slate-300 flex items-center justify-center shrink-0">1</span>
                                        <div class="flex-1 min-w-0">
                                            {{-- TAMBAHAN: Class 'select2-employee' untuk mentrigger select2 --}}
                                            <select name="user_id[]" required class="user-select select2-employee w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl py-3.5 px-5 text-xs font-bold text-slate-700 dark:text-slate-100 outline-none cursor-pointer focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm dark:shadow-none">
                                                <option value="" data-allowance="0">Pilih Nama Karyawan...</option>
                                                @foreach($employees as $emp)
                                                    @php
                                                        $branchName = 'UNIT TIDAK TERDAFTAR';
                                                        if ($emp->branch) {
                                                            $branchName = $emp->branch->name ?? $emp->branch->nama_cabang ?? $emp->branch->nama ?? $emp->branch->company_name ?? 'UNIT TERDAFTAR';
                                                        } elseif ($emp->branch_id) {
                                                            try {
                                                                $bDb = \Illuminate\Support\Facades\DB::table('branches')->where('id', $emp->branch_id)->first();
                                                                if ($bDb) {
                                                                    $branchName = $bDb->name ?? $bDb->nama_cabang ?? $bDb->nama ?? $bDb->company_name ?? 'UNIT TERDAFTAR';
                                                                }
                                                            } catch (\Exception $e) {}
                                                        }
                                                        
                                                        // [PERBAIKAN BUG DESIMAL] Membulatkan nilai desimal (.00) dari MySQL
                                                        $tjJabatanNominal = round($emp->tj_jabatan_otomatis ?? 0);
                                                    @endphp
                                                    
                                                    {{-- TAMBAHAN: Logika Request URL ('karyawan_id') agar otomatis terpilih --}}
                                                    <option value="{{ $emp->id }}" data-allowance="{{ $tjJabatanNominal }}" {{ request('karyawan_id') == $emp->id ? 'selected' : '' }}>
                                                        {{ $emp->nik }} - {{ strtoupper($emp->name) }} [{{ strtoupper($branchName) }}]
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="p-4 border-b border-slate-50 dark:border-slate-800/80 bg-indigo-50/5 dark:bg-indigo-500/[0.03]">
                                    <div class="space-y-2">
                                        <input type="text" name="base_salary[]" placeholder="Gaji Pokok" required class="input-rupiah base-salary w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl py-3 px-4 text-xs font-black text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 text-right outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm dark:shadow-none">
                                        
                                        {{-- Input Tunjangan Jabatan --}}
                                        <input type="text" name="position_allowance[]" placeholder="Tj. Jabatan (Tetap)" class="input-rupiah pos-allowance w-full bg-indigo-50/50 dark:bg-indigo-500/15 border border-indigo-100 dark:border-indigo-500/30 rounded-xl py-3 px-4 text-xs font-bold text-indigo-700 dark:text-indigo-300 placeholder-indigo-400/70 dark:placeholder-indigo-300/50 text-right outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm dark:shadow-none">
                                    </div>
                                </td>
                                
                                <td class="p-4 border-b border-slate-50 dark:border-slate-800/80 text-center">
                                    <div class="space-y-2">
                                        <input type="text" name="family_allowance[]" placeholder="Tj. Keluarga" class="input-rupiah fam-allowance w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl py-3 px-4 text-xs font-bold text-slate-700 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 text-right outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm dark:shadow-none">
                                        <input type="text" name="thr_amount[]" placeholder="Nominal THR" class="input-rupiah w-full bg-amber-50/30 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/30 rounded-xl py-3 px-4 text-xs font-bold text-amber-700 dark:text-amber-300 placeholder-amber-500/60 dark:placeholder-amber-300/50 text-right outline-none focus:ring-2 focus:ring-amber-500 shadow-sm dark:shadow-none">
                                    </div>
                                </td>
                                
                                <td class="p-4 border-b border-slate-50 dark:border-slate-800/80">
                                    <div class="space-y-2">
                                        <input type="text" name="meal_allowance[]" placeholder="Uang Makan / Hari" class="input-rupiah w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl py-3 px-4 text-xs font-bold text-slate-700 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 text-right outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm dark:shadow-none">
                                        <input type="text" name="transport_allowance[]" placeholder="Transport / Hari" class="input-rupiah w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl py-3 px-4 text-xs font-bold text-slate-700 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 text-right outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm dark:shadow-none">
                                    </div>
                                </td>
                                
                                {{-- KOLOM BONUS & LEMBUR --}}
                                <td class="p-4 border-b border-slate-50 dark:border-slate-800/80 bg-emerald-50/10 dark:bg-emerald-500/[0.03]">
                                    <div class="space-y-2">
                                        <input type="text" name="bonus_amount[]" placeholder="Bonus" class="input-rupiah w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl py-3 px-4 text-xs font-bold text-slate-700 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 text-right outline-none shadow-sm dark:shadow-none focus:ring-2 focus:ring-emerald-500">
                                        <input type="text" name="overtime_amount[]" placeholder="Lembur" class="input-rupiah w-full bg-amber-50 dark:bg-amber-500/15 border border-amber-200 dark:border-amber-500/35 rounded-xl py-3 px-4 text-xs font-black text-amber-700 dark:text-amber-300 placeholder-amber-600/60 dark:placeholder-amber-300/60 text-right outline-none shadow-sm dark:shadow-none focus:ring-2 focus:ring-emerald-500">
                                        <input type="text" name="other_allowance[]" placeholder="Lainnya" class="input-rupiah w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl py-3 px-4 text-xs font-bold text-slate-700 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 text-right outline-none shadow-sm dark:shadow-none focus:ring-2 focus:ring-emerald-500">
                                    </div>
                                </td>

                                {{-- KOLOM BARU KHUSUS POTONGAN & TELAT --}}
                                <td class="p-4 border-b border-slate-50 dark:border-slate-800/80 bg-rose-50/10 dark:bg-rose-500/[0.03]">
                                    <div class="space-y-2">
                                        {{-- Input Potongan Manual (Kasbon/Lainnya) --}}
                                        <input type="text" name="routine_deduction[]" placeholder="Pot. Kasbon / Rutin" class="input-rupiah routine-deduction w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl py-3 px-4 text-xs font-bold text-slate-700 dark:text-rose-300 placeholder-slate-400 dark:placeholder-slate-500 text-right outline-none focus:ring-2 focus:ring-rose-500 transition-all shadow-sm dark:shadow-none">
                                        
                                        {{-- Indikator Visual Border Denda Telat (Otomatis) --}}
                                        <div class="w-full bg-rose-50 dark:bg-rose-500/10 border-2 border-dashed border-rose-200 dark:border-rose-500/30 rounded-xl py-2.5 px-4 flex justify-between items-center shadow-sm dark:shadow-none cursor-not-allowed opacity-90" title="Dihitung otomatis Rp 5.000 per 15 menit berdasarkan absensi saat proses Payroll akhir bulan">
                                            <span class="text-[10px] font-black text-rose-400 dark:text-rose-400 uppercase tracking-widest">Telat</span>
                                            <span class="text-[10px] font-bold text-rose-500 dark:text-rose-300 italic">Auto Sistem</span>
                                        </div>
                                    </div>
                                </td>
                                
                                {{-- Input BPJS Menjadi Bentuk Manual Rupiah --}}
                                <td class="p-4 border-b border-indigo-100 dark:border-indigo-500/20 bg-indigo-50/10 dark:bg-indigo-500/[0.05]">
                                    <div class="space-y-3">
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-[10px] font-black text-slate-400 dark:text-slate-500">Rp</span>
                                            </div>
                                            <input type="text" name="jht_deduction[]" placeholder="Nominal JHT" class="input-rupiah jht-nominal w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg py-2 pl-8 pr-2 text-[10px] font-black text-slate-700 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 text-right outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm dark:shadow-none">
                                        </div>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-[10px] font-black text-slate-400 dark:text-slate-500">Rp</span>
                                            </div>
                                            <input type="text" name="kes_deduction[]" placeholder="Nominal Kes." class="input-rupiah kes-nominal w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg py-2 pl-8 pr-2 text-[10px] font-black text-slate-700 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 text-right outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm dark:shadow-none">
                                        </div>
                                        <div class="pt-1 border-t border-indigo-100 dark:border-indigo-500/20">
                                            <small class="result-nominal text-[9px] text-indigo-600 dark:text-indigo-400 block text-center font-black">Est. Pot: Rp 0</small>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="p-4 border-b border-slate-50 dark:border-slate-800/80 text-center">
                                    <button type="button" class="remove-row w-10 h-10 inline-flex items-center justify-center text-slate-300 dark:text-slate-500 hover:text-rose-500 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/15 rounded-xl transition-all cursor-pointer" title="Hapus Baris">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- 4. FOOTER ACTIONS --}}
                <div class="p-8 md:p-10 border-t border-slate-50 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-950/40 flex flex-col md:flex-row justify-between items-center gap-6">
                    <button type="button" id="addRow" class="flex items-center gap-3 bg-white dark:bg-slate-900 border-2 border-dashed border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-300 hover:border-indigo-500 dark:hover:border-indigo-500 hover:text-indigo-600 dark:hover:text-indigo-400 px-8 py-4 rounded-2xl transition-all font-black uppercase text-[10px] active:scale-95 shadow-sm dark:shadow-none cursor-pointer">
                        <i class="fas fa-plus-circle text-base"></i> Tambah Baris Baru
                    </button>

                    <div class="flex items-center gap-4 w-full md:w-auto">
                        <a href="{{ route('admin.gaji.index') }}" class="w-full md:w-auto text-center px-10 py-5 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">Batal</a>
                        <button type="submit" class="w-full md:w-auto bg-emerald-500 dark:bg-emerald-600 text-white px-12 py-5 rounded-3xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-emerald-600 dark:hover:bg-emerald-500 transition shadow-2xl shadow-emerald-100 dark:shadow-emerald-950/50 active:scale-95 cursor-pointer">
                            Simpan Data Gaji Kolektif
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 10px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 8px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; border: 2px solid #f8fafc; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
    input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }

    /* [BARU] Perbaikan Scrollbar Horizontal Dark Mode (Anti-Bocor Garis Putih) */
    html.dark .custom-scrollbar::-webkit-scrollbar-track,
    body.dark .custom-scrollbar::-webkit-scrollbar-track,
    .dark .custom-scrollbar::-webkit-scrollbar-track {
        background: #0f172a;
    }
    html.dark .custom-scrollbar::-webkit-scrollbar-thumb,
    body.dark .custom-scrollbar::-webkit-scrollbar-thumb,
    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #334155;
        border: 2px solid #0f172a;
    }
    html.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover,
    body.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover,
    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #475569;
    }

    /* [BARU] SweetAlert2 Dark Mode Support */
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
@endsection

@section('scripts')
{{-- TAMBAHAN: SCRIPT JQUERY & SELECT2 --}}
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const formRows = document.getElementById('formRows');
        const salaryForm = document.getElementById('salaryForm');
        const fileInput = document.getElementById('file_csv');
        const fileNameDisplay = document.getElementById('file-name-display');

        // [BARU] Helper Sinkronisasi Nomor Urut & Counter Baris
        function syncRowNumbers() {
            const allRows = document.querySelectorAll('.row-salary');
            allRows.forEach((row, idx) => {
                const indexEl = row.querySelector('.row-index');
                if (indexEl) indexEl.innerText = idx + 1;
            });
            const badge = document.getElementById('rowCountBadge');
            if (badge) badge.innerText = `${allRows.length} Baris`;
        }

        // INISIALISASI SELECT2 PERTAMA KALI
        $('.select2-employee').select2({
            placeholder: "🔍 Ketik Nama / NIK Karyawan...",
            allowClear: true,
            width: '100%'
        });

        // Trigger perubahan jika halaman dimuat dengan parameter auto-select karyawan
        if ("{{ request('karyawan_id') }}" !== "") {
            $('.select2-employee').trigger('change');
        }

        // Update tampilan nama file saat dipilih
        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                fileNameDisplay.innerText = this.files[0].name;
                fileNameDisplay.classList.remove('italic', 'text-slate-400', 'dark:text-slate-400');
                fileNameDisplay.classList.add('text-indigo-600', 'dark:text-indigo-400', 'font-black');
            }
        });

        // Helper Format Rupiah
        function formatRupiah(angka) {
            if(!angka) return '';
            let number_string = angka.toString().replace(/[^,\d]/g, ''),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);
            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        }

        // Kalkulasi Live Estimasi Potongan
        function updateNominalPotongan(row) {
            const jhtNominal = parseFloat(row.querySelector('.jht-nominal').value.replace(/\./g, '')) || 0;
            const kesNominal = parseFloat(row.querySelector('.kes-nominal').value.replace(/\./g, '')) || 0;
            const rutinInput = row.querySelector('.routine-deduction');
            const rutinNominal = rutinInput ? (parseFloat(rutinInput.value.replace(/\./g, '')) || 0) : 0;
            
            const totalPotongan = jhtNominal + kesNominal + rutinNominal;
            row.querySelector('.result-nominal').innerText = 'Est. Pot: Rp ' + formatRupiah(Math.round(totalPotongan).toString());
        }

        // EVENT LISTENER UNTUK INPUT RUPIAH & POTONGAN
        formRows.addEventListener('input', function(e) {
            const row = e.target.closest('tr');
            if (e.target.classList.contains('input-rupiah')) {
                e.target.value = formatRupiah(e.target.value);
            }
            updateNominalPotongan(row);
        });

        // -------------------------------------------------------------
        // PERBAIKAN: EVENT LISTENER AUTO-FILL MENGGUNAKAN JQUERY 
        // KARENA SELECT2 MENGGUNAKAN EVENT JQUERY, BUKAN EVENT NATIVE JS
        // -------------------------------------------------------------
        $('#formRows').on('change', '.user-select', function(e) {
            const row = $(this).closest('tr')[0];
            const selectedOption = this.options[this.selectedIndex];
            
            if (!selectedOption) return; // Mencegah error saat di-clear
            
            const tjJabatanValue = selectedOption.getAttribute('data-allowance') || 0;
            const inputPosAllowance = row.querySelector('.pos-allowance');
            
            if(inputPosAllowance) {
                if (tjJabatanValue > 0) {
                    inputPosAllowance.value = formatRupiah(tjJabatanValue);
                    inputPosAllowance.classList.add('ring-2', 'ring-indigo-500');
                    setTimeout(() => {
                        inputPosAllowance.classList.remove('ring-2', 'ring-indigo-500');
                    }, 800);
                } else {
                    inputPosAllowance.value = '';
                }
            }
        });

        // -------------------------------------------------------------
        // PERBAIKAN BUG BARIS BARU: RE-INISIALISASI SELECT2 SAAT DICLONE
        // -------------------------------------------------------------
        document.getElementById('addRow').addEventListener('click', function() {
            const rows = document.querySelectorAll('.row-salary');
            
            // Hancurkan Select2 di baris referensi (baris pertama) terlebih dahulu secara sementara
            const originalSelect = $(rows[0]).find('.user-select');
            originalSelect.select2('destroy');
            
            // Lakukan Cloning HTML
            const newRow = rows[0].cloneNode(true);
            
            // Bersihkan value di baris baru
            newRow.querySelectorAll('input').forEach(input => input.value = ''); 
            newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
            newRow.querySelector('.result-nominal').innerText = 'Est. Pot: Rp 0';
            
            // Masukkan baris baru ke dalam tabel
            formRows.appendChild(newRow);
            syncRowNumbers();
            
            // Inisialisasi ulang Select2 pada baris asli dan baris baru
            $(rows[0]).find('.user-select').select2({
                placeholder: "🔍 Ketik Nama / NIK Karyawan...",
                allowClear: true,
                width: '100%'
            });
            
            $(newRow).find('.user-select').select2({
                placeholder: "🔍 Ketik Nama / NIK Karyawan...",
                allowClear: true,
                width: '100%'
            });

            newRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });

        // Hapus Baris
        formRows.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                if (document.querySelectorAll('.row-salary').length > 1) {
                    e.target.closest('tr').remove();
                    syncRowNumbers();
                } else {
                    Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Minimal harus ada 1 baris input data.' });
                }
            }
        });

        // Submit Form dengan Konfirmasi
        salaryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Simpan Data Kolektif?',
                text: "Pastikan nominal komponen gaji sudah benar.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#f43f5e',
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.querySelectorAll('.input-rupiah').forEach(input => {
                        input.value = input.value.replace(/\./g, '');
                    });
                    this.submit();
                }
            });
        });
    });
</script>
@endsection