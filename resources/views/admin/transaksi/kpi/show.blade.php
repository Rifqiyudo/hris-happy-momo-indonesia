@extends('admin.layouts.master')
@section('title', 'Detail Penilaian KPI')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12">
    {{-- Header & Back Button --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            {{-- Perbaikan Link Back --}}
            <a href="{{ route('admin.transaksi.kpi.index') }}" class="w-10 h-10 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-indigo-600 transition shadow-sm">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Rincian KPI</h1>
                <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mt-1">Periode: {{ $kpi->period_label }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            {!! $kpi->status_html !!}
            {{-- FIX: Menggunakan rute lengkap admin.transaksi.kpi.print --}}
            <a href="{{ route('admin.transaksi.kpi.print', $kpi->id) }}" target="_blank" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-900 transition flex items-center gap-2">
                <i class="fas fa-print text-xs"></i> Cetak Laporan
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Profile Card --}}
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm text-center">
                <div class="relative inline-block mb-4">
                    <img src="{{ $kpi->user->avatar_url }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($kpi->user->name) }}&background=6366f1&color=fff&bold=true'" 
                         class="w-24 h-24 rounded-[2rem] object-cover border-4 border-slate-50 shadow-md">
                    <div class="absolute -bottom-2 -right-2">
                        {!! $kpi->grade_label !!}
                    </div>
                </div>
                <h3 class="font-black text-slate-800 uppercase text-sm leading-tight">{{ $kpi->user->name }}</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $kpi->user->nik }}</p>
                
                <div class="mt-4 pt-4 border-t border-slate-50 flex flex-col gap-2">
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter leading-none">Departemen</p>
                        <p class="text-[11px] font-bold text-slate-700 uppercase">{{ $kpi->user->department->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter leading-none">Jabatan</p>
                        <p class="text-[11px] font-bold text-slate-700 uppercase">{{ $kpi->user->position->name ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Summary Score Card --}}
            <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-xl shadow-slate-200 text-white relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Skor Akhir Performa</p>
                    <h2 class="text-5xl font-black">{{ number_format($kpi->final_score, 2) }}</h2>
                    <div class="mt-6 flex items-center gap-2">
                        <div class="h-2 flex-1 bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-500 transition-all duration-1000" style="width: {{ $kpi->final_score }}%"></div>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest">Grade {{ $kpi->grade }}</span>
                    </div>
                </div>
                <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-indigo-500/20 rounded-full blur-3xl group-hover:bg-indigo-500/30 transition-all"></div>
            </div>
        </div>

        {{-- Details Table Card --}}
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden text-slate-800">
                <div class="p-6 border-b border-slate-50 bg-slate-50/50 flex items-center justify-between">
                    <h3 class="text-[10px] font-black uppercase tracking-widest">Breakdown Indikator Penilaian</h3>
                    <i class="fas fa-layer-group text-slate-300"></i>
                </div>
                <div class="p-0">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                                <th class="px-6 py-4">Kriteria</th>
                                <th class="px-6 py-4 text-center">Bobot</th>
                                <th class="px-6 py-4 text-center">Nilai</th>
                                <th class="px-6 py-4 text-right">Hasil Terbobot</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-[11px] font-bold text-slate-700">
                            @foreach($kpi->score_details as $detail)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-5 uppercase tracking-tighter leading-tight">{{ $detail['indicator_name'] }}</td>
                                <td class="px-6 py-5 text-center">
                                    <span class="text-slate-400">{{ $detail['weight'] }}%</span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="px-3 py-1 bg-slate-100 rounded-lg">{{ $detail['score'] }}</span>
                                </td>
                                <td class="px-6 py-5 text-right font-black text-indigo-600">
                                    {{ number_format($detail['weighted_result'] ?? (($detail['score'] * $detail['weight']) / 100), 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-indigo-50/50 border-t border-indigo-100">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-[10px] font-black text-indigo-900 uppercase text-right tracking-widest">Total Skor Terhitung</td>
                                <td class="px-6 py-4 text-right text-lg font-black text-indigo-600">{{ number_format($kpi->final_score, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Feedback Card --}}
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm text-slate-800">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-xs">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <h3 class="text-[10px] font-black uppercase tracking-widest">Evaluasi & Feedback Atasan</h3>
                </div>
                <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 italic text-slate-600 text-sm leading-relaxed">
                    "{{ $kpi->feedback ?? 'Tidak ada catatan tambahan untuk periode ini.' }}"
                </div>
                <div class="mt-6 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center border border-indigo-100">
                        <img src="{{ $kpi->reviewer->avatar_url }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($kpi->reviewer->name) }}&background=6366f1&color=fff&bold=true'" 
                             class="w-full h-full object-cover rounded-xl grayscale group-hover:grayscale-0 transition-all">
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase leading-none">{{ $kpi->reviewer->name }}</p>
                        <p class="text-[8px] font-bold text-slate-400 uppercase mt-1 italic tracking-widest">Penilai Terverifikasi (Reviewer)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .transition-all { transition: all 0.5s ease; }
</style>
@endsection