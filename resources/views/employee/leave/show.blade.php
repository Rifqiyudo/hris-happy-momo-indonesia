@extends('employee.layouts.app')
@section('title', 'Detail Pengajuan Izin')

@section('content')
<div class="page-fade space-y-6 pb-24 px-2">
    {{-- Header & Back Button --}}
    <div class="flex items-center justify-between py-2">
        <div class="flex items-center gap-4">
            <a href="{{ route('employee.leave.index') }}" 
               class="w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-sm border border-gray-100 text-slate-500 active:scale-95 transition">
                <i class="fas fa-chevron-left text-xs"></i>
            </a>
            <h1 class="text-lg font-black text-slate-800 tracking-tight">Detail Izin</h1>
        </div>
        
        {{-- Tombol Batal (Hanya jika status masih Pending murni) --}}
        @if($leave->status === 'Pending')
        <form action="{{ route('employee.leave.destroy', $leave->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="text-[10px] font-black text-rose-500 uppercase tracking-widest bg-rose-50 px-4 py-2 rounded-xl border border-rose-100 active:scale-95 transition">
                <i class="fas fa-trash-alt mr-1"></i> Batalkan
            </button>
        </form>
        @endif
    </div>

    {{-- 1. Main Info Card --}}
    <div class="bg-white rounded-[2.5rem] p-7 shadow-[0_10px_40px_rgba(0,0,0,0.03)] border border-gray-50 space-y-6">
        <div class="flex justify-between items-start">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-0.5">Tipe Pengajuan</p>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight uppercase">{{ $leave->type }}</h2>
            </div>

            @php
                $status = $leave->status;
                $isApproved = str_contains(strtolower($status), 'approved');
                $isRejected = str_contains(strtolower($status), 'rejected');
                
                $badgeClass = $isApproved ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 
                             ($isRejected ? 'bg-rose-50 text-rose-600 border-rose-100' : 
                              'bg-amber-50 text-amber-600 border-amber-100');
                
                $label = match($status) {
                    'Approved_by_pic'   => 'DISETUJUI SPV',
                    'Approved_by_admin' => 'DISETUJUI HRD',
                    'Rejected_by_pic'   => 'DITOLAK SPV',
                    'Rejected_by_admin' => 'DITOLAK HRD',
                    'Pending'           => 'MENUNGGU',
                    default             => strtoupper($status)
                };
            @endphp

            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-[11px] font-black border {{ $badgeClass }}">
                <span class="w-2 h-2 rounded-full bg-current opacity-70 {{ $status == 'Pending' ? 'animate-pulse' : '' }}"></span>
                {{ $label }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-dashed border-slate-100">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Rentang Waktu</p>
                <p class="text-sm font-black text-slate-700">
                    {{ \Carbon\Carbon::parse($leave->start_date)->translatedFormat('d M') }} - {{ \Carbon\Carbon::parse($leave->end_date)->translatedFormat('d M Y') }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Hari</p>
                <p class="text-sm font-black text-indigo-600">{{ $leave->total_days }} Hari</p>
            </div>
        </div>

        <div class="bg-slate-50 p-5 rounded-3xl border border-slate-100">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Alasan Pengajuan:</p>
            <p class="text-sm text-slate-600 leading-relaxed font-medium italic">"{{ $leave->reason }}"</p>
        </div>
    </div>

    {{-- 2. Timeline Progress (TERBARU) --}}
    <div class="bg-white rounded-[2.5rem] p-7 shadow-sm border border-gray-50 space-y-6">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Status Progress</p>
        
        <div class="space-y-6 ml-2">
            {{-- Tahap 1: Pengajuan --}}
            <div class="relative pl-8 border-l-2 border-emerald-500">
                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-emerald-500 border-4 border-white shadow-sm"></div>
                <p class="text-[11px] font-black text-slate-800 uppercase">Pengajuan Terkirim</p>
                <p class="text-[10px] text-slate-400 font-bold uppercase">{{ $leave->created_at->translatedFormat('d M Y, H:i') }}</p>
            </div>

            {{-- Tahap 2: PIC/Supervisor --}}
            @php $picDone = str_contains(strtolower($status), 'pic') || $status === 'Approved_by_admin'; @endphp
            <div class="relative pl-8 border-l-2 {{ $picDone ? 'border-emerald-500' : 'border-slate-100' }}">
                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full {{ $picDone ? 'bg-emerald-500' : 'bg-slate-200' }} border-4 border-white shadow-sm"></div>
                <p class="text-[11px] font-black {{ $picDone ? 'text-slate-800' : 'text-slate-300' }} uppercase">Verifikasi Supervisor</p>
                @if($leave->approved_by_pic_at)
                    <p class="text-[10px] text-emerald-500 font-bold uppercase">Disetujui pada {{ \Carbon\Carbon::parse($leave->approved_by_pic_at)->translatedFormat('d M, H:i') }}</p>
                @elseif(str_contains($status, 'Rejected_by_pic'))
                    <p class="text-[10px] text-rose-500 font-bold uppercase">Ditolak oleh Supervisor</p>
                @else
                    <p class="text-[10px] text-slate-300 font-bold uppercase">Menunggu Antrean</p>
                @endif
            </div>

            {{-- Tahap 3: Admin/HRD --}}
            @php $adminDone = str_contains(strtolower($status), 'admin'); @endphp
            <div class="relative pl-8">
                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full {{ $adminDone ? ($isApproved ? 'bg-emerald-500' : 'bg-rose-500') : 'bg-slate-200' }} border-4 border-white shadow-sm"></div>
                <p class="text-[11px] font-black {{ $adminDone ? 'text-slate-800' : 'text-slate-300' }} uppercase">Persetujuan Final HRD</p>
                @if($leave->approved_by_admin_at)
                    <p class="text-[10px] text-emerald-600 font-bold uppercase">Selesai pada {{ \Carbon\Carbon::parse($leave->approved_by_admin_at)->translatedFormat('d M, H:i') }}</p>
                @elseif(str_contains($status, 'Rejected_by_admin'))
                    <p class="text-[10px] text-rose-500 font-bold uppercase">Ditolak oleh HRD</p>
                @else
                    <p class="text-[10px] text-slate-300 font-bold uppercase">Tahap Terakhir</p>
                @endif
            </div>
        </div>
    </div>

    {{-- 3. Lampiran Dokumen --}}
    <div class="bg-white rounded-[2.5rem] p-7 shadow-sm border border-gray-50">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 ml-1">Lampiran Dokumen</p>
        
        @if($leave->attachment)
            @php $extension = pathinfo($leave->attachment, PATHINFO_EXTENSION); @endphp
            @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png']))
                <div class="relative group">
                    <img src="{{ asset('storage/' . $leave->attachment) }}" class="w-full rounded-3xl border-4 border-slate-50 shadow-md">
                    <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" class="absolute inset-0 flex items-center justify-center bg-black/20 opacity-0 group-hover:opacity-100 transition rounded-3xl">
                        <span class="bg-white px-4 py-2 rounded-xl text-[10px] font-black uppercase shadow-xl">Lihat Foto</span>
                    </a>
                </div>
            @else
                <div class="flex items-center justify-between p-4 bg-indigo-50 rounded-2xl border border-indigo-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-indigo-500 shadow-sm">
                            <i class="fas fa-file-pdf text-lg"></i>
                        </div>
                        <span class="text-xs font-black text-indigo-600 uppercase">File Lampiran</span>
                    </div>
                    <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" class="text-[10px] font-black text-indigo-600 underline">DOWNLOAD</a>
                </div>
            @endif
        @else
            <div class="py-10 text-center bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200">
                <i class="fas fa-image text-slate-200 text-3xl mb-2"></i>
                <p class="text-[10px] font-bold text-slate-400 uppercase">Tidak ada lampiran</p>
            </div>
        @endif
    </div>

    {{-- 4. Catatan Khusus --}}
    @if($leave->admin_note || $leave->pic_note)
    <div class="bg-amber-50 rounded-[2.5rem] p-7 border border-amber-100 space-y-4">
        <div class="flex items-center gap-2 text-amber-600">
            <i class="fas fa-comment-dots"></i>
            <p class="text-[10px] font-black uppercase tracking-widest">Tanggapan Manajemen</p>
        </div>
        
        @if($leave->pic_note)
            <div class="space-y-1">
                <p class="text-[9px] font-black text-amber-500 uppercase tracking-tighter">Catatan Supervisor:</p>
                <p class="text-sm font-bold text-amber-900 leading-relaxed">"{{ $leave->pic_note }}"</p>
            </div>
        @endif

        @if($leave->admin_note)
            <div class="space-y-1 pt-2 border-t border-amber-200/50">
                <p class="text-[9px] font-black text-amber-500 uppercase tracking-tighter">Catatan HRD:</p>
                <p class="text-sm font-bold text-amber-900 leading-relaxed">"{{ $leave->admin_note }}"</p>
            </div>
        @endif
    </div>
    @endif
</div>
@endsection