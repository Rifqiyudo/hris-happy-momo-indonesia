@extends('admin.layouts.master')
@section('title', 'Koreksi Penilaian KPI')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12">
    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.transaksi.kpi.index') }}" class="w-10 h-10 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-indigo-600 transition shadow-sm">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Edit Penilaian</h1>
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mt-1">Koreksi performa periode {{ $kpi->period_label }}</p>
        </div>
    </div>

    <form action="{{ route('admin.transaksi.kpi.update', $kpi->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            {{-- Info Karyawan (Read Only) --}}
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm md:col-span-1 text-center">
                <img src="{{ $kpi->user->avatar_url }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($kpi->user->name) }}&background=6366f1&color=fff&bold=true'" 
                     class="w-20 h-20 rounded-[1.5rem] mx-auto mb-4 object-cover border-4 border-slate-50">
                
                <h3 class="text-sm font-black text-slate-800 uppercase leading-tight">{{ $kpi->user->name }}</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $kpi->user->nik }}</p>
                
                <input type="hidden" name="user_id" value="{{ $kpi->user_id }}">
                <input type="hidden" name="month" value="{{ $kpi->period_month }}">
                <input type="hidden" name="year" value="{{ $kpi->period_year }}">

                <div class="mt-6 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Skor Saat Ini</p>
                    <p class="text-2xl font-black text-indigo-600">{{ number_format($kpi->final_score, 2) }}</p>
                    <span class="text-[10px] font-black text-white bg-slate-800 px-3 py-1 rounded-full uppercase">Grade {{ $kpi->grade }}</span>
                </div>
            </div>

            {{-- Input Nilai --}}
            <div class="md:col-span-2 space-y-4">
                @php 
                    // Mengonversi score_details ke array key-value untuk memudahkan pencarian nilai lama
                    $oldScores = collect($kpi->score_details)->pluck('score', 'indicator_id')->toArray();
                @endphp

                @foreach($indicators as $ind)
                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center justify-between gap-6 hover:border-indigo-200 transition-all group">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h4 class="text-xs font-black text-slate-800 uppercase">{{ $ind->name }}</h4>
                            <span class="text-[10px] font-black text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded-lg">{{ $ind->weight }}%</span>
                        </div>
                        <p class="text-[10px] text-slate-400 italic leading-tight">{{ $ind->description }}</p>
                    </div>
                    <div class="w-24">
                        <input type="number" name="scores[{{ $ind->id }}]" 
                               value="{{ $oldScores[$ind->id] ?? 0 }}" 
                               required min="0" max="100" 
                               class="w-full bg-slate-50 border-none rounded-xl py-3 px-4 text-center font-black text-indigo-600 focus:ring-2 focus:ring-indigo-500 group-hover:bg-white transition-all" 
                               placeholder="0-100">
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl">
            <label class="block text-[10px] font-black text-slate-400 uppercase mb-4 tracking-widest">Feedback / Catatan Perbaikan</label>
            <textarea name="feedback" rows="4" class="w-full bg-slate-50 border-none rounded-[1.5rem] py-4 px-6 text-xs font-bold focus:ring-2 focus:ring-indigo-500" 
                      placeholder="Berikan alasan koreksi atau catatan performa...">{{ $kpi->feedback }}</textarea>
            
            <div class="flex justify-end mt-8 gap-3">
                <a href="{{ route('admin.transaksi.kpi.index') }}" class="px-8 py-4 text-[10px] font-black uppercase text-slate-400 hover:text-slate-600 transition">Batal</a>
                <button type="submit" class="bg-indigo-600 text-white px-10 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition transform active:scale-95">
                    Update Penilaian
                </button>
            </div>
        </div>
    </form>
</div>
@endsection