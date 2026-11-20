@extends('employee.layouts.app')
@section('title', 'Riwayat Absensi')

@section('content')
<div class="page-fade space-y-6 pb-24 px-2">
    
    {{-- TOP NAVIGATION --}}
    <div class="flex items-center gap-4 py-2">
        <a href="{{ route('employee.dashboard') }}" 
           class="w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-sm border border-gray-100 text-slate-500 active:scale-95 transition">
            <i class="fas fa-chevron-left text-xs"></i>
        </a>
        <h1 class="text-lg font-black text-slate-800 tracking-tight">Riwayat Absensi</h1>
    </div>

    {{-- STATS SUMMARY --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white p-5 rounded-[2rem] border border-gray-50 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Hadir</p>
            <p class="text-xl font-black text-slate-800">{{ $history->total() }} <span class="text-[10px] text-slate-400">Hari</span></p>
        </div>
        <div class="bg-white p-5 rounded-[2rem] border border-gray-50 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Terlambat</p>
            <p class="text-xl font-black text-rose-500">{{ $history->where('is_late', true)->count() }} <span class="text-[10px] text-slate-400">Kali</span></p>
        </div>
    </div>

    {{-- LIST HISTORY --}}
    <div class="space-y-4">
        @forelse($history as $item)
        {{-- Area Klik Full Card menuju Detail --}}
        <a href="{{ route('employee.history.show', $item->id) }}" class="block group">
            <div class="bg-white p-5 rounded-[2.5rem] shadow-[0_10px_40px_rgba(0,0,0,0.03)] border border-gray-50 flex items-center justify-between active:scale-[0.97] transition-all duration-200">
                <div class="flex items-center gap-5">
                    {{-- Date Box --}}
                    <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex flex-col items-center justify-center border border-indigo-100/50 group-hover:bg-indigo-600 transition-colors duration-300">
                        <span class="text-sm font-black text-indigo-600 leading-none group-hover:text-white">{{ \Carbon\Carbon::parse($item->date)->format('d') }}</span>
                        <span class="text-[9px] font-bold text-indigo-400 uppercase tracking-tighter group-hover:text-indigo-100">{{ \Carbon\Carbon::parse($item->date)->translatedFormat('M') }}</span>
                    </div>
                    
                    {{-- Attendance Info --}}
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-1.5 text-emerald-500">
                                <i class="fas fa-sign-in-alt text-[10px]"></i>
                                <span class="text-xs font-black">{{ \Carbon\Carbon::parse($item->clock_in)->format('H:i') }}</span>
                            </div>
                            <div class="w-1 h-1 rounded-full bg-slate-200"></div>
                            <div class="flex items-center gap-1.5 {{ $item->clock_out ? 'text-indigo-500' : 'text-slate-300' }}">
                                <i class="fas fa-sign-out-alt text-[10px]"></i>
                                <span class="text-xs font-black">{{ $item->clock_out ? \Carbon\Carbon::parse($item->clock_out)->format('H:i') : '--:--' }}</span>
                            </div>
                        </div>
                        
                        {{-- Status Badge --}}
                        @if($item->is_late)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-rose-50 text-rose-600 rounded-lg text-[9px] font-black uppercase tracking-wider border border-rose-100">
                                <i class="fas fa-clock text-[8px]"></i> Terlambat
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[9px] font-black uppercase tracking-wider border border-emerald-100">
                                <i class="fas fa-check-circle text-[8px]"></i> Tepat Waktu
                            </span>
                        @endif
                    </div>
                </div>
                
                {{-- Arrow Icon --}}
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-50 text-slate-300 group-hover:bg-indigo-50 group-hover:text-indigo-500 transition-colors">
                    <i class="fas fa-chevron-right text-[10px]"></i>
                </div>
            </div>
        </a>
        @empty
        {{-- Empty State --}}
        <div class="flex flex-col items-center justify-center py-20 bg-white rounded-[2.5rem] border border-dashed border-slate-200">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-calendar-times text-3xl text-slate-200"></i>
            </div>
            <h3 class="text-slate-800 font-bold text-sm">Belum ada riwayat</h3>
            <p class="text-slate-400 text-[10px] mt-1 uppercase tracking-widest">Data absensi Anda akan muncul di sini</p>
        </div>
        @endforelse

        {{-- PAGINATION --}}
        <div class="pt-4 px-2">
            {{ $history->links() }}
        </div>
    </div>
</div>
@endsection