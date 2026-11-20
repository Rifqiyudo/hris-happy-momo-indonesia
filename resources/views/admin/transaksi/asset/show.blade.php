@extends('admin.layouts.master')
@section('title', 'Riwayat Peminjaman Aset - ' . ($asset->name ?? $asset->asset_name))

@section('content')
<div class="max-w-5xl mx-auto bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-xl animate-fadeIn">
    
    {{-- ========================================================= --}}
    {{-- 1. HEADER & TOMBOL KEMBALI --}}
    {{-- ========================================================= --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 border-b border-slate-100 pb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.transaksi.asset.index') }}" class="w-10 h-10 flex items-center justify-center rounded-2xl bg-slate-50 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition shadow-sm">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tighter">Riwayat Pemakaian Aset</h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Log Distribusi & Penarikan Perangkat</p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.transaksi.asset.edit', $asset->id) }}" class="px-5 py-2.5 bg-amber-50 text-amber-600 border border-amber-100 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-amber-500 hover:text-white transition shadow-sm flex items-center gap-2">
                <i class="fas fa-edit"></i> Edit Aset Ini
            </a>
        </div>
    </div>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="mb-8 p-5 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-3 text-emerald-600">
            <i class="fas fa-check-circle text-xl"></i>
            <span class="text-[10px] font-bold uppercase tracking-widest">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-8 p-5 bg-rose-50 border border-rose-100 rounded-2xl flex items-center gap-3 text-rose-600">
            <i class="fas fa-exclamation-circle text-xl"></i>
            <span class="text-[10px] font-bold uppercase tracking-widest">{{ session('error') }}</span>
        </div>
    @endif

    {{-- ========================================================= --}}
    {{-- 2. KARTU INFORMASI ASET (KONTEKS) --}}
    {{-- ========================================================= --}}
    <div class="mb-10 p-6 bg-slate-50 border border-slate-100 rounded-[2rem] flex flex-col md:flex-row gap-6 items-center md:items-start shadow-sm">
        
        {{-- Foto Aset --}}
        @php
            $assetImagePath = $asset->image ?? $asset->photo ?? $asset->asset_image ?? null;
        @endphp
        <div class="w-32 h-32 bg-white rounded-2xl border-2 border-slate-100 p-2 flex-shrink-0 flex items-center justify-center overflow-hidden shadow-sm">
            @if($assetImagePath)
                <img src="{{ asset('storage/' . $assetImagePath) }}" class="w-full h-full object-contain" alt="Foto Aset">
            @else
                <div class="text-center text-slate-300">
                    <i class="fas fa-box-open text-3xl mb-1"></i>
                    <p class="text-[8px] font-black uppercase tracking-widest">No Image</p>
                </div>
            @endif
        </div>

        {{-- Detail Aset --}}
        <div class="flex-1 w-full text-center md:text-left">
            <div class="inline-block px-3 py-1 mb-3 rounded-full text-[9px] font-black uppercase tracking-widest 
                {{ strtolower($asset->status) == 'available' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 
                  (strtolower($asset->status) == 'assigned' ? 'bg-indigo-50 text-indigo-600 border border-indigo-100' : 
                  'bg-rose-50 text-rose-600 border border-rose-100') }}">
                <i class="fas fa-circle text-[6px] mr-1 align-middle"></i> 
                {{ $asset->status == 'available' ? 'Tersedia Di Gudang' : ($asset->status == 'assigned' ? 'Sedang Dipakai' : 'Maintenance / Rusak') }}
            </div>
            
            <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight mb-1">{{ $asset->name ?? $asset->asset_name }}</h3>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4">
                SN: <span class="text-slate-700">{{ $asset->serial_number ?? $asset->asset_serial_number ?? '-' }}</span> 
                @if($asset->brand) • MERK: <span class="text-slate-700">{{ $asset->brand }}</span> @endif
            </p>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-slate-200/60">
                <div>
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Kategori</p>
                    <p class="text-[10px] font-bold text-slate-700 uppercase">{{ $asset->category ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Kondisi</p>
                    <p class="text-[10px] font-bold text-slate-700 uppercase">{{ $asset->condition ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Harga Beli</p>
                    <p class="text-[10px] font-bold text-slate-700 uppercase">Rp {{ number_format($asset->purchase_price ?? 0, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Tgl Beli</p>
                    <p class="text-[10px] font-bold text-slate-700 uppercase">
                        {{ $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->translatedFormat('d M Y') : '-' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- 3. TIMELINE RIWAYAT PEMINJAMAN --}}
    {{-- ========================================================= --}}
    <div>
        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2 mb-6">
            <i class="fas fa-history text-indigo-400 text-lg"></i> <span>Log Riwayat Pemegang Aset</span>
        </h3>

        <div class="space-y-4 relative before:absolute before:inset-0 before:ml-6 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-200 before:to-transparent">
            
            @forelse($asset->assignments()->orderBy('assigned_date', 'desc')->get() as $log)
                
                @php
                    // Ambil data User secara aman
                    $userName = $log->user->name ?? 'Karyawan Dihapus';
                    $userPosition = $log->user->position->name ?? 'Unknown Position';
                    $userAvatar = isset($log->user->avatar) && $log->user->avatar 
                                  ? asset('storage/'.$log->user->avatar) 
                                  : 'https://ui-avatars.com/api/?name='.urlencode($userName).'&background=4f46e5&color=fff';
                    
                    // Format Tanggal
                    $dateAssign = $log->assigned_date ? \Carbon\Carbon::parse($log->assigned_date)->translatedFormat('d M Y') : 'Unknown';
                    $dateReturn = $log->return_date ? \Carbon\Carbon::parse($log->return_date)->translatedFormat('d M Y') : null;
                    
                    $isActive = is_null($log->return_date);
                @endphp

                <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                    
                    {{-- Dot Timeline Marker --}}
                    <div class="flex items-center justify-center w-12 h-12 rounded-full border-4 border-white {{ $isActive ? 'bg-indigo-500 text-white shadow-indigo-200' : 'bg-slate-200 text-slate-400' }} shadow-lg absolute left-0 md:left-1/2 -translate-y-4 sm:translate-y-0 transform md:-translate-x-1/2 z-10 transition-transform duration-300 group-hover:scale-110">
                        <i class="fas {{ $isActive ? 'fa-hand-holding' : 'fa-box-archive' }} text-sm"></i>
                    </div>

                    {{-- Kartu Riwayat --}}
                    <div class="w-full md:w-[calc(50%-3rem)] pl-16 md:pl-0">
                        <div class="p-6 bg-white border-2 {{ $isActive ? 'border-indigo-100 shadow-md shadow-indigo-50/50' : 'border-slate-100 shadow-sm hover:border-slate-200' }} rounded-[2rem] transition-all">
                            
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $userAvatar }}" class="w-10 h-10 rounded-full object-cover border-2 border-slate-100 shadow-sm">
                                    <div>
                                        <p class="font-black text-slate-800 uppercase text-xs tracking-tight">{{ $userName }}</p>
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $userPosition }}</p>
                                    </div>
                                </div>
                                
                                {{-- Badge Status --}}
                                @if($isActive)
                                    <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[8px] font-black uppercase tracking-widest border border-indigo-100 animate-pulse">
                                        Active
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-slate-50 text-slate-400 rounded-full text-[8px] font-black uppercase tracking-widest border border-slate-100">
                                        Selesai
                                    </span>
                                @endif
                            </div>

                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100/50">
                                <div class="flex items-center gap-4 text-[9px] font-bold uppercase tracking-widest">
                                    <div class="text-indigo-600">
                                        <span class="block text-[7px] text-slate-400 mb-0.5">Tanggal Serah Terima</span>
                                        <i class="fas fa-calendar-check mr-1"></i> {{ $dateAssign }}
                                    </div>
                                    <div class="text-slate-300">
                                        <i class="fas fa-arrow-right"></i>
                                    </div>
                                    <div class="{{ $isActive ? 'text-amber-500' : 'text-emerald-600' }}">
                                        <span class="block text-[7px] text-slate-400 mb-0.5">Tanggal Kembali</span>
                                        @if($isActive)
                                            <i class="fas fa-clock mr-1"></i> Masih Dipinjam
                                        @else
                                            <i class="fas fa-calendar-times mr-1"></i> {{ $dateReturn }}
                                        @endif
                                    </div>
                                </div>

                                @if($log->condition_notes)
                                    <div class="mt-3 pt-3 border-t border-slate-200/60">
                                        <span class="block text-[7px] font-black text-slate-400 uppercase tracking-widest mb-1">Catatan Serah Terima:</span>
                                        <p class="text-[10px] font-bold text-slate-600 italic">"{{ $log->condition_notes }}"</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Tombol Proses Kembali (Hanya muncul jika aset masih dipinjam) --}}
                            @if($isActive)
                                <div class="mt-5 pt-5 border-t border-slate-100 flex justify-end">
                                    <form action="{{ route('admin.transaksi.asset.return', $log->id) }}" method="POST" onsubmit="return confirmReturn(event, this, '{{ $userName }}')">
                                        @csrf
                                        <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-slate-900 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-rose-600 transition shadow-md active:scale-95 flex items-center justify-center gap-2 group/btn">
                                            <i class="fas fa-undo-alt group-hover/btn:-rotate-180 transition-transform duration-500"></i> Tarik Ke Gudang
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            
            @empty
                
                {{-- Jika Kosong --}}
                <div class="relative flex items-center justify-center z-10 py-10">
                    <div class="bg-white border-2 border-dashed border-slate-200 rounded-[2rem] p-10 text-center w-full max-w-lg shadow-sm">
                        <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-folder-open text-2xl"></i>
                        </div>
                        <h4 class="text-sm font-black text-slate-700 uppercase tracking-tight mb-1">Belum Ada Riwayat</h4>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Aset ini belum pernah dialokasikan ke karyawan manapun semenjak didaftarkan.</p>
                    </div>
                </div>

            @endforelse

        </div>
    </div>
</div>

<script>
    // Fungsi Konfirmasi JavaScript Native (Anti-Gagal)
    function confirmReturn(e, formElement, userName) {
        e.preventDefault(); // Hentikan form agar tidak langsung terkirim
        
        const message = `PERHATIAN!\n\nApakah Anda yakin ingin menarik/mengembalikan aset ini dari Sdr/i: ${userName}?\n\nStatus aset akan otomatis berubah menjadi "Tersedia Di Gudang".`;
        
        if (confirm(message)) {
            formElement.submit(); // Jika user klik "OK", lanjutkan submit
        }
    }
</script>
@endsection