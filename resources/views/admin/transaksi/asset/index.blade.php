@extends('admin.layouts.master')
@section('title', 'Manajemen Aset - PT. Happy Momo Indonesia')

@section('content')
<div class="space-y-8 animate-fadeIn max-w-7xl mx-auto pb-12">
    
    {{-- 1. HEADER & ACTION --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 md:p-8 rounded-[2rem] border border-slate-100 shadow-sm">
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center shadow-inner">
                <i class="fas fa-boxes text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">Daftar Aset Perusahaan</h1>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Monitoring Inventaris & Alokasi Perangkat</p>
            </div>
        </div>
        
        {{-- SEARCH, FILTER & ADD BUTTON --}}
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto items-center">
            <form action="{{ route('admin.transaksi.asset.index') }}" method="GET" class="flex w-full sm:w-auto gap-2">
                <div class="relative w-full sm:w-48">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aset/SN..." 
                           class="w-full bg-slate-50 border border-slate-200 text-xs font-bold text-slate-600 rounded-xl pl-9 pr-3 py-3 outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>
                <select name="status" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 text-xs font-bold text-slate-600 rounded-xl px-3 py-3 outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer transition-all">
                    <option value="">Semua Status</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Di Gudang</option>
                    <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Dipakai</option>
                    <option value="broken" {{ request('status') == 'broken' ? 'selected' : '' }}>Rusak</option>
                </select>
            </form>
            
            <a href="{{ route('admin.transaksi.asset.create') }}" class="w-full sm:w-auto px-6 py-3.5 bg-slate-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition shadow-lg shadow-slate-200 flex items-center justify-center gap-2 transform active:scale-95">
                <i class="fas fa-plus text-[10px]"></i> Tambah Aset
            </a>
        </div>
    </div>

    {{-- 2. STATISTIC CARDS (MODERNIZED) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors">
                    <i class="fas fa-wallet text-lg"></i>
                </div>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Nilai Aset</p>
            <h3 class="text-3xl font-black text-slate-800 tracking-tighter">Rp {{ number_format($assets->sum('purchase_price'), 0, ',', '.') }}</h3>
        </div>
        
        <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                    <i class="fas fa-user-check text-lg"></i>
                </div>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Aset Terpakai</p>
            <h3 class="text-3xl font-black text-blue-600 tracking-tighter">{{ $assets->whereNotNull('user_id')->count() }} <span class="text-xs text-slate-400 ml-1 font-bold uppercase tracking-widest">Unit</span></h3>
        </div>
        
        <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all duration-300 group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center group-hover:bg-emerald-50 group-hover:text-emerald-500 transition-colors">
                    <i class="fas fa-warehouse text-lg"></i>
                </div>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Tersedia di Gudang</p>
            <h3 class="text-3xl font-black text-emerald-500 tracking-tighter">{{ $assets->whereNull('user_id')->count() }} <span class="text-xs text-slate-400 ml-1 font-bold uppercase tracking-widest">Unit</span></h3>
        </div>
    </div>

    {{-- 3. MAIN TABLE AREA --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50/80 border-b border-slate-100">
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                        <th class="px-8 py-6">Nama & Info Aset</th>
                        <th class="px-6 py-6">Pemegang (Karyawan)</th>
                        <th class="px-6 py-6">Nilai & Perolehan</th>
                        <th class="px-6 py-6 text-center">Status</th>
                        <th class="px-8 py-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($assets as $asset)
                    <tr class="hover:bg-slate-50/60 transition duration-150 group">
                        
                        {{-- Nama & Serial (Dengan Ikon Dinamis) --}}
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                @php
                                    $cat = strtolower($asset->category);
                                    $icon = 'fa-box';
                                    $iconBg = 'bg-slate-50 text-slate-500 border-slate-200';
                                    
                                    if(in_array($cat, ['electronics', 'laptop', 'pc', 'hp'])) {
                                        $icon = 'fa-laptop';
                                        $iconBg = 'bg-blue-50 text-blue-500 border-blue-100';
                                    } elseif(in_array($cat, ['vehicle', 'kendaraan', 'motor', 'mobil'])) {
                                        $icon = 'fa-car';
                                        $iconBg = 'bg-amber-50 text-amber-500 border-amber-100';
                                    } elseif(in_array($cat, ['furniture', 'meja', 'kursi'])) {
                                        $icon = 'fa-chair';
                                        $iconBg = 'bg-orange-50 text-orange-500 border-orange-100';
                                    }
                                @endphp
                                <div class="w-12 h-12 flex-shrink-0 rounded-2xl flex items-center justify-center border shadow-sm {{ $iconBg }}">
                                    <i class="fas {{ $icon }} text-lg"></i>
                                </div>
                                <div>
                                    <div class="font-black text-slate-800 uppercase text-sm mb-1 tracking-tight">{{ $asset->asset_name ?? $asset->name }}</div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest bg-slate-50 px-2 py-0.5 rounded-md border border-slate-100">
                                            SN: {{ $asset->asset_serial_number ?? $asset->serial_number }}
                                        </span>
                                        <span class="text-[9px] font-black text-indigo-500 uppercase tracking-widest">
                                            {{ $asset->category }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        
                        {{-- PEMEGANG KARYAWAN --}}
                        <td class="px-6 py-5">
                            @if($asset->user_id && $asset->user)
                                <div class="flex items-center gap-3">
                                    <img src="{{ $asset->user->avatar ? asset('storage/'.$asset->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($asset->user->name).'&background=f8fafc&color=4f46e5' }}" 
                                         class="w-10 h-10 rounded-xl object-cover shadow-sm border border-slate-200">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black text-slate-700 uppercase tracking-tight">{{ $asset->user->name }}</span>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase mt-0.5 tracking-widest">
                                            {{ $asset->user->position->name ?? 'Staff' }} 
                                            <span class="mx-1 opacity-50">•</span> 
                                            {{-- [DIPERBAIKI] Memanggil accessor unit_kerja yang aman --}}
                                            {{ $asset->user->unit_kerja ?? 'Pusat' }}
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center gap-2 text-emerald-500">
                                    <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center border border-emerald-100">
                                        <i class="fas fa-warehouse text-xs"></i>
                                    </div>
                                    <span class="text-[10px] font-black uppercase tracking-widest">Ready Stock</span>
                                </div>
                            @endif
                        </td>

                        {{-- Info Keuangan --}}
                        <td class="px-6 py-5">
                            <div class="text-sm font-black text-slate-700 tracking-tight">Rp {{ number_format($asset->purchase_price, 0, ',', '.') }}</div>
                            <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">
                                {{-- [DIPERBAIKI] Format Tanggal Lokal Indonesia --}}
                                Beli: <span class="text-slate-500">{{ $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->translatedFormat('d M Y') : '-' }}</span>
                            </div>
                        </td>

                        {{-- Status Badge --}}
                        <td class="px-6 py-5 text-center">
                            @php
                                $statusClasses = [
                                    'available'   => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                    'assigned'    => 'bg-indigo-50 text-indigo-600 border-indigo-200',
                                    'used'        => 'bg-indigo-50 text-indigo-600 border-indigo-200',
                                    'maintenance' => 'bg-amber-50 text-amber-600 border-amber-200',
                                    'lost'        => 'bg-rose-50 text-rose-600 border-rose-200',
                                    'broken'      => 'bg-rose-50 text-rose-600 border-rose-200',
                                ];
                                $currentStatus = strtolower($asset->status);
                                $class = $statusClasses[$currentStatus] ?? 'bg-slate-50 text-slate-500 border-slate-200';
                            @endphp
                            <span class="px-4 py-2 rounded-xl border {{ $class }} text-[9px] font-black uppercase tracking-widest shadow-sm inline-flex items-center justify-center">
                                {{ $asset->status }}
                            </span>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-8 py-5 text-right align-middle">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <a href="{{ route('admin.transaksi.asset.show', $asset->id) }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-indigo-500 hover:bg-indigo-50 hover:border-indigo-200 transition shadow-sm transform active:scale-95" title="Riwayat & Detail">
                                    <i class="fas fa-history text-[10px]"></i>
                                </a>
                                <a href="{{ route('admin.transaksi.asset.edit', $asset->id) }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-amber-500 hover:bg-amber-50 hover:border-amber-200 transition shadow-sm transform active:scale-95" title="Edit Data">
                                    <i class="fas fa-pencil-alt text-[10px]"></i>
                                </a>
                                <form action="{{ route('admin.transaksi.asset.destroy', $asset->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus aset ini secara permanen?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-rose-500 hover:bg-rose-50 hover:border-rose-200 transition shadow-sm transform active:scale-95" title="Hapus">
                                        <i class="fas fa-trash-alt text-[10px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-32 text-center">
                            <div class="opacity-30 flex flex-col items-center">
                                <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-box-open text-4xl text-slate-400"></i>
                                </div>
                                <h4 class="font-black uppercase tracking-[0.2em] text-sm text-slate-500">Belum ada aset terdaftar</h4>
                                <p class="text-[10px] font-bold text-slate-400 mt-2">Klik tombol Tambah Aset untuk memulai.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Paginasi Data Jika Tersedia --}}
        @if(method_exists($assets, 'hasPages') && $assets->hasPages())
            <div class="p-6 border-t border-slate-100 bg-slate-50">
                {{ $assets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection