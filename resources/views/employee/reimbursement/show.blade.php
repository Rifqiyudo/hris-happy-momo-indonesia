@extends('employee.layouts.app')
@section('title', 'Detail Klaim')

@section('content')
<div class="page-fade space-y-6 pb-20 max-w-2xl mx-auto">
    {{-- Navigasi Atas --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('employee.reimbursement.index') }}" class="w-10 h-10 flex items-center justify-center rounded-2xl bg-white shadow-sm border border-slate-100 text-slate-400 hover:text-indigo-600 transition">
            <i class="fas fa-chevron-left text-xs"></i>
        </a>
        <h1 class="text-lg font-black text-slate-800 tracking-tight">Detail Klaim & Reimbursement</h1>
    </div>

    {{-- Kartu Info Utama --}}
    <div class="bg-white rounded-[2rem] p-6 sm:p-8 shadow-sm border border-slate-100 space-y-6 relative overflow-hidden">
        <div class="absolute right-0 top-0 w-36 h-36 bg-indigo-50 rounded-bl-full pointer-events-none opacity-50"></div>
        
        <div class="flex justify-between items-start relative z-10">
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Item / Keperluan</p>
                <h2 class="text-xl font-black text-slate-800 tracking-tight">{{ $reimbursement->item_name ?? $reimbursement->description }}</h2>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $reimbursement->status_badge ?? 'bg-amber-50 text-amber-600 border-amber-100' }}">
                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-75"></span>
                {{ $reimbursement->status_label ?? $reimbursement->status }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4 border-t border-dashed border-slate-100 pt-6 relative z-10">
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Kategori / Tipe</p>
                <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-xl text-[10px] font-black border border-slate-200 uppercase tracking-wider inline-block">
                    {{ $reimbursement->category ?? $reimbursement->type ?? 'Umum' }}
                </span>
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Tanggal Pengajuan</p>
                <p class="text-xs sm:text-sm font-bold text-slate-700 flex items-center gap-1.5">
                    <i class="far fa-calendar-alt text-indigo-400"></i> 
                    {{ optional($reimbursement->date ?? $reimbursement->created_at)->format('d M Y') }}
                </p>
            </div>
        </div>

        <div class="border-t border-slate-100 pt-6 relative z-10">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Nominal Pengajuan</p>
            <p class="text-2xl sm:text-3xl font-black text-indigo-600 tracking-tight">{{ $reimbursement->amount_rupiah ?? 'Rp ' . number_format($reimbursement->amount ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- [FITUR TAMBAHAN BARU] Kotak Alasan Penolakan dari Admin --}}
    @if(strtolower($reimbursement->status) == 'rejected' && ($reimbursement->rejection_reason || $reimbursement->admin_note))
    <div class="bg-rose-50 rounded-[2rem] p-6 border border-rose-100 shadow-sm space-y-2">
        <div class="flex items-center gap-2 text-rose-600 font-black text-xs uppercase tracking-wider">
            <i class="fas fa-exclamation-circle text-base"></i> Alasan Penolakan Pengajuan
        </div>
        <p class="text-xs sm:text-sm font-medium text-rose-900 leading-relaxed pl-6">
            {{ $reimbursement->rejection_reason ?? $reimbursement->admin_note }}
        </p>
    </div>
    @endif

    {{-- Catatan Admin Umum (Jika Ada) --}}
    @if($reimbursement->admin_note && strtolower($reimbursement->status) !== 'rejected')
    <div class="bg-amber-50 rounded-[2rem] p-6 border border-amber-100 shadow-sm space-y-2">
        <div class="flex items-center gap-2 text-amber-600 font-black text-xs uppercase tracking-wider">
            <i class="fas fa-info-circle text-base"></i> Catatan Admin
        </div>
        <p class="text-xs sm:text-sm font-medium text-amber-900 leading-relaxed pl-6">
            {{ $reimbursement->admin_note }}
        </p>
    </div>
    @endif

    {{-- Bukti Foto / Struk --}}
    <div class="bg-white rounded-[2rem] p-6 sm:p-8 shadow-sm border border-slate-100 space-y-4">
        <div class="flex items-center justify-between">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Lampiran Bukti Struk / Nota</p>
            @if($reimbursement->proof_file || $reimbursement->attachment)
            <a href="{{ asset('storage/' . ($reimbursement->proof_file ?? $reimbursement->attachment)) }}" target="_blank" class="text-[10px] font-black text-indigo-600 hover:underline uppercase tracking-wider flex items-center gap-1">
                <i class="fas fa-external-link-alt"></i> Buka Gambar Asli
            </a>
            @endif
        </div>

        @if($reimbursement->proof_file || $reimbursement->attachment)
            <div class="overflow-hidden rounded-2xl border-4 border-slate-50 shadow-md bg-slate-100">
                <img src="{{ asset('storage/' . ($reimbursement->proof_file ?? $reimbursement->attachment)) }}" class="w-full h-auto object-cover hover:scale-105 transition duration-300">
            </div>
        @else
            <div class="py-12 text-center bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300 text-xl">
                    <i class="fas fa-image"></i>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tidak ada foto bukti yang dilampirkan</p>
            </div>
        @endif
    </div>
</div>
@endsection