@extends('employee.layouts.app')
@section('title', 'Detail Lembur')

@section('content')
<div class="p-6 pb-24">

    {{-- HEADER --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('employee.overtime.index') }}" class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 rounded-xl shadow-sm hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left text-slate-600"></i>
        </a>
        <h1 class="text-xl font-bold text-slate-800">Detail Lembur</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        {{-- BAGIAN ATAS: STATUS --}}
        <div class="p-6 border-b border-gray-50 bg-gray-50/50">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Status Pengajuan</p>
                    @if($overtime->status == 'pending')
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-bold bg-yellow-100 text-yellow-600">
                            <i class="fas fa-clock"></i> Menunggu
                        </span>
                    @elseif($overtime->status == 'approved')
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-bold bg-green-100 text-green-600">
                            <i class="fas fa-check-circle"></i> Disetujui
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-bold bg-red-100 text-red-600">
                            <i class="fas fa-times-circle"></i> Ditolak
                        </span>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Durasi</p>
                    <p class="text-xl font-black text-indigo-600">{{ $overtime->duration }} Jam</p>
                </div>
            </div>
        </div>

        {{-- BAGIAN ISI: DATA DETAIL --}}
        <div class="p-6 space-y-6">
            
            {{-- Tanggal --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Tanggal</label>
                <p class="text-slate-800 font-semibold text-lg">
                    {{ \Carbon\Carbon::parse($overtime->date)->translatedFormat('l, d F Y') }}
                </p>
            </div>

            {{-- Waktu --}}
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Jam Mulai</label>
                    <p class="text-slate-800 font-bold font-mono text-lg">
                        {{ \Carbon\Carbon::parse($overtime->start_time)->format('H:i') }}
                    </p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Jam Selesai</label>
                    <p class="text-slate-800 font-bold font-mono text-lg">
                        {{ \Carbon\Carbon::parse($overtime->end_time)->format('H:i') }}
                    </p>
                </div>
            </div>

            {{-- Alasan --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Kegiatan / Alasan</label>
                <div class="bg-gray-50 p-4 rounded-xl text-sm text-slate-700 leading-relaxed border border-gray-100">
                    {{ $overtime->reason }}
                </div>
            </div>

            {{-- Lampiran (Jika Ada) --}}
            @if($overtime->attachment)
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Lampiran Bukti</label>
                <div class="rounded-xl overflow-hidden border border-gray-200">
                    <img src="{{ asset('storage/' . $overtime->attachment) }}" alt="Bukti Lembur" class="w-full h-auto object-cover">
                </div>
            </div>
            @endif

            {{-- Catatan Penolakan (Hanya jika Ditolak) --}}
            @if($overtime->status == 'rejected' && $overtime->rejection_note)
            <div class="bg-red-50 p-4 rounded-xl border border-red-100">
                <label class="block text-xs font-bold text-red-500 uppercase mb-1">Alasan Penolakan Admin</label>
                <p class="text-sm text-red-700">{{ $overtime->rejection_note }}</p>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection