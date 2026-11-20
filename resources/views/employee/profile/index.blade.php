@extends('employee.layouts.app')

@section('title', 'Profil Saya - Happy Momo')

@section('content')
{{-- 1. HEADER MINIMALIS --}}
<div class="relative bg-white pb-6 pt-10 border-b border-slate-100">
    <div class="px-6 flex items-center justify-between mb-8">
        <h1 class="text-xl font-black text-slate-800 tracking-tight">Profil Saya</h1>
        <a href="{{ route('employee.profile.edit') }}" class="w-10 h-10 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 active:scale-95 transition-all shadow-sm border border-indigo-100">
            <i class="fas fa-pen-nib text-xs"></i>
        </a>
    </div>

    <div class="px-6 flex flex-col items-center">
        <div class="relative">
            {{-- Foto Profil dengan Border Indigo Halus --}}
            <div class="w-24 h-24 rounded-3xl border-4 border-white shadow-2xl overflow-hidden bg-slate-50 ring-1 ring-indigo-100">
                <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=5D5FEF&color=fff' }}" alt="Avatar" class="w-full h-full object-cover">
            </div>
            <div class="absolute -bottom-2 -right-2 bg-emerald-500 text-white text-[7px] font-black px-3 py-1.5 rounded-full border-4 border-white uppercase tracking-widest shadow-lg">
                {{ $user->is_active ? 'Aktif' : 'Non-Aktif' }}
            </div>
        </div>
        <h2 class="mt-5 text-lg font-black text-slate-800 tracking-tight uppercase text-center leading-tight">{{ $user->name }}</h2>
        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.2em] mt-1">{{ $user->position->name ?? 'Staff' }}</p>
    </div>
</div>

<div class="px-6 py-8 pb-24 space-y-6 bg-slate-50/50">
    
    {{-- 2. DATA UTAMA (KARTU ELEGAN) --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white p-4 rounded-3xl shadow-sm border border-slate-100">
            <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest mb-1">NIK Karyawan</p>
            <p class="text-xs font-black text-slate-700 tracking-tight">{{ $user->nik }}</p>
        </div>
        <div class="bg-white p-4 rounded-3xl shadow-sm border border-slate-100">
            <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest mb-1">Masa Kerja</p>
            <p class="text-xs font-black text-slate-700 tracking-tight">{{ $user->service_period ?? '0 Hari' }}</p>
        </div>
        
        {{-- UNIT KERJA / CABANG (DINAMIS) --}}
        <div class="col-span-2 bg-white p-5 rounded-3xl shadow-sm border border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-500">
                    <i class="fas fa-building text-sm"></i>
                </div>
                <div>
                    <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest mb-0.5">Cabang Penempatan</p>
                    <p class="text-sm font-black text-slate-700 uppercase tracking-tight">
                        {{-- PERBAIKAN: Memanggil accessor unit_kerja agar dinamis dan terhubung dengan relasi cabang --}}
                        {{ $user->unit_kerja }}
                    </p>
                </div>
            </div>
            <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[8px] font-black rounded-lg uppercase border border-indigo-100">Verified</span>
        </div>
    </div>

    {{-- 3. MENU KEUANGAN (SLIP GAJI TERAKHIR) --}}
    <div class="space-y-3">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Gaji Terbaru</p>
        @if(isset($lastPayroll) && $lastPayroll)
            <a href="{{ route('employee.payroll.print', $lastPayroll->id) }}" 
               class="flex items-center justify-between p-4 bg-white rounded-3xl border border-slate-100 shadow-sm group active:bg-slate-50 transition-all">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-700 uppercase tracking-tight">Unduh Slip Gaji</p>
                        <p class="text-[9px] text-slate-400 font-bold uppercase">
                            @php
                                $displayMonth = $lastPayroll->month;
                                if(is_numeric($lastPayroll->month)) {
                                    $displayMonth = \Carbon\Carbon::createFromDate(null, (int)$lastPayroll->month, 1)->translatedFormat('F');
                                }
                            @endphp
                            {{ $displayMonth }} {{ $lastPayroll->year }}
                        </p>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
            </a>
        @else
            <div class="p-6 bg-white rounded-3xl border border-slate-100 border-dashed flex flex-col items-center justify-center opacity-60">
                <i class="fas fa-receipt text-slate-200 text-xl mb-2"></i>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest italic">Belum ada slip gaji tersedia</p>
            </div>
        @endif
    </div>

    {{-- 4. INFO KONTAK --}}
    <div class="bg-white rounded-[2.5rem] p-6 shadow-sm border border-slate-100 space-y-5">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400">
                <i class="fas fa-envelope-open-text"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest">Email Perusahaan</p>
                <p class="text-xs font-bold text-slate-700 truncate">{{ $user->email }}</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400">
                <i class="fab fa-whatsapp text-lg"></i>
            </div>
            <div>
                <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest">Nomor WhatsApp</p>
                <p class="text-xs font-bold text-slate-700">{{ $user->phone ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- 5. INVENTARIS ASET --}}
    <div class="space-y-3">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Aset Perusahaan</p>
        <div class="bg-white rounded-[2.5rem] p-6 shadow-sm border border-slate-100">
            @forelse($myAssets as $item)
                <div class="flex items-center gap-4 py-3 {{ !$loop->last ? 'border-b border-slate-50' : '' }}">
                    <div class="w-10 h-10 rounded-2xl bg-slate-50 flex items-center justify-center text-indigo-500">
                        <i class="fas fa-laptop-code text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-black text-slate-700 uppercase tracking-tight">{{ $item->asset->name }}</p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase">SN: {{ $item->asset->serial_number }}</p>
                    </div>
                    <span class="text-[8px] font-black px-2 py-1 bg-emerald-50 text-emerald-600 rounded-lg border border-emerald-100 uppercase">Dipinjam</span>
                </div>
            @empty
                <div class="text-center py-4">
                    <p class="text-[9px] font-bold text-slate-300 uppercase tracking-widest italic">Tidak ada aset terdaftar</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 6. ARSIP PAYROLL --}}
    <div class="space-y-3">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Arsip Slip Gaji</p>
        <div class="space-y-2">
            @forelse($myPayrolls as $payroll)
                <a href="{{ route('employee.payroll.print', $payroll->id) }}" 
                   class="flex items-center justify-between p-4 bg-white rounded-3xl border border-slate-100 shadow-sm active:scale-[0.98] transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400">
                            <i class="fas fa-calendar-alt text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-slate-700 uppercase">
                                @if(is_numeric($payroll->month))
                                    {{ \Carbon\Carbon::createFromDate(null, (int)$payroll->month, 1)->translatedFormat('F') }}
                                @else
                                    {{ $payroll->month }}
                                @endif
                                {{ $payroll->year }}
                            </p>
                            <p class="text-[8px] text-slate-400 font-bold uppercase tracking-tighter italic">Status: {{ $payroll->status }}</p>
                        </div>
                    </div>
                    <div class="w-8 h-8 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                        <i class="fas fa-download text-[10px]"></i>
                    </div>
                </a>
            @empty
                <div class="p-6 bg-white rounded-3xl border border-slate-100 border-dashed text-center">
                    <p class="text-[9px] font-bold text-slate-300 uppercase tracking-widest">Belum ada riwayat gaji</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- TAMBAHAN TERBARU: 7. DOKUMEN ADMINISTRASI --}}
    {{-- ========================================== --}}
    <div class="space-y-3">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Dokumen Administrasi</p>
        <a href="{{ route('employee.profile.certificate') }}" 
           class="flex items-center justify-between p-5 bg-indigo-600 rounded-[2rem] shadow-xl shadow-indigo-100 active:scale-95 transition-all group overflow-hidden relative">
            {{-- Efek dekoratif di background tombol --}}
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>
            
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-white border border-white/30">
                    <i class="fas fa-file-contract text-lg"></i>
                </div>
                <div>
                    <p class="text-sm font-black text-white uppercase tracking-tight">Surat Keterangan Kerja</p>
                    <p class="text-[10px] text-indigo-100 font-bold uppercase opacity-80">Unduh Format Digital (PDF)</p>
                </div>
            </div>
            <div class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center text-white relative z-10">
                <i class="fas fa-arrow-down text-xs animate-bounce"></i>
            </div>
        </a>
    </div>

    {{-- TOMBOL KELUAR --}}
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="w-full py-5 bg-white text-rose-500 rounded-3xl font-black text-[10px] uppercase tracking-[0.3em] hover:bg-rose-50 transition-all border border-rose-100 shadow-sm flex items-center justify-center gap-3 active:scale-95">
            <i class="fas fa-power-off text-xs"></i>
            Keluar Aplikasi
        </button>
    </form>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const successMessage = "{{ session('success') }}";
        if (successMessage && successMessage !== "") {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: successMessage,
                timer: 2000,
                showConfirmButton: false,
                customClass: { popup: 'rounded-3xl' }
            });
        }

        const errorMessage = "{{ session('error') }}";
        if (errorMessage && errorMessage !== "") {
            Swal.fire({
                icon: 'error',
                title: 'Ups!',
                text: errorMessage,
                confirmButtonColor: '#4f46e5',
                customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl font-bold' }
            });
        }
    });
</script>
@endpush
@endsection