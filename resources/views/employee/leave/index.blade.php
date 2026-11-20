@extends('employee.layouts.app')
@section('title', 'Daftar Izin & Cuti')

@section('content')
<div class="page-fade space-y-6 pb-24 px-2">
    
    {{-- TOP NAVIGATION & HEADER --}}
    <div class="flex items-center justify-between py-2">
        <div class="flex items-center gap-4">
            <a href="{{ route('employee.dashboard') }}" 
               class="w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-sm border border-gray-100 text-slate-500 active:scale-95 transition-all">
                <i class="fas fa-chevron-left text-xs"></i>
            </a>
            <h1 class="text-lg font-black text-slate-800 tracking-tight">Izin & Cuti</h1>
        </div>
        
        <a href="{{ route('employee.leave.create') }}" 
           class="px-5 py-2.5 bg-indigo-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-lg shadow-indigo-100 active:scale-95 transition-all flex items-center gap-2">
            <i class="fas fa-plus"></i> Buat Baru
        </a>
    </div>

    {{-- 1. TAB NAVIGATION (TERBARU) --}}
    <div class="flex items-center gap-2 overflow-x-auto pb-2 no-scrollbar">
        @php $currentStatus = request('status', 'Pending'); @endphp
        
        <a href="{{ route('employee.leave.index', ['status' => 'Pending']) }}" 
           class="px-5 py-2.5 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all whitespace-nowrap
           {{ $currentStatus == 'Pending' ? 'bg-amber-500 text-white shadow-lg shadow-amber-100' : 'bg-white text-slate-400 border border-slate-100' }}">
            Menunggu ({{ $stats['pending'] ?? 0 }})
        </a>

        <a href="{{ route('employee.leave.index', ['status' => 'Approved']) }}" 
           class="px-5 py-2.5 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all whitespace-nowrap
           {{ $currentStatus == 'Approved' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-100' : 'bg-white text-slate-400 border border-slate-100' }}">
            Disetujui ({{ $stats['approved'] ?? 0 }})
        </a>

        <a href="{{ route('employee.leave.index', ['status' => 'all']) }}" 
           class="px-5 py-2.5 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all whitespace-nowrap
           {{ $currentStatus == 'all' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'bg-white text-slate-400 border border-slate-100' }}">
            Semua
        </a>
    </div>

    {{-- 2. LIST PENGAJUAN --}}
    <div class="space-y-5">
        @forelse($leaves as $item)
        <div class="bg-white p-6 rounded-[2.5rem] shadow-[0_10px_40px_rgba(0,0,0,0.03)] border border-gray-50 flex flex-col gap-4 group active:scale-[0.98] transition-all relative overflow-hidden">
            
            {{-- Indikator Warna Samping --}}
            @php
                $statusLower = strtolower($item->status);
                $isApproved = str_contains($statusLower, 'approved');
                $isRejected = str_contains($statusLower, 'rejected');
                $isPending = $statusLower == 'pending';
            @endphp

            <div class="absolute left-0 top-0 bottom-0 w-1.5 
                {{ $isApproved ? 'bg-emerald-500' : ($isRejected ? 'bg-rose-500' : 'bg-amber-500') }}">
            </div>

            {{-- Bagian Atas: Badge Tipe & Status --}}
            <div class="flex justify-between items-start">
                <div class="flex flex-col gap-1">
                    <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-lg text-[9px] font-black uppercase tracking-widest border border-slate-200 w-fit">
                        {{ $item->type }}
                    </span>
                    <span class="text-[9px] font-bold text-slate-400 ml-0.5">
                        <i class="far fa-clock mr-1"></i> {{ $item->total_days }} Hari
                    </span>
                </div>
                
                {{-- STATUS BADGE DENGAN LOGIKA WORKFLOW --}}
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-black border tracking-tight
                    {{ $isApproved ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 
                       ($isRejected ? 'bg-rose-50 text-rose-600 border-rose-100' : 
                        'bg-amber-50 text-amber-600 border-amber-100') }}">
                    
                    <span class="w-1.5 h-1.5 rounded-full bg-current {{ $isPending ? 'animate-pulse' : '' }}"></span>
                    
                    @if($item->status == 'Approved_by_pic')
                        DISETUJUI SPV
                    @elseif($item->status == 'Approved_by_admin')
                        DISETUJUI HRD
                    @elseif($isRejected)
                        DITOLAK
                    @else
                        MENUNGGU
                    @endif
                </span>
            </div>

            {{-- Bagian Tengah: Informasi Tanggal --}}
            <div class="flex items-center gap-4 py-1">
                <div class="w-12 h-12 {{ $isApproved ? 'bg-emerald-50 text-emerald-500 border-emerald-100' : 'bg-indigo-50 text-indigo-400 border-indigo-100' }} rounded-2xl flex items-center justify-center border">
                    <i class="fas fa-calendar-check text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5 ml-0.5">Rentang Waktu</p>
                    <p class="text-sm font-black text-slate-800 italic">
                        {{ \Carbon\Carbon::parse($item->start_date)->translatedFormat('d M') }} 
                        <span class="mx-1 text-slate-300 font-normal">s/d</span> 
                        {{ \Carbon\Carbon::parse($item->end_date)->translatedFormat('d M Y') }}
                    </p>
                </div>
            </div>

            {{-- Bagian Bawah: Alasan --}}
            <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-100">
                <p class="text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Keterangan:</p>
                <p class="text-xs text-slate-600 leading-relaxed font-medium italic">
                    "{{ Str::limit($item->reason, 100) }}"
                </p>
            </div>

            {{-- Footer: Catatan Admin & Link Detail --}}
            <div class="flex justify-between items-center pt-1">
                @if($item->admin_note || $item->pic_note)
                    <span class="text-[9px] font-bold text-indigo-500 italic flex items-center gap-1">
                        <i class="fas fa-comment-dots"></i> Ada catatan progres
                    </span>
                @else
                    <span></span>
                @endif

                <a href="{{ route('employee.leave.show', $item->id) }}" class="text-[10px] font-black text-indigo-600 uppercase tracking-widest flex items-center gap-2 group-hover:gap-3 transition-all">
                    Detail Pengajuan <i class="fas fa-arrow-right text-[8px]"></i>
                </a>
            </div>
        </div>
        @empty
        {{-- Tampilan saat data kosong --}}
        <div class="flex flex-col items-center justify-center py-24 bg-white rounded-[3rem] border border-dashed border-slate-200 shadow-sm">
            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-6 text-slate-200">
                <i class="fas fa-envelope-open-text text-4xl"></i>
            </div>
            <h3 class="text-slate-800 font-black text-lg mb-2">Folder Kosong</h3>
            <p class="text-slate-400 text-xs text-center px-10 leading-relaxed uppercase tracking-tighter">
                Tidak ada data pengajuan dengan status <strong>{{ $currentStatus }}</strong> saat ini.
            </p>
        </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    @if($leaves->hasPages())
    <div class="pt-4 px-2">
        {{ $leaves->links() }}
    </div>
    @endif
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection