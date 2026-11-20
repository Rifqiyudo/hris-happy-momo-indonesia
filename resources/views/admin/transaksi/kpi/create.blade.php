@extends('admin.layouts.master')
@section('title', 'Input Penilaian KPI')

@section('content')
<div class="max-w-6xl mx-auto pb-20 animate-fadeIn">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Input Penilaian KPI</h1>
        <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mt-1">Evaluasi Capaian Target Karyawan Periode Ini</p>
    </div>

    <form action="{{ route('admin.transaksi.kpi.store') }}" method="POST" id="kpiForm">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- 1. Sidebar Meta Data --}}
            <div class="lg:col-span-1">
                <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm sticky top-6 space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-4 tracking-widest text-center">Pilih Karyawan</label>
                        <select name="user_id" required class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-xs font-bold focus:ring-2 focus:ring-indigo-500 transition-all">
                            <option value="">-- Cari Karyawan --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->nik }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-4 tracking-widest text-center">Periode Penilaian</label>
                        <div class="grid grid-cols-2 gap-3">
                            <select name="month" class="bg-slate-50 border-none rounded-2xl py-3 px-3 text-[10px] font-black uppercase text-center focus:ring-2 focus:ring-indigo-500">
                                @foreach(range(1,12) as $m)
                                    <option value="{{ sprintf('%02d', $m) }}" {{ date('m') == $m ? 'selected' : '' }}>
                                        {{ date('F', mktime(0,0,0,$m,1)) }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="number" name="year" value="{{ date('Y') }}" class="bg-slate-50 border-none rounded-2xl py-3 px-3 text-[10px] font-black uppercase text-center focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    {{-- Live Score Preview --}}
                    <div class="pt-6 border-t border-slate-100 text-center">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Total Skor Akhir</p>
                        <div id="totalScore" class="text-4xl font-black text-indigo-600">0.00</div>
                    </div>
                </div>
            </div>

            {{-- 2. Daftar Indikator --}}
            <div class="lg:col-span-2 space-y-4">
                @foreach($indicators as $ind)
                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center justify-between gap-6 hover:border-indigo-200 transition-all group">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h4 class="text-xs font-black text-slate-800 uppercase">{{ $ind->name }}</h4>
                            <span class="text-[9px] font-black text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-100 weight-badge" data-weight="{{ $ind->weight }}">{{ $ind->weight }}%</span>
                        </div>
                        <p class="text-[10px] text-slate-400 italic leading-tight">{{ $ind->description }}</p>
                    </div>
                    <div class="w-24">
                        <input type="number" name="scores[{{ $ind->id }}]" required min="0" max="100" 
                               class="kpi-input w-full bg-slate-50 border-none rounded-xl py-3 px-4 text-center font-black text-indigo-600 focus:ring-2 focus:ring-indigo-500 transition-all" 
                               placeholder="0-100">
                    </div>
                </div>
                @endforeach

                {{-- Feedback Area --}}
                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm mt-6">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-4 tracking-widest">Catatan Evaluasi / Feedback</label>
                    <textarea name="feedback" rows="4" class="w-full bg-slate-50 border-none rounded-[1.5rem] py-4 px-6 text-xs font-bold focus:ring-2 focus:ring-indigo-500" placeholder="Tuliskan catatan pengembangan untuk karyawan..."></textarea>
                    
                    <div class="flex justify-end gap-4 mt-8">
                        <a href="{{ route('admin.transaksi.kpi.index') }}" class="px-8 py-4 text-[10px] font-black uppercase text-slate-400 hover:text-slate-600 transition">Batal</a>
                        <button type="submit" class="bg-emerald-500 text-white px-10 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-emerald-100 hover:bg-emerald-600 transition flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> Simpan & Publish Penilaian
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    const inputs = document.querySelectorAll('.kpi-input');
    const display = document.getElementById('totalScore');

    inputs.forEach(input => {
        input.addEventListener('input', calculateTotal);
    });

    function calculateTotal() {
        let total = 0;
        inputs.forEach(input => {
            const val = parseFloat(input.value) || 0;
            const weight = parseFloat(input.parentElement.parentElement.querySelector('.weight-badge').dataset.weight) || 0;
            total += (val * (weight / 100));
        });
        display.innerText = total.toFixed(2);
    }
</script>
@endsection