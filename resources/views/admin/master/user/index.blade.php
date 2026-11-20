@extends('admin.layouts.master')
@section('title', 'Monitoring Karyawan - PT. Happy Momo Indonesia')

@section('content')
<div class="space-y-6 sm:space-y-8 max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 pb-20 pt-4 sm:pt-0 animate-fadeIn">

    {{-- 1. STATISTIC CARDS (Modern SaaS Minimalist Cards) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        
        {{-- Card 1: Total Pegawai --}}
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-none flex items-center justify-between group hover:-translate-y-1 transition-all duration-300">
            <div>
                <p class="text-[10px] text-slate-400 dark:text-slate-400 uppercase font-black tracking-widest mb-1">Total Pegawai</p>
                <h3 class="text-3xl font-black text-slate-800 dark:text-white tracking-tighter">{{ $stats['total'] }}</h3>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-500/15 border border-transparent dark:border-indigo-500/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300 shadow-sm dark:shadow-none">
                <i class="fas fa-users"></i>
            </div>
        </div>

        {{-- Card 2: Pegawai Aktif --}}
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-none flex items-center justify-between group hover:-translate-y-1 transition-all duration-300">
            <div>
                <p class="text-[10px] text-slate-400 dark:text-slate-400 uppercase font-black tracking-widest mb-1">Pegawai Aktif</p>
                <h3 class="text-3xl font-black text-emerald-600 dark:text-emerald-400 tracking-tighter">{{ $stats['active'] }}</h3>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-500/15 border border-transparent dark:border-emerald-500/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl group-hover:scale-110 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300 shadow-sm dark:shadow-none">
                <i class="fas fa-user-check"></i>
            </div>
        </div>

        {{-- Card 3: Sudah Resign --}}
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-none flex items-center justify-between group hover:-translate-y-1 transition-all duration-300">
            <div>
                <p class="text-[10px] text-slate-400 dark:text-slate-400 uppercase font-black tracking-widest mb-1">Sudah Resign</p>
                <h3 class="text-3xl font-black text-rose-500 dark:text-rose-400 tracking-tighter">{{ $stats['resigned'] }}</h3>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-rose-50 dark:bg-rose-500/15 border border-transparent dark:border-rose-500/30 text-rose-500 dark:text-rose-400 flex items-center justify-center text-xl group-hover:scale-110 group-hover:bg-rose-500 group-hover:text-white transition-all duration-300 shadow-sm dark:shadow-none">
                <i class="fas fa-user-times"></i>
            </div>
        </div>

        {{-- Card 4: Baru (Bln Ini) --}}
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-none flex items-center justify-between group hover:-translate-y-1 transition-all duration-300">
            <div>
                <p class="text-[10px] text-slate-400 dark:text-slate-400 uppercase font-black tracking-widest mb-1">Baru (Bln Ini)</p>
                <h3 class="text-3xl font-black text-blue-600 dark:text-blue-400 tracking-tighter">+{{ $stats['new'] }}</h3>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-500/15 border border-transparent dark:border-blue-500/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl group-hover:scale-110 group-hover:bg-blue-500 group-hover:text-white transition-all duration-300 shadow-sm dark:shadow-none">
                <i class="fas fa-user-plus"></i>
            </div>
        </div>

    </div>

    {{-- 2. MAIN TABLE AREA --}}
    <div class="space-y-4">
        
        {{-- TOOLBAR & FILTER SECTION --}}
        <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-none flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 relative z-30">
            
            {{-- Toggle Aktif / Resign --}}
            <div class="flex bg-slate-50 dark:bg-slate-950 p-1.5 rounded-2xl border border-slate-100 dark:border-slate-800 w-full lg:w-auto">
                <a href="{{ route('admin.master.user.index', ['status' => 'active', 'branch_id' => request('branch_id'), 'employee_status' => request('employee_status')]) }}" 
                    class="flex-1 lg:flex-none px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.15em] transition-all flex items-center justify-center gap-2 {{ request('status') != 'inactive' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm dark:shadow-none border border-slate-200 dark:border-slate-700' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300' }}">
                    <i class="fas fa-toggle-on text-[10px]"></i> AKTIF
                </a>
                <a href="{{ route('admin.master.user.index', ['status' => 'inactive', 'branch_id' => request('branch_id'), 'employee_status' => request('employee_status')]) }}" 
                    class="flex-1 lg:flex-none px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.15em] transition-all flex items-center justify-center gap-2 {{ request('status') == 'inactive' ? 'bg-white dark:bg-slate-800 text-rose-600 dark:text-rose-400 shadow-sm dark:shadow-none border border-slate-200 dark:border-slate-700' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300' }}">
                    <i class="fas fa-door-open text-[10px]"></i> RESIGN
                </a>
            </div>

            {{-- Filter & Actions Toolbar --}}
            @php
                // [BARU] Label aktif untuk Custom Dropdown Anti-Bocor
                $statusLabels = [
                    '' => 'SEMUA JENIS PEGAWAI',
                    'Permanent' => 'KARYAWAN TETAP',
                    'Contract' => 'KONTRAK',
                    'Probation' => 'PROBATION',
                ];
                $expiryLabels = [
                    '' => 'STATUS JATUH TEMPO',
                    'expired' => 'SUDAH HABIS',
                    'urgent' => 'HABIS < 30 HARI',
                    'safe' => 'MASA AKTIF AMAN',
                ];
                $selectedBranchObj = $branches->firstWhere('id', request('branch_id'));
                $selectedBranchLabel = $selectedBranchObj ? strtoupper($selectedBranchObj->name) : 'SEMUA CABANG';
                $selectedStatusLabel = $statusLabels[request('employee_status', '')] ?? 'SEMUA JENIS PEGAWAI';
                $selectedExpiryLabel = $expiryLabels[request('contract_expiry', '')] ?? 'STATUS JATUH TEMPO';
            @endphp

            <div class="flex flex-col sm:flex-row flex-wrap lg:flex-nowrap gap-3 w-full lg:w-auto lg:justify-end items-center">
                <form action="{{ route('admin.master.user.index') }}" method="GET" id="monitoringFilterForm" class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto flex-wrap lg:flex-nowrap items-center">
                    <input type="hidden" name="status" value="{{ request('status', 'active') }}">
                    
                    {{-- [BARU] CUSTOM DROPDOWN JATUH TEMPO KONTRAK (ANTI-BOCOR DARK/LIGHT MODE) --}}
                    <div class="relative w-full sm:w-auto {{ request('employee_status') == 'Contract' ? '' : 'hidden' }}" id="customExpiryWrapper">
                        <button type="button" onclick="toggleMonitoringDropdown('expiry')"
                            class="w-full sm:w-auto bg-amber-50/80 dark:bg-amber-500/15 border border-amber-200 dark:border-amber-500/40 rounded-2xl text-[10px] font-black text-amber-700 dark:text-amber-300 px-4 py-3 outline-none transition uppercase shadow-sm dark:shadow-none cursor-pointer flex items-center justify-between gap-2.5">
                            <span>{{ $selectedExpiryLabel }}</span>
                            <i id="expiryDropdownIcon" class="fas fa-chevron-down text-[9px] transition-transform duration-200"></i>
                        </button>
                        <div id="expiryDropdownMenu"
                            class="hidden absolute left-0 mt-2 w-52 rounded-2xl bg-white dark:bg-[#13151d] border border-slate-200 dark:border-slate-800 shadow-2xl shadow-slate-900/15 dark:shadow-black/80 py-1.5 z-50">
                            @foreach($expiryLabels as $expVal => $expText)
                                <button type="button" onclick="applyMonitoringFilter('filter_expiry', '{{ $expVal }}')"
                                    class="w-full px-4 py-2 text-left text-[10px] uppercase tracking-wider flex items-center justify-between transition-colors cursor-pointer {{ request('contract_expiry', '') === (string)$expVal ? 'bg-amber-500 text-white font-black' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/90 font-bold' }}">
                                    <span>{{ $expText }}</span>
                                    @if(request('contract_expiry', '') === (string)$expVal)
                                        <i class="fas fa-check text-[9px]"></i>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- SELECT ASLI JATUH TEMPO (Tetap Dipertahankan & Tersinkronisasi) --}}
                    <select name="contract_expiry" onchange="this.form.submit()" 
                        class="sr-only dark:[color-scheme:dark] bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-100" id="filter_expiry">
                        <option value="" class="bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-100">Status Jatuh Tempo</option>
                        <option value="expired" class="bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-100" {{ request('contract_expiry') == 'expired' ? 'selected' : '' }}>Sudah Habis</option>
                        <option value="urgent" class="bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-100" {{ request('contract_expiry') == 'urgent' ? 'selected' : '' }}>Habis &lt; 30 Hari</option>
                        <option value="safe" class="bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-100" {{ request('contract_expiry') == 'safe' ? 'selected' : '' }}>Masa Aktif Aman</option>
                    </select>

                    {{-- [BARU] CUSTOM DROPDOWN JENIS PEGAWAI (ANTI-BOCOR DARK/LIGHT MODE) --}}
                    <div class="relative w-full sm:w-auto" id="customStatusWrapper">
                        <button type="button" onclick="toggleMonitoringDropdown('status')"
                            class="w-full sm:w-auto bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-indigo-400 dark:hover:border-indigo-500/60 rounded-2xl text-[10px] font-bold text-slate-600 dark:text-slate-200 px-4 py-3 outline-none transition uppercase shadow-sm dark:shadow-none cursor-pointer flex items-center justify-between gap-3">
                            <span>{{ $selectedStatusLabel }}</span>
                            <i id="statusDropdownIcon" class="fas fa-chevron-down text-[9px] text-slate-400 transition-transform duration-200"></i>
                        </button>
                        <div id="statusDropdownMenu"
                            class="hidden absolute left-0 mt-2 w-56 rounded-2xl bg-white dark:bg-[#13151d] border border-slate-200 dark:border-slate-800 shadow-2xl shadow-slate-900/15 dark:shadow-black/80 py-1.5 z-50">
                            @foreach($statusLabels as $stVal => $stText)
                                <button type="button" onclick="applyMonitoringFilter('filter_status', '{{ $stVal }}', true)"
                                    class="w-full px-4 py-2 text-left text-[10px] uppercase tracking-wider flex items-center justify-between transition-colors cursor-pointer {{ request('employee_status', '') === (string)$stVal ? 'bg-indigo-600 text-white font-black' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/90 font-bold' }}">
                                    <span>{{ $stText }}</span>
                                    @if(request('employee_status', '') === (string)$stVal)
                                        <i class="fas fa-check text-[9px]"></i>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- SELECT ASLI JENIS PEGAWAI (Tetap Dipertahankan & Tersinkronisasi) --}}
                    <select name="employee_status" id="filter_status" onchange="toggleExpiryFilter(); this.form.submit()" 
                        class="sr-only dark:[color-scheme:dark] bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-100">
                        <option value="" class="bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-100">SEMUA JENIS PEGAWAI</option>
                        <option value="Permanent" class="bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-100" {{ request('employee_status') == 'Permanent' ? 'selected' : '' }}>KARYAWAN TETAP</option>
                        <option value="Contract" class="bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-100" {{ request('employee_status') == 'Contract' ? 'selected' : '' }}>KONTRAK</option>
                        <option value="Probation" class="bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-100" {{ request('employee_status') == 'Probation' ? 'selected' : '' }}>PROBATION</option>
                    </select>

                    {{-- [BARU] CUSTOM DROPDOWN CABANG (ANTI-BOCOR DARK/LIGHT MODE) --}}
                    <div class="relative w-full sm:w-auto" id="customBranchWrapper">
                        <button type="button" onclick="toggleMonitoringDropdown('branch')"
                            class="w-full sm:w-auto bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-indigo-400 dark:hover:border-indigo-500/60 rounded-2xl text-[10px] font-bold text-slate-600 dark:text-slate-200 px-4 py-3 outline-none transition uppercase shadow-sm dark:shadow-none cursor-pointer flex items-center justify-between gap-3">
                            <span class="truncate max-w-[180px]">{{ $selectedBranchLabel }}</span>
                            <i id="branchDropdownIcon" class="fas fa-chevron-down text-[9px] text-slate-400 transition-transform duration-200"></i>
                        </button>
                        <div id="branchDropdownMenu"
                            class="hidden absolute left-0 mt-2 w-64 max-h-64 overflow-y-auto monitoring-scrollbar rounded-2xl bg-white dark:bg-[#13151d] border border-slate-200 dark:border-slate-800 shadow-2xl shadow-slate-900/15 dark:shadow-black/80 py-1.5 z-50">
                            <button type="button" onclick="applyMonitoringFilter('filter_branch', '')"
                                class="w-full px-4 py-2 text-left text-[10px] uppercase tracking-wider flex items-center justify-between transition-colors cursor-pointer {{ !request('branch_id') ? 'bg-indigo-600 text-white font-black' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/90 font-bold' }}">
                                <span>SEMUA CABANG</span>
                                @if(!request('branch_id'))
                                    <i class="fas fa-check text-[9px]"></i>
                                @endif
                            </button>
                            @foreach($branches as $branch)
                                <button type="button" onclick="applyMonitoringFilter('filter_branch', '{{ $branch->id }}')"
                                    class="w-full px-4 py-2 text-left text-[10px] uppercase tracking-wider flex items-center justify-between transition-colors cursor-pointer {{ (string)request('branch_id') === (string)$branch->id ? 'bg-indigo-600 text-white font-black' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/90 font-bold' }}">
                                    <span class="truncate pr-2">{{ $branch->name }}</span>
                                    @if((string)request('branch_id') === (string)$branch->id)
                                        <i class="fas fa-check text-[9px] shrink-0"></i>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- SELECT ASLI CABANG (Tetap Dipertahankan & Tersinkronisasi) --}}
                    <select name="branch_id" id="filter_branch" onchange="this.form.submit()" 
                        class="sr-only dark:[color-scheme:dark] bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-100">
                        <option value="" class="bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-100">SEMUA CABANG</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" class="bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-100" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>

                    <div class="relative group w-full sm:w-64">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs transition-colors group-focus-within:text-indigo-500"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama, NIK..." 
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl text-[11px] font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500/60 focus:bg-white dark:focus:bg-slate-900 outline-none transition shadow-sm dark:shadow-none placeholder:text-slate-400 dark:placeholder:text-slate-500">
                    </div>

                    {{-- [BARU] Tombol Reset Cepat jika ada filter aktif --}}
                    @if(request('employee_status') || request('branch_id') || request('contract_expiry') || request('search'))
                        <a href="{{ route('admin.master.user.index', ['status' => request('status', 'active')]) }}"
                           title="Reset Semua Filter"
                           class="w-10 h-10 flex items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-300 hover:bg-rose-500 hover:text-white transition shrink-0">
                            <i class="fas fa-times text-xs"></i>
                        </a>
                    @endif
                </form>

                <div class="flex items-center gap-2.5 w-full sm:w-auto mt-2 sm:mt-0">
                    {{-- DROPDOWN EXPORT --}}
                    <div class="relative group" id="exportDropdown">
                        <button type="button" onclick="document.getElementById('exportMenu').classList.toggle('hidden')" class="w-11 h-11 flex items-center justify-center rounded-2xl bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30 hover:bg-emerald-500 hover:text-white transition shadow-sm dark:shadow-none cursor-pointer" title="Export & Import">
                            <i class="fas fa-file-export text-xs"></i>
                        </button>
                        <div id="exportMenu" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-[#13151d] rounded-2xl shadow-xl dark:shadow-black/80 border border-slate-100 dark:border-slate-800 z-50 overflow-hidden">
                            <a href="{{ route('admin.master.user.report-pdf') }}" class="flex items-center gap-3 px-5 py-3.5 text-[10px] font-black uppercase text-slate-600 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/90 transition">
                                <i class="fas fa-file-pdf text-rose-500"></i> Laporan Kontrak
                            </a>
                            <a href="{{ route('admin.master.user.import') }}" class="flex items-center gap-3 px-5 py-3.5 text-[10px] font-black uppercase text-slate-600 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/90 transition border-t border-slate-50 dark:border-slate-800">
                                <i class="fas fa-file-csv text-emerald-500"></i> Import Data CSV
                            </a>
                        </div>
                    </div>
                    
                    <a href="{{ route('admin.master.user.create') }}" class="flex-1 sm:flex-none bg-slate-900 dark:bg-indigo-600 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 dark:hover:bg-indigo-500 transition shadow-lg shadow-slate-200 dark:shadow-indigo-950/50 flex items-center justify-center gap-2 whitespace-nowrap active:scale-95">
                        <i class="fas fa-plus text-[10px]"></i> Karyawan Baru
                    </a>
                </div>
            </div>
        </div>

        {{-- TABLE CONTENT (REVISED FOR NO SCROLLING) --}}
        <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-800 overflow-hidden relative">
            
            {{-- REMOVED: overflow-x-auto & whitespace-nowrap --}}
            <div class="w-full">
                <table class="w-full text-left border-collapse table-auto break-words">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-950/60 border-b border-slate-100 dark:border-slate-800 text-slate-400 dark:text-slate-400">
                            {{-- DIKURANGI PADDING px-6 MENJADI px-3/px-4 AGAR LEBIH RAPAT --}}
                            <th class="px-4 py-4 text-[9px] font-black uppercase tracking-widest w-1/4">Profil Karyawan</th>
                            <th class="px-3 py-4 text-[9px] font-black uppercase tracking-widest w-1/5">Unit Kerja</th>
                            <th class="px-3 py-4 text-[9px] font-black uppercase tracking-widest">Pendidikan</th>
                            <th class="px-3 py-4 text-[9px] font-black uppercase tracking-widest">Jabatan</th>
                            <th class="px-3 py-4 text-[9px] font-black uppercase tracking-widest text-center">Status</th>
                            <th class="px-3 py-4 text-[9px] font-black uppercase tracking-widest min-w-[90px]">Masa Kerja</th>
                            <th class="px-4 py-4 text-[9px] font-black uppercase tracking-widest text-right min-w-[150px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/70">
                        @forelse($users as $user)
                        
                        {{-- SUPER BYPASS LOGIC: Membaca aman data dari database --}}
                        @php
                            $branchName = 'UNIT TIDAK TERDAFTAR';
                            $branchLoc  = 'N/A';
                            $hasBranch  = false;

                            if ($user->branch_id) {
                                $branchDb = \Illuminate\Support\Facades\DB::table('branches')->where('id', $user->branch_id)->first();
                                if ($branchDb) {
                                    $branchName = $branchDb->name ?? $branchDb->nama_cabang ?? $branchDb->nama ?? $branchDb->company_name ?? 'UNIT TERDAFTAR';
                                    $branchLoc  = $branchDb->location ?? $branchDb->alamat ?? $branchDb->city ?? 'N/A';
                                    $hasBranch  = true;
                                }
                            }
                            
                            $assetCount = optional($user->masterAssets)->count() + optional($user->assets)->count();
                        @endphp

                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition duration-200 group">
                            
                            {{-- Profil --}}
                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="relative flex-shrink-0">
                                        @if($user->avatar)
                                            <img src="{{ asset('storage/' . $user->avatar) }}" class="w-10 h-10 rounded-xl object-cover shadow-sm border border-slate-200 dark:border-slate-700">
                                        @else
                                            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center font-black text-sm text-slate-500 dark:text-slate-300 border border-slate-200 dark:border-slate-700 shadow-sm dark:shadow-none">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div class="absolute -bottom-1 -right-1 w-3 h-3 {{ $user->is_active ? 'bg-emerald-500' : 'bg-rose-500' }} border-2 border-white dark:border-slate-900 rounded-full shadow-sm"></div>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-black text-slate-800 dark:text-white text-[11px] uppercase tracking-tight group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors truncate w-full" title="{{ $user->name }}">{{ $user->name }}</h3>
                                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-1">
                                            <span class="text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/15 px-1.5 py-0.5 rounded border border-indigo-100 dark:border-indigo-500/30">ID: {{ $user->nik }}</span>
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Unit Kerja (CABANG) --}}
                            <td class="px-3 py-3 align-middle">
                                <div class="flex flex-col">
                                    <div class="flex items-start gap-1.5 mb-0.5">
                                        <div class="w-1.5 h-1.5 mt-1 rounded-full {{ $hasBranch ? 'bg-indigo-500' : 'bg-rose-400' }} flex-shrink-0"></div>
                                        <span class="text-[10px] font-black leading-tight {{ $hasBranch ? 'text-slate-800 dark:text-slate-100' : 'text-rose-500 dark:text-rose-400' }} uppercase tracking-tight line-clamp-2">
                                            {{ strtoupper($branchName) }}
                                        </span>
                                    </div>
                                    <span class="text-[8px] text-slate-400 dark:text-slate-400 font-bold uppercase tracking-wider ml-3">
                                        LOC: {{ strtoupper($branchLoc) }}
                                    </span>
                                    
                                    @if($assetCount > 0)
                                        <div class="mt-1 ml-3 inline-flex items-center">
                                            <span class="px-1.5 py-0.5 bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 rounded border border-emerald-100 dark:border-emerald-500/30 text-[8px] font-black uppercase tracking-widest flex items-center gap-1">
                                                <i class="fas fa-laptop text-[8px]"></i> {{ $assetCount }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            {{-- Pendidikan & Jurusan --}}
                            <td class="px-3 py-3 align-middle">
                                <div class="flex flex-col gap-0.5">
                                    <span class="w-fit text-[8px] font-black text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/15 px-1.5 py-0.5 rounded border border-indigo-100 dark:border-indigo-500/30 uppercase tracking-widest">
                                        {{ $user->last_education ?? 'SMA' }}
                                    </span>
                                    <p class="text-[10px] font-black text-slate-700 dark:text-slate-200 italic uppercase leading-tight line-clamp-2" title="{{ $user->educational_major ?? 'UMUM' }}">
                                        {{ $user->educational_major ?? 'UMUM' }}
                                    </p>
                                    <p class="text-[8px] text-slate-400 dark:text-slate-400 font-bold uppercase tracking-tighter">
                                        {{ $user->age ?? '0' }} Thn <span class="opacity-30">|</span> {{ $user->sex ?? 'L' }}
                                    </p>
                                </div>
                            </td>

                            {{-- Jabatan & Dept --}}
                            <td class="px-3 py-3 align-middle">
                                <p class="text-[10px] font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight mb-0.5 leading-tight line-clamp-2" title="{{ strtoupper($user->position->name ?? 'STAFF') }}">{{ strtoupper($user->position->name ?? 'STAFF') }}</p>
                                <p class="text-[8px] text-slate-400 dark:text-slate-400 font-bold uppercase tracking-widest line-clamp-1" title="{{ strtoupper($user->department->name ?? 'UMUM') }}">{{ strtoupper($user->department->name ?? 'UMUM') }}</p>
                            </td>

                            {{-- STATUS KONTRAK --}}
                            <td class="px-3 py-3 text-center align-middle">
                                <div class="flex flex-col items-center gap-1">
                                    @if(strtoupper($user->employee_status) === 'PERMANENT')
                                        <span class="inline-block px-2 py-0.5 rounded-lg border bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-500/30 text-[8px] font-black uppercase tracking-widest shadow-sm dark:shadow-none">
                                            Permanent
                                        </span>
                                    @elseif(strtoupper($user->employee_status) === 'CONTRACT')
                                        <span class="inline-block px-2 py-0.5 rounded-lg border bg-amber-50 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 border-amber-100 dark:border-amber-500/30 text-[8px] font-black uppercase tracking-widest shadow-sm dark:shadow-none">
                                            Contract
                                        </span>

                                        @if($user->contract_end_date)
                                            @php
                                                $endDate = \Carbon\Carbon::parse($user->contract_end_date)->startOfDay();
                                                $today = \Carbon\Carbon::today();
                                                $diffDays = $today->diffInDays($endDate, false); 
                                            @endphp

                                            @if($diffDays < 0)
                                                <span class="text-[7.5px] font-black text-white bg-rose-500 px-1.5 py-0.5 rounded-md uppercase animate-pulse">
                                                    Habis
                                                </span>
                                            @elseif($diffDays <= 30)
                                                <span class="text-[7.5px] font-black text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/15 px-1.5 py-0.5 rounded-md border border-rose-100 dark:border-rose-500/30 uppercase italic">
                                                    Sisa {{ round($diffDays) }} H
                                                </span>
                                            @endif
                                        @endif

                                    @elseif(strtoupper($user->employee_status) === 'PROBATION')
                                        <span class="inline-block px-2 py-0.5 rounded-lg border bg-blue-50 dark:bg-blue-500/15 text-blue-600 dark:text-blue-400 border-blue-100 dark:border-blue-500/30 text-[8px] font-black uppercase tracking-widest shadow-sm dark:shadow-none">
                                            Probation
                                        </span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded-lg border bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700 text-[8px] font-black uppercase tracking-widest shadow-sm dark:shadow-none">
                                            -
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Masa Kerja --}}
                            <td class="px-3 py-3 align-middle min-w-[90px]">
                                <div class="space-y-0.5">
                                    <p class="text-[10px] text-slate-700 dark:text-slate-200 font-black uppercase tracking-tight">{{ $user->join_date ? $user->join_date->format('d/m/Y') : '-' }}</p>
                                    <div class="flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                        <p class="text-[9px] text-indigo-600 dark:text-indigo-400 font-black italic uppercase tracking-tighter">{{ $user->service_period ?? '0 Hari' }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Aksi / Manajemen --}}
                            <td class="px-4 py-3 text-right align-middle">
                                <div class="flex justify-end gap-1 flex-wrap">
                                    {{-- Set Jatah Cuti --}}
                                    <button type="button" onclick="openQuotaModal({{ $user->id }}, '{{ addslashes($user->name) }}', {{ optional($user->leaveAllowance)->quota ?? 0 }})" 
                                        class="w-7 h-7 flex items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/30 hover:bg-emerald-500 hover:text-white transition shadow-sm dark:shadow-none cursor-pointer" 
                                        title="Set Jatah Cuti">
                                        <i class="fas fa-calendar-check text-[9px]"></i>
                                    </button>

                                    {{-- Cetak ID Card --}}
                                    <a href="{{ route('admin.master.user.preview-card', $user->id) }}" 
                                        class="w-7 h-7 flex items-center justify-center rounded-lg bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/30 hover:bg-indigo-600 hover:text-white transition shadow-sm dark:shadow-none" 
                                        title="Cetak ID Card">
                                        <i class="fas fa-id-card text-[9px]"></i>
                                    </a>

                                    {{-- Lihat Profil --}}
                                    <a href="{{ route('admin.master.user.show', $user->id) }}" 
                                        class="w-7 h-7 flex items-center justify-center rounded-lg bg-slate-800 dark:bg-slate-800 text-white dark:text-slate-200 border border-transparent dark:border-slate-700 hover:bg-slate-700 dark:hover:bg-slate-700 transition shadow-sm dark:shadow-none" 
                                        title="Lihat Profil">
                                        <i class="fas fa-eye text-[9px]"></i>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.master.user.edit', $user->id) }}" 
                                       class="w-7 h-7 flex items-center justify-center rounded-lg bg-white dark:bg-amber-500/15 border border-slate-200 dark:border-amber-500/30 text-amber-500 dark:text-amber-400 hover:bg-amber-500 hover:text-white transition shadow-sm dark:shadow-none" 
                                       title="Edit Data">
                                        <i class="fas fa-pencil-alt text-[9px]"></i>
                                    </a>

                                    {{-- Hapus --}}
                                    <form action="{{ route('admin.master.user.destroy', $user->id) }}" method="POST" class="inline delete-form">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn-delete w-7 h-7 flex items-center justify-center rounded-lg bg-white dark:bg-rose-500/15 border border-slate-200 dark:border-rose-500/30 text-rose-500 dark:text-rose-400 hover:bg-rose-500 hover:text-white transition shadow-sm dark:shadow-none cursor-pointer" title="Hapus">
                                            <i class="fas fa-trash-alt text-[9px]"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-20 text-center">
                                <div class="flex flex-col items-center opacity-40">
                                    <i class="fas fa-users-slash text-5xl mb-3 text-slate-400 dark:text-slate-600"></i>
                                    <h4 class="font-black text-slate-500 dark:text-slate-400 text-sm tracking-tight uppercase">Data Karyawan Tidak Ditemukan</h4>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-8 py-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-950/40">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- MODAL PENGATURAN KUOTA CUTI --}}
<div id="quotaModal" class="fixed inset-0 z-[9999] hidden">
    <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="closeQuotaModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <form action="{{ route('admin.leave-allowance.update') }}" method="POST" class="bg-white dark:bg-slate-900 border border-transparent dark:border-slate-800 w-full max-w-sm rounded-[2rem] p-8 shadow-2xl relative overflow-hidden mx-4">
            @csrf
            <div class="absolute top-0 inset-x-0 h-2 bg-emerald-500"></div>
            <input type="hidden" name="user_id" id="modal_user_id">
            
            <h3 class="text-lg font-black text-slate-800 dark:text-white mb-1 tracking-tight">Kuota Cuti Tahunan</h3>
            <p id="modal_user_name" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-6"></p>
            
            <div class="space-y-4 mb-8">
                <div>
                    <label class="text-[9px] font-black text-slate-400 dark:text-slate-400 uppercase mb-2 block tracking-widest">Tahun Anggaran</label>
                    <input type="text" value="{{ date('Y') }}" readonly class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl py-3 px-4 text-xs font-black text-slate-400 dark:text-slate-400 outline-none cursor-not-allowed">
                </div>
                <div>
                    <label class="text-[9px] font-black text-slate-700 dark:text-slate-300 uppercase mb-2 block tracking-widest">Jumlah Hari Jatah</label>
                    <input type="number" name="quota" id="modal_quota" required min="0" max="30" 
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl py-3.5 px-4 text-xl font-black text-slate-800 dark:text-white outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition shadow-inner dark:shadow-none" placeholder="0">
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeQuotaModal()" class="flex-1 py-3.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-[10px] font-black uppercase text-slate-500 dark:text-slate-300 tracking-widest hover:bg-slate-50 dark:hover:bg-slate-700 transition shadow-sm dark:shadow-none cursor-pointer">Batal</button>
                <button type="submit" class="flex-1 bg-emerald-600 text-white py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-emerald-100 dark:shadow-none hover:bg-emerald-700 transition transform active:scale-95 cursor-pointer">
                    Simpan Data
                </button>
            </div>
        </form>
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

    .monitoring-scrollbar::-webkit-scrollbar { width: 6px; }
    .monitoring-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .monitoring-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
    .dark .monitoring-scrollbar::-webkit-scrollbar-thumb,
    html.dark .monitoring-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // [BARU] Logika Custom Dropdown Filter Anti-Bocor Warna Putih
    function toggleMonitoringDropdown(type) {
        const menus = {
            expiry: document.getElementById('expiryDropdownMenu'),
            status: document.getElementById('statusDropdownMenu'),
            branch: document.getElementById('branchDropdownMenu'),
        };
        const icons = {
            expiry: document.getElementById('expiryDropdownIcon'),
            status: document.getElementById('statusDropdownIcon'),
            branch: document.getElementById('branchDropdownIcon'),
        };

        Object.keys(menus).forEach(key => {
            if (key !== type && menus[key]) {
                menus[key].classList.add('hidden');
                icons[key]?.classList.remove('rotate-180');
            }
        });

        if (menus[type]) {
            const isHidden = menus[type].classList.contains('hidden');
            menus[type].classList.toggle('hidden', !isHidden);
            icons[type]?.classList.toggle('rotate-180', isHidden);
        }
    }

    function applyMonitoringFilter(selectId, value, isStatusFilter = false) {
        const selectEl = document.getElementById(selectId);
        if (selectEl) {
            selectEl.value = value;
            if (isStatusFilter) {
                toggleExpiryFilter();
            }
            document.getElementById('monitoringFilterForm').submit();
        }
    }

    function openQuotaModal(id, name, currentQuota) {
        document.getElementById('modal_user_id').value = id;
        document.getElementById('modal_user_name').innerText = name;
        document.getElementById('modal_quota').value = currentQuota;
        document.getElementById('quotaModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden'; 
    }

    function closeQuotaModal() {
        document.getElementById('quotaModal').classList.add('hidden');
        document.body.style.overflow = 'auto'; 
    }

    function toggleExpiryFilter() {
        const status = document.getElementById('filter_status').value;
        const expiryFilter = document.getElementById('filter_expiry');
        const customExpiryWrapper = document.getElementById('customExpiryWrapper');
        if (status === 'Contract') {
            expiryFilter.classList.remove('hidden');
            if (customExpiryWrapper) customExpiryWrapper.classList.remove('hidden');
        } else {
            expiryFilter.classList.add('hidden');
            if (customExpiryWrapper) customExpiryWrapper.classList.add('hidden');
        }
    }

    window.addEventListener('click', function(e) {
        const dropdown = document.getElementById('exportDropdown');
        if (dropdown && !dropdown.contains(e.target)) {
            document.getElementById('exportMenu').classList.add('hidden');
        }

        // [BARU] Tutup custom dropdown filter jika klik di luar area
        const wrappers = [
            { wrap: 'customExpiryWrapper', menu: 'expiryDropdownMenu', icon: 'expiryDropdownIcon' },
            { wrap: 'customStatusWrapper', menu: 'statusDropdownMenu', icon: 'statusDropdownIcon' },
            { wrap: 'customBranchWrapper', menu: 'branchDropdownMenu', icon: 'branchDropdownIcon' },
        ];
        wrappers.forEach(item => {
            const wEl = document.getElementById(item.wrap);
            if (wEl && !wEl.contains(e.target)) {
                document.getElementById(item.menu)?.classList.add('hidden');
                document.getElementById(item.icon)?.classList.remove('rotate-180');
            }
        });
    });

    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const isDark = document.documentElement.classList.contains('dark') || document.body.classList.contains('dark');
            Swal.fire({
                title: 'Hapus Karyawan?',
                text: "Seluruh riwayat absensi, payroll, dan pengajuan izin akan ikut terhapus secara permanen.",
                icon: 'warning',
                background: isDark ? '#0f172a' : '#ffffff',
                color: isDark ? '#f1f5f9' : '#1e293b',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                borderRadius: '1rem'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.closest('form').submit();
                }
            });
        });
    });
</script>
@endsection