@extends('admin.layouts.master')
@section('title', 'Profil Karyawan - ' . $user->name)

@section('content')
<div class="max-w-7xl mx-auto pb-24 px-4 sm:px-6 animate-fadeIn">
    
    {{-- ========================================== --}}
    {{-- BREADCRUMB NAV --}}
    {{-- ========================================== --}}
    <nav class="flex mb-8 no-print" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest text-slate-400 bg-white px-5 py-2.5 rounded-2xl shadow-sm border border-slate-100">
            <li><a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 transition flex items-center gap-1.5"><i class="fas fa-home text-slate-300"></i> Home</a></li>
            <li><i class="fas fa-chevron-right text-[8px] text-slate-300"></i></li>
            <li><a href="{{ route('admin.master.user.index') }}" class="hover:text-indigo-600 transition">Data Karyawan</a></li>
            <li><i class="fas fa-chevron-right text-[8px] text-slate-300"></i></li>
            <li class="text-indigo-600">Detail Profil</li>
        </ol>
    </nav>

    {{-- ========================================== --}}
    {{-- HERO SECTION (DARK MODE CARD) --}}
    {{-- ========================================== --}}
    <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 rounded-[2.5rem] p-8 md:p-12 mb-8 text-white shadow-2xl shadow-indigo-900/20 relative overflow-hidden group">
        {{-- Background Ornament --}}
        <div class="absolute right-0 top-0 opacity-5 translate-x-12 -translate-y-12 pointer-events-none transition-transform duration-700 group-hover:rotate-12 group-hover:scale-110">
            <i class="fas fa-id-badge text-[20rem]"></i>
        </div>
        <div class="absolute left-0 bottom-0 w-64 h-64 bg-indigo-500 rounded-full blur-[100px] opacity-20 pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-10">
            {{-- Foto Avatar --}}
            <div class="relative shrink-0">
                <div class="absolute inset-0 bg-indigo-500 rounded-[2.5rem] blur-xl opacity-50"></div>
                <img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : asset('assets/img/default-avatar.png') }}" 
                     class="relative w-36 h-36 md:w-44 md:h-44 rounded-[2rem] object-cover border-[6px] border-white/10 shadow-2xl hover:scale-105 transition-transform duration-500 ease-out bg-slate-800">
                
                {{-- Indikator Status (Aktif / Non-Aktif) --}}
                <div class="absolute -bottom-3 -right-3 w-12 h-12 {{ $user->is_active ? 'bg-emerald-500' : 'bg-rose-500' }} rounded-2xl flex items-center justify-center border-4 border-slate-900 shadow-xl text-white backdrop-blur-md" title="{{ $user->is_active ? 'Status Karyawan Aktif' : 'Status Karyawan Non-Aktif' }}">
                    <i class="fas {{ $user->is_active ? 'fa-check' : 'fa-times' }} text-sm"></i>
                </div>
            </div>
            
            {{-- Identitas Utama --}}
            <div class="text-center md:text-left flex-1 w-full">
                <div class="flex flex-wrap justify-center md:justify-start items-center gap-3 mb-3">
                    <span class="px-4 py-1.5 bg-white/10 backdrop-blur-md text-indigo-200 rounded-xl text-[10px] font-black uppercase tracking-[0.15em] border border-white/10 shadow-inner">
                        NIK: {{ $user->nik ?? 'TIDAK ADA NIK' }}
                    </span>
                    <span class="px-4 py-1.5 bg-indigo-500/80 backdrop-blur-md text-white rounded-xl text-[10px] font-black uppercase tracking-widest border border-indigo-400/50 shadow-lg shadow-indigo-500/30">
                        {{ $user->position->name ?? 'Staff' }}
                    </span>
                </div>
                
                <h1 class="text-3xl md:text-5xl font-black tracking-tighter uppercase text-white mb-4 drop-shadow-md leading-none">{{ $user->name }}</h1>
                
                {{-- Badge Cabang / Unit Kerja --}}
                <div class="inline-flex items-center gap-3 px-5 py-2.5 rounded-2xl backdrop-blur-md border {{ $user->branch_id && $user->branchDetail ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400 shadow-inner shadow-emerald-500/10' : 'bg-rose-500/10 border-rose-500/30 text-rose-400 shadow-inner shadow-rose-500/10' }}">
                    <div class="w-8 h-8 rounded-full {{ $user->branch_id && $user->branchDetail ? 'bg-emerald-500/20' : 'bg-rose-500/20' }} flex items-center justify-center">
                        <i class="fas fa-building text-xs"></i>
                    </div>
                    <div class="flex flex-col text-left">
                        <span class="text-[10px] font-bold uppercase tracking-widest opacity-75 leading-none mb-1">Penempatan Unit</span>
                        <span class="text-xs font-black uppercase tracking-wider leading-none">
                            {{ $user->unit_kerja ?? 'Belum Terdaftar' }}
                            @if($user->branch_id && $user->branchDetail)
                                <span class="text-[9px] ml-1 opacity-75 font-mono">[{{ $user->branchDetail->code ?? '-' }}]</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi Cepat --}}
            <div class="flex md:flex-col gap-4 no-print shrink-0">
                <a href="{{ route('admin.master.user.edit', $user->id) }}" class="w-14 h-14 bg-white/5 hover:bg-amber-500 hover:text-white text-white rounded-2xl flex items-center justify-center border border-white/10 transition-all duration-300 shadow-xl backdrop-blur-sm group/btn" title="Edit Data Karyawan">
                    <i class="fas fa-user-edit text-lg group-hover/btn:scale-110 transition-transform"></i>
                </a>
                
                {{-- [UPDATE TERBARU] Mengubah window.print() menjadi link ke Halaman Cetak Khusus (A4) --}}
                <a href="{{ route('admin.master.user.print', $user->id) }}" target="_blank" class="w-14 h-14 bg-white/5 hover:bg-white hover:text-slate-900 text-white rounded-2xl flex items-center justify-center border border-white/10 transition-all duration-300 shadow-xl backdrop-blur-sm group/btn" title="Cetak Profil Dasar (A4)">
                    <i class="fas fa-print text-lg group-hover/btn:scale-110 transition-transform"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- GRID KONTEN UTAMA --}}
    {{-- ========================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- KOLOM KIRI (2/3 LEBAR) --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- BLOK 1: DATA KEPEGAWAIAN --}}
            <div class="bg-white rounded-[2.5rem] p-8 md:p-10 border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-bl-full -z-10 opacity-50"></div>
                
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-8 flex items-center gap-4">
                    <span class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-inner border border-indigo-100/50 text-lg"><i class="fas fa-briefcase"></i></span>
                    Data Kepegawaian & Penempatan
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50/50 p-6 md:p-8 rounded-[2rem] border border-slate-100">
                    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-1 tracking-widest flex items-center gap-1.5"><i class="fas fa-city text-indigo-400"></i> Unit Kerja / PT</p>
                        @if($user->branch_id && $user->branchDetail)
                            <p class="text-sm font-black text-slate-800 uppercase tracking-tight">{{ $user->unit_kerja }}</p>
                            <p class="text-[9px] text-slate-400 font-bold mt-1 uppercase tracking-widest bg-slate-100 px-2 py-0.5 rounded inline-block">KODE: {{ $user->branchDetail->code ?? '-' }}</p>
                        @else
                            <p class="text-xs font-bold text-rose-500 italic flex items-center gap-1.5"><i class="fas fa-exclamation-triangle"></i> Unit Belum Terdaftar</p>
                        @endif
                    </div>
                    
                    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-1 tracking-widest flex items-center gap-1.5"><i class="fas fa-sitemap text-indigo-400"></i> Departemen</p>
                        <p class="text-sm font-black text-slate-800 uppercase tracking-tight">{{ $user->department->name ?? 'Belum Diatur' }}</p>
                    </div>
                    
                    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-2 tracking-widest flex items-center gap-1.5"><i class="fas fa-file-contract text-indigo-400"></i> Status Karyawan</p>
                        <span class="px-4 py-1.5 bg-indigo-50 text-indigo-700 rounded-xl text-[10px] font-black uppercase tracking-widest border border-indigo-100 inline-block shadow-sm">
                            {{ $user->employee_status ?? 'Contract' }}
                        </span>
                    </div>
                    
                    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-1 tracking-widest flex items-center gap-1.5"><i class="fas fa-calendar-alt text-indigo-400"></i> Tanggal Bergabung</p>
                        <p class="text-sm font-black text-slate-800 tracking-tight">
                            {{ $user->join_date ? \Carbon\Carbon::parse($user->join_date)->translatedFormat('d F Y') : 'Belum Ditentukan' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- BLOK 2: INFORMASI PRIBADI --}}
            <div class="bg-white rounded-[2.5rem] p-8 md:p-10 border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-bl-full -z-10 opacity-50"></div>

                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-8 flex items-center gap-4">
                    <span class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-inner border border-emerald-100/50 text-lg"><i class="fas fa-id-card"></i></span>
                    Informasi Pribadi & Pendidikan
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50/50 p-6 md:p-8 rounded-[2rem] border border-slate-100 mb-6">
                    <div class="space-y-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Email Perusahaan</p>
                        <p class="text-sm font-bold text-slate-700 break-all bg-white p-3 rounded-xl border border-slate-100 shadow-sm flex items-center gap-2">
                            <i class="fas fa-envelope text-slate-400"></i> {{ $user->email ?? '-' }}
                        </p>
                    </div>
                    
                    <div class="space-y-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Nomor HP / WhatsApp</p>
                        <p class="text-sm font-bold text-slate-700 bg-white p-3 rounded-xl border border-slate-100 shadow-sm flex items-center gap-2">
                            <i class="fab fa-whatsapp text-emerald-500 text-lg"></i> {{ $user->phone ?? '-' }}
                        </p>
                    </div>
                    
                    <div class="space-y-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Tempat, Tanggal Lahir</p>
                        <p class="text-sm font-bold text-slate-700 bg-white p-3 rounded-xl border border-slate-100 shadow-sm">
                            {{ $user->birth_place ?? '-' }}, {{ $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->translatedFormat('d F Y') : '-' }}
                        </p>
                    </div>
                    
                    <div class="space-y-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Pendidikan Terakhir</p>
                        <p class="text-sm font-black text-slate-700 uppercase bg-white p-3 rounded-xl border border-slate-100 shadow-sm">
                            {{ $user->last_education ?? $user->education ?? '-' }}
                        </p>
                    </div>
                </div>

                {{-- Banner Jurusan --}}
                <div class="p-6 bg-gradient-to-r from-indigo-50 to-white rounded-[2rem] border border-indigo-100/60 flex items-center gap-5 shadow-sm">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-200 shrink-0 text-xl">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-1">Bidang Jurusan / Keahlian Utama</p>
                        <p class="font-black text-slate-800 uppercase tracking-tight text-base">
                            {{ $user->educational_major ?? 'DATA BELUM DILENGKAPI' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- BLOK 3: ASET & INVENTARIS --}}
            <div class="bg-white rounded-[2.5rem] p-8 md:p-10 border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-amber-50 rounded-bl-full -z-10 opacity-50"></div>

                @php
                    $totalAset = optional($user->masterAssets)->count() + optional($user->assets)->count();
                @endphp

                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-8 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <span class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shadow-inner border border-amber-100/50 text-lg"><i class="fas fa-laptop"></i></span>
                        Aset & Inventaris Perusahaan
                    </div>
                    <span class="text-[10px] font-black bg-amber-100 text-amber-700 px-4 py-1.5 rounded-xl border border-amber-200 tracking-widest shadow-sm">{{ $totalAset }} UNIT</span>
                </h3>
                
                <div class="space-y-4">
                    {{-- MASTER ASSETS --}}
                    @forelse($user->masterAssets ?? [] as $masterAsset)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-5 bg-indigo-50/50 rounded-2xl border border-indigo-100 hover:border-indigo-300 transition-all shadow-sm gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-indigo-600 shadow-sm border border-slate-100 text-lg">
                                    <i class="fas {{ strtolower($masterAsset->category) == 'electronics' ? 'fa-laptop' : 'fa-tools' }}"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-black text-slate-800 uppercase tracking-tight">{{ $masterAsset->name }}</h4>
                                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-1 flex items-center gap-2">
                                        <span class="bg-white px-2 py-0.5 rounded border border-slate-200">S/N: {{ $masterAsset->serial_number ?? '-' }}</span>
                                        <span class="text-indigo-500 bg-indigo-100/50 px-2 py-0.5 rounded">Master Asset</span>
                                    </p>
                                </div>
                            </div>
                            <div class="sm:text-right">
                                <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-100 uppercase tracking-widest inline-flex items-center gap-1.5">
                                    <i class="fas fa-calendar-check text-emerald-400"></i> Sejak {{ $masterAsset->assigned_date ? \Carbon\Carbon::parse($masterAsset->assigned_date)->format('d/m/y') : '-' }}
                                </span>
                            </div>
                        </div>
                    @empty
                    @endforelse

                    {{-- LEGACY ASSETS --}}
                    @forelse($user->assets ?? [] as $asset)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-5 bg-slate-50 rounded-2xl border border-slate-100 hover:border-amber-200 transition-all shadow-sm gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-amber-600 shadow-sm border border-slate-100 text-lg">
                                    <i class="fas {{ $asset->category == 'electronics' ? 'fa-laptop' : 'fa-tools' }}"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-black text-slate-800 uppercase tracking-tight">{{ $asset->asset_name }}</h4>
                                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-1 flex items-center gap-2 flex-wrap">
                                        <span class="bg-white px-2 py-0.5 rounded border border-slate-200">S/N: {{ $asset->asset_serial_number }}</span>
                                        <span class="bg-white px-2 py-0.5 rounded border border-slate-200 text-slate-400"><i class="fas fa-tag"></i> {{ $asset->asset_brand }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="sm:text-right">
                                <span class="text-[9px] font-black text-slate-500 bg-white px-3 py-1.5 rounded-xl border border-slate-200 uppercase tracking-widest shadow-sm inline-flex items-center gap-1.5">
                                    <i class="fas fa-calendar-check text-slate-400"></i> Sejak {{ $asset->handover_date ? \Carbon\Carbon::parse($asset->handover_date)->format('d/m/y') : '-' }}
                                </span>
                            </div>
                        </div>
                    @empty
                    @endforelse

                    {{-- FALLBACK KOSONG --}}
                    @if($totalAset == 0)
                        <div class="text-center py-10 bg-slate-50/50 rounded-3xl border-2 border-dashed border-slate-200">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-box-open text-2xl text-slate-300"></i>
                            </div>
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Tidak ada aset inventaris yang dialokasikan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN (1/3 LEBAR - SIDEBAR) --}}
        <div class="space-y-8">
            
            {{-- WIDGET MASA KERJA --}}
            <div class="bg-gradient-to-br from-indigo-900 to-slate-900 rounded-[2.5rem] p-8 text-white shadow-xl shadow-indigo-900/20 relative overflow-hidden group">
                <i class="fas fa-history absolute -right-4 -bottom-4 text-[8rem] text-white opacity-5 group-hover:rotate-12 transition-transform duration-700 pointer-events-none"></i>
                
                <div class="relative z-10">
                    <div class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-xl flex items-center justify-center mb-6 border border-white/10 text-indigo-300">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-indigo-300 mb-2">Masa Kerja Aktif</p>
                    <h4 class="text-3xl font-black mb-6 tracking-tighter">{{ $user->service_period ?? 'Baru Bergabung' }}</h4>
                    
                    <div class="w-full bg-slate-900/50 rounded-full h-2.5 overflow-hidden p-0.5 border border-white/10 shadow-inner">
                        <div class="bg-gradient-to-r from-indigo-400 via-emerald-400 to-teal-400 h-full rounded-full w-full animate-pulse"></div>
                    </div>
                    <p class="text-[9px] text-indigo-200/50 mt-3 font-bold uppercase tracking-widest flex items-center gap-1.5">
                        <i class="fas fa-robot"></i> Dihitung otomatis oleh sistem
                    </p>
                </div>
            </div>

            {{-- WIDGET LEGAL & BANK --}}
            <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-xl shadow-slate-200/40">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 border-b-2 border-slate-50 pb-4 flex items-center gap-2">
                    <i class="fas fa-file-signature text-slate-300 text-lg"></i> Legal & Finansial
                </h3>
                
                <ul class="space-y-6">
                    <li class="flex flex-col group">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5"><i class="fas fa-id-card text-slate-300 group-hover:text-indigo-400 transition-colors"></i> No. Identitas (KTP)</span>
                        <span class="text-sm font-black text-slate-700 tracking-widest font-mono bg-slate-50 px-4 py-2 rounded-xl border border-slate-100">{{ $user->ktp_no ?? 'Belum Diinput' }}</span>
                    </li>
                    <li class="flex flex-col group">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5"><i class="fas fa-money-check text-slate-300 group-hover:text-emerald-400 transition-colors"></i> Rekening {{ $user->bank_name ?? 'Bank' }}</span>
                        <span class="text-sm font-black text-emerald-600 tracking-widest font-mono bg-emerald-50 px-4 py-2 rounded-xl border border-emerald-100">{{ $user->bank_account_no ?? 'Belum Diinput' }}</span>
                    </li>
                    <li class="flex flex-col pt-4 border-t border-slate-50 group">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5"><i class="fas fa-heartbeat text-slate-300 group-hover:text-rose-400 transition-colors"></i> BPJS Kesehatan</span>
                        <span class="text-xs font-black text-indigo-600 px-4 py-2 bg-indigo-50 rounded-xl border border-indigo-100 font-mono tracking-widest inline-block w-fit">
                            {{ $user->bpjs_kesehatan_no ?? 'UNREGISTERED' }}
                        </span>
                    </li>
                </ul>
            </div>

            {{-- WIDGET DOKUMEN DIGITAL --}}
            <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-xl shadow-slate-200/40 no-print">
                <div class="flex items-center justify-between mb-6 border-b-2 border-slate-50 pb-4">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-folder-open text-slate-300 text-lg"></i> Berkas Digital
                    </h3>
                    <button onclick="toggleUploadForm()" class="text-[10px] bg-indigo-50 text-indigo-600 px-4 py-2 rounded-xl font-black uppercase tracking-wider hover:bg-indigo-600 hover:text-white transition-all shadow-sm flex items-center gap-1.5">
                        <i class="fas fa-cloud-upload-alt"></i> Upload
                    </button>
                </div>

                {{-- Form Upload (Hidden by default) --}}
                <div id="uploadFormContainer" class="hidden mb-6 p-6 bg-slate-50 rounded-2xl border border-slate-200 animate-fadeIn relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
                    <form action="{{ route('admin.documents.upload', $user->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Nama Dokumen</label>
                            <input type="text" name="type" placeholder="Misal: KTP, Ijazah, Kontrak Kerja" required class="w-full px-4 py-3 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition font-bold text-slate-700 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Pilih Berkas (Max 5MB)</label>
                            <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" required class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200 cursor-pointer bg-white border border-slate-200 rounded-xl shadow-sm">
                        </div>
                        <div class="flex justify-end gap-2 pt-3 border-t border-slate-200/60">
                            <button type="button" onclick="toggleUploadForm()" class="px-4 py-2.5 text-[10px] font-black uppercase tracking-widest bg-white border border-slate-200 text-slate-500 rounded-xl hover:bg-slate-50 transition shadow-sm">Batal</button>
                            <button type="submit" class="px-5 py-2.5 text-[10px] font-black uppercase tracking-widest bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition flex items-center gap-2"><i class="fas fa-save"></i> Simpan</button>
                        </div>
                    </form>
                </div>

                {{-- List Dokumen --}}
                <div class="space-y-3">
                    @forelse($user->documents ?? [] as $doc)
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:bg-white hover:border-indigo-100 hover:shadow-md transition-all group/doc">
                            <div class="flex items-center gap-3 overflow-hidden">
                                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center text-sm shrink-0 border border-rose-100">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-xs font-black text-slate-800 uppercase block tracking-tight truncate">{{ $doc->type }}</span>
                                    <span class="text-[9px] text-slate-400 font-bold block truncate mt-0.5 tracking-wider">{{ $doc->file_name }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0 pl-2">
                                <a href="{{ route('admin.documents.download', $doc->id) }}" class="w-8 h-8 flex items-center justify-center text-indigo-500 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition" title="Download Berkas">
                                    <i class="fas fa-cloud-download-alt text-sm"></i>
                                </a>
                                <form action="{{ route('admin.documents.delete', $doc->id) }}" method="POST" onsubmit="return confirm('Peringatan!\n\nApakah Anda yakin ingin menghapus dokumen permanen ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center text-rose-400 hover:bg-rose-50 hover:text-rose-600 rounded-lg transition" title="Hapus Berkas">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 bg-slate-50/50 rounded-3xl border border-dashed border-slate-200">
                            <i class="fas fa-folder-open text-3xl mb-3 text-slate-200"></i>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest italic">Belum ada dokumen terlampir</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* CSS Khusus Mode Print jika dicetak langsung dari layar Detail */
    @media print {
        @page { margin: 1cm; }
        body { background: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .no-print { display: none !important; }
        
        /* Hapus shadow dan optimasi border */
        .shadow-2xl, .shadow-xl, .shadow-lg, .shadow-sm, .shadow-inner { box-shadow: none !important; }
        .rounded-\[2\.5rem\], .rounded-\[2rem\] { border-radius: 1rem !important; }
        
        /* Ubah Hero Card gelap menjadi mode hemat tinta yang elegan */
        .bg-gradient-to-br { background: #f8fafc !important; border: 2px solid #e2e8f0 !important; color: #1e293b !important; }
        .text-white { color: #0f172a !important; }
        .text-indigo-200, .text-indigo-300 { color: #64748b !important; }
        .bg-white\/10 { background: white !important; border: 1px solid #cbd5e1 !important; }
        .drop-shadow-md { filter: none !important; }
    }
</style>

<script>
    function toggleUploadForm() {
        const container = document.getElementById('uploadFormContainer');
        
        if (container.classList.contains('hidden')) {
            container.classList.remove('hidden');
            // Sedikit scroll agar form terlihat jika di layar kecil
            setTimeout(() => {
                container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 100);
        } else {
            container.classList.add('hidden');
        }
    }
</script>
@endsection