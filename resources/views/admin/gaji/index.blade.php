@extends('admin.layouts.master')
@section('title', 'Manajemen Gaji (Master) - PT. Happy Momo Indonesia')

@section('content')
<div class="max-w-7xl mx-auto pb-20 px-4 animate-fadeIn">
    
    {{-- 1. BREADCRUMB --}}
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
            <li><a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Home</a></li>
            <li><i class="fas fa-chevron-right text-[8px] opacity-50"></i></li>
            <li class="text-slate-600 dark:text-slate-300">Payroll System Master</li>
        </ol>
    </nav>

    {{-- 2. HEADER & TOOLBAR SECTION --}}
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-6 mb-8 relative z-30">
        <div>
            <div class="flex items-center gap-4 mb-2">
                <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-200 dark:shadow-indigo-950/60">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tighter">Manajemen Gaji</h1>
                </div>
            </div>
            <p class="text-slate-400 dark:text-slate-400 text-sm font-medium ml-16">Kelola komponen gaji pokok, tunjangan, dan iuran BPJS karyawan secara terpusat.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3.5 w-full lg:w-auto">
            @php
                // [BARU] Menentukan label cabang aktif untuk Custom Dropdown Anti-Bocor Dark Mode
                $selectedCabangObj = (isset($cabangs) && count($cabangs) > 0)
                    ? collect($cabangs)->firstWhere('id', request('cabang_id'))
                    : null;
                $selectedCabangLabel = $selectedCabangObj
                    ? ($selectedCabangObj->name ?? $selectedCabangObj->nama_cabang ?? 'Cabang')
                    : 'Semua Cabang';
            @endphp
            
            {{-- Search Bar & Filter Cabang --}}
            <form action="{{ route('admin.gaji.index') }}" method="GET" id="gajiMasterFilterForm" class="relative w-full sm:w-[450px] lg:w-[510px] flex flex-col sm:flex-row gap-2.5">
                <input type="hidden" name="periode" value="{{ request('periode', date('Y-m')) }}">
                
                {{-- TAMBAHAN: Filter Cabang (Dilengkapi Custom Dropdown Anti-Bocor + Select Asli Tersinkronisasi) --}}
                <div class="relative w-full sm:w-2/5 min-w-[170px]" id="gajiCabangDropdownWrapper">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10">
                        <i class="fas fa-building text-slate-400 dark:text-slate-500 text-xs"></i>
                    </div>

                    {{-- [BARU] Tombol Trigger Custom Dropdown Cabang --}}
                    <button type="button" onclick="toggleGajiCabangDropdown()"
                        class="w-full pl-9 pr-8 py-3.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-indigo-400 dark:hover:border-indigo-500/60 rounded-2xl text-xs font-bold text-slate-600 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none transition-all shadow-sm dark:shadow-none text-left flex items-center justify-between cursor-pointer">
                        <span class="truncate">{{ $selectedCabangLabel }}</span>
                    </button>

                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none z-10">
                        <i id="gajiCabangDropdownIcon" class="fas fa-chevron-down text-slate-400 dark:text-slate-500 text-[10px] transition-transform duration-200"></i>
                    </div>

                    {{-- [BARU] Menu Popup Cabang (Gelap di Dark Mode, Putih di Light Mode) --}}
                    <div id="gajiCabangDropdownMenu"
                        class="hidden absolute left-0 top-full mt-2 w-64 max-h-64 overflow-y-auto gaji-dropdown-scroll rounded-2xl bg-white dark:bg-[#13151d] border border-slate-200 dark:border-slate-800 shadow-2xl shadow-slate-900/15 dark:shadow-black/80 py-1.5 z-50">
                        <button type="button" onclick="selectGajiCabang('')"
                            class="w-full px-4 py-2.5 text-left text-xs flex items-center justify-between transition-colors cursor-pointer {{ !request('cabang_id') ? 'bg-indigo-600 text-white font-black' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/90 font-bold' }}">
                            <span>Semua Cabang</span>
                            @if(!request('cabang_id'))
                                <i class="fas fa-check text-[10px]"></i>
                            @endif
                        </button>
                        @if(isset($cabangs) && count($cabangs) > 0)
                            @foreach($cabangs as $cabang)
                                <button type="button" onclick="selectGajiCabang('{{ $cabang->id }}')"
                                    class="w-full px-4 py-2.5 text-left text-xs flex items-center justify-between transition-colors cursor-pointer {{ (string)request('cabang_id') === (string)$cabang->id ? 'bg-indigo-600 text-white font-black' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/90 font-bold' }}">
                                    <span class="truncate pr-2">{{ $cabang->name ?? $cabang->nama_cabang ?? 'Cabang' }}</span>
                                    @if((string)request('cabang_id') === (string)$cabang->id)
                                        <i class="fas fa-check text-[10px] shrink-0"></i>
                                    @endif
                                </button>
                            @endforeach
                        @endif
                    </div>

                    {{-- Select Asli Cabang (Tetap Dipertahankan & Tersinkronisasi) --}}
                    <select name="cabang_id" id="nativeGajiCabangSelect" class="sr-only dark:[color-scheme:dark] w-full pl-9 pr-4 py-3.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl text-xs font-bold text-slate-600 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none transition-all shadow-sm appearance-none cursor-pointer" onchange="this.form.submit()">
                        <option value="" class="bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200">Semua Cabang</option>
                        @if(isset($cabangs) && count($cabangs) > 0)
                            @foreach($cabangs as $cabang)
                                <option value="{{ $cabang->id }}" class="bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200" {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>
                                    {{ $cabang->name ?? $cabang->nama_cabang ?? 'Cabang' }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Input Search Asli --}}
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / NIK / Jabatan..." 
                        class="w-full pl-11 {{ request('search') ? 'pr-9' : 'pr-4' }} py-3.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-indigo-400 dark:hover:border-indigo-500/60 rounded-2xl text-xs font-bold text-slate-600 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 outline-none transition-all shadow-sm dark:shadow-none">
                    <i class="fas fa-search absolute left-4 top-4 text-slate-400 dark:text-slate-500"></i>
                    
                    {{-- [BARU] Tombol Reset Cepat Kata Kunci Pencarian --}}
                    @if(request('search'))
                        <a href="{{ route('admin.gaji.index', array_filter(['cabang_id' => request('cabang_id'), 'periode' => request('periode')])) }}"
                            class="absolute right-3 top-3 w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-rose-500 dark:hover:text-rose-400 flex items-center justify-center transition-colors"
                            title="Hapus Pencarian">
                            <i class="fas fa-times text-[10px]"></i>
                        </a>
                    @endif
                </div>
            </form>

            {{-- Action Button --}}
            <a href="{{ route('admin.gaji.create') }}" class="w-full sm:w-auto bg-indigo-600 text-white px-6 py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-500 transition-all shadow-xl shadow-indigo-200 dark:shadow-indigo-950/60 flex items-center justify-center gap-2 active:scale-95 whitespace-nowrap">
                <i class="fas fa-plus"></i> Atur Master Gaji Kolektif
            </a>
        </div>
    </div>

    {{-- [BARU] Indikator Filter Aktif (Jika sedang memfilter cabang atau pencarian) --}}
    @if(request('search') || request('cabang_id'))
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6 px-4 py-3 rounded-2xl bg-indigo-50/70 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20">
        <div class="flex flex-wrap items-center gap-2 text-xs">
            <span class="text-[10px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400">
                <i class="fas fa-filter mr-1"></i> Filter Aktif:
            </span>
            @if(request('cabang_id'))
                <span class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-900 border border-indigo-200/70 dark:border-slate-700 text-[10px] font-black uppercase text-slate-700 dark:text-slate-200">
                    Unit: {{ $selectedCabangLabel }}
                </span>
            @endif
            @if(request('search'))
                <span class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-900 border border-indigo-200/70 dark:border-slate-700 text-[10px] font-black text-slate-700 dark:text-slate-200">
                    Kata Kunci: "{{ request('search') }}"
                </span>
            @endif
        </div>
        <a href="{{ route('admin.gaji.index', ['periode' => request('periode', date('Y-m'))]) }}"
            class="text-[10px] font-black uppercase tracking-widest text-rose-600 dark:text-rose-400 hover:underline flex items-center gap-1.5">
            <i class="fas fa-undo-alt text-[9px]"></i> Reset Filter
        </a>
    </div>
    @endif

    {{-- 3. QUICK STATS WIDGETS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
        
        {{-- Total Karyawan --}}
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-none flex items-center gap-5 hover:border-indigo-200 dark:hover:border-slate-700 transition-all">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 border border-transparent dark:border-indigo-500/30 flex items-center justify-center text-2xl shrink-0">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest mb-1">Total Karyawan</p>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">{{ $users->total() ?? 0 }}</h3>
            </div>
        </div>
        
        {{-- Periode Aktif (Filter Auto-Submit) --}}
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-none flex items-center gap-5 relative group hover:border-emerald-200 dark:hover:border-emerald-500/40 transition-all">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-transparent dark:border-emerald-500/30 flex items-center justify-center text-2xl shrink-0">
                <i class="far fa-calendar-alt"></i>
            </div>
            <div class="w-full">
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest mb-1">Periode Aktif</p>
                <form action="{{ route('admin.gaji.index') }}" method="GET" class="m-0 p-0 relative">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="cabang_id" value="{{ request('cabang_id') }}">
                    <input type="month" name="periode" value="{{ request('periode', date('Y-m')) }}" 
                        onchange="this.form.submit()"
                        class="w-full !bg-transparent dark:[color-scheme:dark] border-none p-0 text-xl font-black text-slate-800 dark:text-white tracking-tighter focus:ring-0 cursor-pointer"
                        title="Pilih Bulan & Tahun">
                </form>
            </div>
            <div class="absolute right-6 opacity-40 group-hover:opacity-100 transition-opacity pointer-events-none">
                <i class="fas fa-chevron-down text-emerald-500 dark:text-emerald-400 text-sm"></i>
            </div>
        </div>
        
        {{-- Sync Status --}}
        <div class="bg-indigo-600 dark:bg-gradient-to-r dark:from-indigo-600 dark:to-indigo-500 p-6 rounded-3xl border border-indigo-700 dark:border-indigo-400/30 shadow-lg shadow-indigo-200 dark:shadow-none flex justify-between items-center relative overflow-hidden">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
            <div>
                <p class="text-[10px] font-black text-indigo-200 dark:text-indigo-100 uppercase tracking-widest mb-1">Sync Status</p>
                <h3 class="text-xl font-black text-white tracking-tight flex items-center gap-2">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    Data Terintegrasi
                </h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-white/15 text-white flex items-center justify-center text-xl shrink-0 backdrop-blur-sm">
                <i class="fas fa-sync-alt animate-spin-slow"></i>
            </div>
        </div>
    </div>

    {{-- 4. MASTER PAYROLL DATA TABLE --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-none overflow-hidden flex flex-col min-h-[450px]">
        <div class="overflow-x-auto gaji-table-scroll">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-slate-950/80 border-b border-slate-200 dark:border-slate-800">
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest whitespace-nowrap">Info Karyawan</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest whitespace-nowrap text-center">Unit Kerja</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest whitespace-nowrap text-center">Jabatan / Dept</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-800 dark:text-indigo-300 uppercase tracking-widest text-right bg-indigo-50/50 dark:bg-indigo-500/10 whitespace-nowrap">Gaji Pokok (Master)</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest text-center whitespace-nowrap">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-sm">
                    @forelse($users as $user)

                    {{-- DOUBLE PROTECTION LOGIC --}}
                    @php
                        $branchName = 'BELUM ADA CABANG';
                        $hasBranch  = false;

                        if ($user->branch) {
                            $branchName = $user->branch->name ?? $user->branch->nama_cabang ?? $user->branch->nama ?? $user->branch->company_name ?? 'CABANG TERDAFTAR';
                            $hasBranch  = true;
                        } 
                        elseif ($user->branch_id) {
                            try {
                                $branchDb = \Illuminate\Support\Facades\DB::table('branches')->where('id', $user->branch_id)->first();
                                if ($branchDb) {
                                    $branchName = $branchDb->name ?? $branchDb->nama_cabang ?? $branchDb->nama ?? $branchDb->company_name ?? 'CABANG TERDAFTAR';
                                    $hasBranch  = true;
                                }
                            } catch (\Exception $e) { }
                        }
                    @endphp

                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/45 transition-colors duration-200 group">
                        
                        {{-- Profil Karyawan --}}
                        <td class="px-8 py-5 align-middle">
                            <div class="flex items-center gap-4">
                                <div class="relative shrink-0">
                                    <img src="{{ $user->avatar_url }}" class="w-12 h-12 rounded-2xl object-cover border-2 border-white dark:border-slate-800 shadow-sm dark:shadow-none group-hover:scale-105 transition-transform duration-300">
                                    @if($user->salary && $user->salary->base_salary > 0)
                                        <div class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-emerald-500 border-2 border-white dark:border-slate-900 rounded-full shadow-sm" title="Gaji Sudah Diatur"></div>
                                    @else
                                        <div class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-amber-400 border-2 border-white dark:border-slate-900 rounded-full shadow-sm" title="Gaji Belum Diatur"></div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-black uppercase text-slate-800 dark:text-white tracking-tight leading-none mb-1.5 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $user->name }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-widest">{{ $user->nik }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Unit Kerja (CABANG) --}}
                        <td class="px-6 py-5 align-middle text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest {{ $hasBranch ? 'bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-500/30' : 'bg-rose-50 dark:bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-500/30' }}">
                                <i class="fas {{ $hasBranch ? 'fa-building' : 'fa-exclamation-circle' }} {{ $hasBranch ? 'text-indigo-400 dark:text-indigo-400' : 'text-rose-400 dark:text-rose-400' }}"></i> 
                                {{ $branchName }}
                            </span>
                        </td>

                        {{-- Jabatan --}}
                        <td class="px-6 py-5 align-middle text-center">
                            <p class="text-[10px] font-black uppercase tracking-tighter text-slate-700 dark:text-slate-200 leading-none mb-1">{{ $user->position->name ?? '-' }}</p>
                            <p class="text-[8px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">{{ $user->department->name ?? '-' }}</p>
                        </td>

                        {{-- Gaji Pokok --}}
                        <td class="px-8 py-5 align-middle text-right bg-indigo-50/30 dark:bg-indigo-500/[0.04] group-hover:bg-indigo-50/80 dark:group-hover:bg-indigo-500/10 transition-colors">
                            @if($user->salary && $user->salary->base_salary > 0)
                                <span class="text-sm font-black text-slate-800 dark:text-white font-mono tracking-tight block">
                                    Rp {{ number_format($user->salary->base_salary, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest bg-amber-50 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-500/35 border-dashed">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Belum Diatur
                                </span>
                            @endif
                        </td>

                        {{-- Action Detail --}}
                        <td class="px-8 py-5 align-middle text-center">
                            {{-- TAMBAHAN: Ubah link agar mem-passing parameter ID Karyawan secara otomatis ke halaman Kolektif --}}
                            <a href="{{ route('admin.gaji.create', ['karyawan_id' => $user->id]) }}" class="inline-flex w-10 h-10 items-center justify-center bg-white dark:bg-slate-800/90 text-slate-400 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-white hover:bg-indigo-50 dark:hover:bg-indigo-600 hover:border-indigo-200 dark:hover:border-indigo-500 rounded-xl border border-slate-200 dark:border-slate-700 transition-all shadow-sm dark:shadow-none group-hover:shadow-md" title="Atur Komponen Gaji Karyawan Ini">
                                <i class="fas fa-external-link-alt text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-10 py-24 text-center">
                            <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800/70 text-slate-300 dark:text-slate-600 rounded-3xl flex items-center justify-center text-2xl mx-auto mb-4 border-2 border-dashed border-slate-200 dark:border-slate-700">
                                <i class="fas fa-users-slash"></i>
                            </div>
                            <h3 class="text-lg font-black text-slate-700 dark:text-slate-200 uppercase tracking-tight">Tidak Ada Data Karyawan</h3>
                            <p class="text-xs font-medium text-slate-400 dark:text-slate-500 mt-1">Sistem belum menemukan data karyawan pada pencarian ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if(isset($users) && $users->hasPages())
        <div class="px-8 py-5 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-100 dark:border-slate-800 mt-auto gaji-pagination-wrapper">
            {{ $users->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    {{-- 5. HELP BANNER --}}
    <div class="mt-8 p-8 md:p-10 rounded-3xl bg-slate-900 dark:bg-slate-900/95 text-white relative overflow-hidden shadow-2xl dark:shadow-none border border-slate-800 flex flex-col md:flex-row items-center justify-between gap-8">
        <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-indigo-500/20 rounded-full blur-3xl"></div>
        <div class="absolute -left-10 -top-10 w-40 h-40 bg-purple-500/10 rounded-full blur-2xl"></div>
        
        <div class="relative z-10 text-center md:text-left">
            <h4 class="text-2xl font-black tracking-tight mb-2">Automatisasi Payroll PT. Happy Momo Indonesia</h4>
            <p class="text-slate-400 text-sm font-medium tracking-wide">Gaji pokok dan tunjangan tetap yang diatur di sini akan menjadi dasar perhitungan iuran BPJS otomatis.</p>
        </div>
        
        <div class="relative z-10 w-full md:w-auto">
            <a href="https://wa.me/6281234567890?text=Halo%20Admin,%20saya%20membutuhkan%20bantuan%20terkait%20manajemen%20gaji." target="_blank" class="block w-full md:w-auto bg-white dark:bg-indigo-600 text-slate-900 dark:text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-500 hover:text-white dark:hover:bg-indigo-500 transition-all shadow-xl dark:shadow-none active:scale-95 text-center whitespace-nowrap">
                <i class="fab fa-whatsapp text-sm mr-1"></i> Minta Bantuan
            </a>
        </div>
    </div>
</div>

<style>
    .animate-spin-slow { animation: spin 6s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    
    /* Menghilangkan icon default input month pada webkit */
    input[type="month"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        opacity: 0; 
        position: absolute;
        right: 0;
        top: 0;
        width: 100%;
        height: 100%;
    }

    /* [BARU] Dukungan Dark Mode untuk Month Picker, Dropdown Scrollbar, dan Pagination Laravel */
    html.dark input[type="month"],
    body.dark input[type="month"],
    .dark input[type="month"] {
        color-scheme: dark;
    }

    .gaji-dropdown-scroll::-webkit-scrollbar,
    .gaji-table-scroll::-webkit-scrollbar {
        width: 6px;
        height: 8px;
    }
    .gaji-dropdown-scroll::-webkit-scrollbar-track,
    .gaji-table-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .gaji-dropdown-scroll::-webkit-scrollbar-thumb,
    .gaji-table-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 9999px;
    }
    html.dark .gaji-dropdown-scroll::-webkit-scrollbar-thumb,
    body.dark .gaji-dropdown-scroll::-webkit-scrollbar-thumb,
    .dark .gaji-dropdown-scroll::-webkit-scrollbar-thumb,
    html.dark .gaji-table-scroll::-webkit-scrollbar-thumb,
    body.dark .gaji-table-scroll::-webkit-scrollbar-thumb,
    .dark .gaji-table-scroll::-webkit-scrollbar-thumb {
        background: #334155;
    }

    /* Mencegah tombol pagination bawaan Tailwind Laravel bocor putih di Dark Mode */
    html.dark .gaji-pagination-wrapper nav[role="navigation"] a,
    body.dark .gaji-pagination-wrapper nav[role="navigation"] a,
    .dark .gaji-pagination-wrapper nav[role="navigation"] a,
    html.dark .gaji-pagination-wrapper nav[role="navigation"] span[aria-disabled="true"] > span,
    body.dark .gaji-pagination-wrapper nav[role="navigation"] span[aria-disabled="true"] > span,
    .dark .gaji-pagination-wrapper nav[role="navigation"] span[aria-disabled="true"] > span {
        background-color: #0f172a !important;
        border-color: #1e293b !important;
        color: #cbd5e1 !important;
    }
    html.dark .gaji-pagination-wrapper nav[role="navigation"] a:hover,
    body.dark .gaji-pagination-wrapper nav[role="navigation"] a:hover,
    .dark .gaji-pagination-wrapper nav[role="navigation"] a:hover {
        background-color: #1e293b !important;
        color: #ffffff !important;
    }
    html.dark .gaji-pagination-wrapper nav[role="navigation"] span[aria-current="page"] > span,
    body.dark .gaji-pagination-wrapper nav[role="navigation"] span[aria-current="page"] > span,
    .dark .gaji-pagination-wrapper nav[role="navigation"] span[aria-current="page"] > span {
        background-color: #4f46e5 !important;
        border-color: #4f46e5 !important;
        color: #ffffff !important;
    }
</style>

{{-- [BARU] Script Interaktif Custom Dropdown Cabang (Tersinkronisasi dengan Select Asli) --}}
<script>
    function toggleGajiCabangDropdown() {
        const menu = document.getElementById('gajiCabangDropdownMenu');
        const icon = document.getElementById('gajiCabangDropdownIcon');
        if (menu) {
            const isHidden = menu.classList.contains('hidden');
            menu.classList.toggle('hidden', !isHidden);
            icon?.classList.toggle('rotate-180', isHidden);
        }
    }

    function selectGajiCabang(cabangId) {
        const nativeSelect = document.getElementById('nativeGajiCabangSelect');
        if (nativeSelect) {
            nativeSelect.value = cabangId;
            document.getElementById('gajiMasterFilterForm').submit();
        }
    }

    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('gajiCabangDropdownWrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            document.getElementById('gajiCabangDropdownMenu')?.classList.add('hidden');
            document.getElementById('gajiCabangDropdownIcon')?.classList.remove('rotate-180');
        }
    });
</script>
@endsection