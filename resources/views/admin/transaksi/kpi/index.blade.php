@extends('admin.layouts.master')
@section('title', 'Monitoring KPI Karyawan')

@section('content')
<div class="space-y-6 animate-fadeIn">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Monitoring KPI</h1>
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mt-1">Rekapitulasi Penilaian Kinerja Karyawan</p>
        </div>
        <a href="{{ route('admin.transaksi.kpi.create') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Input Penilaian Baru
        </a>
    </div>

    {{-- Stats Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Penilaian</p>
            <h3 class="text-2xl font-black text-slate-800 mt-1">{{ $kpis->total() }}</h3>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Rata-rata Skor</p>
            <h3 class="text-2xl font-black text-indigo-600 mt-1">{{ number_format($kpis->avg('final_score'), 2) }}</h3>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Periode Aktif</p>
            <h3 class="text-2xl font-black text-slate-800 mt-1">{{ $month }}/{{ $year }}</h3>
        </div>
    </div>

    {{-- Filter Periode --}}
    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
        <form action="{{ route('admin.transaksi.kpi.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="w-full md:w-48">
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 ml-1">Bulan</label>
                <select name="month" class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-xs font-bold focus:ring-2 focus:ring-indigo-500">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full md:w-32">
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 ml-1">Tahun</label>
                <input type="number" name="year" value="{{ $year }}" class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-xs font-bold focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit" class="bg-slate-800 text-white px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-900 transition">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
        </form>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Karyawan</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Periode</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Skor Akhir</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($kpis as $kpi)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <img src="{{ $kpi->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($kpi->user->name) }}" class="w-10 h-10 rounded-2xl object-cover shadow-sm">
                                <div>
                                    <p class="text-xs font-black text-slate-800 uppercase">{{ $kpi->user->name }}</p>
                                    <p class="text-[9px] font-bold text-slate-400">{{ $kpi->user->nik }} • {{ $kpi->user->position->name ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center text-xs font-black text-slate-600 uppercase">{{ $kpi->period_label }}</td>
                        <td class="px-6 py-5 text-center"><span class="px-3 py-1 rounded-lg bg-indigo-50 text-indigo-600 text-xs font-black">{{ number_format($kpi->final_score, 2) }}</span></td>
                        <td class="px-6 py-5 text-center">{!! $kpi->status_html !!}</td>
                        <td class="px-6 py-5 text-right">
                            <a href="{{ route('admin.transaksi.kpi.show', $kpi->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-50 hover:bg-indigo-600 hover:text-white transition">
                                <i class="fas fa-eye text-[10px]"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-20 text-center">
                            <div class="flex flex-col items-center text-slate-300">
                                <i class="fas fa-chart-line text-4xl mb-3 opacity-20"></i>
                                <p class="text-[10px] font-black uppercase tracking-widest">Belum ada data penilaian periode ini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($kpis->hasPages())
        <div class="p-6 border-t border-slate-100">
            {{ $kpis->links() }}
        </div>
        @endif
    </div>
</div>
@endsection