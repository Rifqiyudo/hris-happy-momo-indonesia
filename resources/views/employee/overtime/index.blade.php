@extends('employee.layouts.app')
@section('title', 'Riwayat Lembur')

@section('content')
<div class="p-6 pb-24">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Lembur</h1>
            <p class="text-xs text-slate-500">Riwayat pengajuan lembur Anda</p>
        </div>
        
        {{-- Tombol Tambah (Link ke Create) --}}
        <a href="{{ route('employee.overtime.create') }}" class="bg-indigo-600 text-white p-3 rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition active:scale-95">
            <i class="fas fa-plus"></i>
        </a>
    </div>

    {{-- LIST DATA LEMBUR --}}
    <div class="space-y-4">
        
        @forelse($overtimes as $item)
            {{-- UPDATE: Div diganti menjadi Link (<a>) agar bisa diklik ke halaman Detail --}}
            <a href="{{ route('employee.overtime.show', $item->id) }}" class="block bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex flex-col gap-3 hover:bg-gray-50 transition">
                
                {{-- Baris Atas: Tanggal & Status --}}
                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">
                                {{ \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y') }}
                            </p>
                            <p class="text-[10px] text-slate-500">
                                {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($item->end_time)->format('H:i') }}
                            </p>
                        </div>
                    </div>

                    {{-- Badge Status --}}
                    @php
                        $statusColor = 'bg-yellow-100 text-yellow-600'; // Default Pending
                        $statusText = 'Menunggu';

                        if($item->status == 'approved') {
                            $statusColor = 'bg-green-100 text-green-600';
                            $statusText = 'Disetujui';
                        } elseif($item->status == 'rejected') {
                            $statusColor = 'bg-red-100 text-red-600';
                            $statusText = 'Ditolak';
                        }
                    @endphp
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold {{ $statusColor }}">
                        {{ $statusText }}
                    </span>
                </div>

                <div class="border-t border-gray-50 my-1"></div>

                {{-- Baris Bawah: Alasan & Durasi --}}
                <div class="flex justify-between items-center">
                    <p class="text-xs text-slate-600 italic line-clamp-1">
                        "{{ $item->reason }}"
                    </p>
                    <div class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-lg">
                        {{ $item->duration }} Jam
                    </div>
                </div>

            </a>
        @empty
            {{-- TAMPILAN JIKA KOSONG --}}
            <div class="text-center py-10">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                    <i class="fas fa-business-time text-3xl"></i>
                </div>
                <h3 class="text-slate-800 font-bold">Belum Ada Lembur</h3>
                <p class="text-xs text-slate-500 mt-1">Anda belum pernah mengajukan lembur bulan ini.</p>
            </div>
        @endforelse

    </div>
</div>
@endsection