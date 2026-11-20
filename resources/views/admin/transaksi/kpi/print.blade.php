<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan KPI - {{ $kpi->user->name }} - {{ $kpi->period_label }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: white; color: black; }
        @media print {
            @page { size: A4; margin: 20mm; }
            .no-print { display: none !important; }
            .print-shadow-none { shadow: none !important; border: 1px solid #eee !important; }
        }
    </style>
</head>
<body class="p-4 md:p-10">
    
    {{-- Tombol Navigasi (Hilang saat diprint) --}}
    <div class="no-print flex justify-between items-center mb-10 bg-slate-50 p-4 rounded-2xl border border-slate-100">
        <a href="{{ route('admin.transaksi.kpi.show', $kpi->id) }}" class="text-sm font-bold text-slate-500 hover:text-indigo-600 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Detail
        </a>
        <button onclick="window.print()" class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-indigo-200">
            Cetak Sekarang
        </button>
    </div>

    {{-- KOP SURAT --}}
    <div class="flex justify-between items-start border-b-4 border-slate-900 pb-6 mb-8">
        <div>
            <h1 class="text-2xl font-black uppercase tracking-tighter">PT. Happy Momo Indonesia</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">Laporan Penilaian Kinerja Karyawan (KPI)</p>
            <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-4">Periode Penilaian: <span class="text-slate-900 font-bold">{{ $kpi->period_label }}</span></p>
        </div>
        <div class="text-right">
            <div class="inline-block p-4 border-2 border-slate-900 rounded-2xl text-center">
                <p class="text-[10px] font-black uppercase mb-1">Grade Akhir</p>
                <h2 class="text-4xl font-black text-slate-900">{{ $kpi->grade }}</h2>
            </div>
        </div>
    </div>

    {{-- INFORMASI KARYAWAN --}}
    <div class="grid grid-cols-2 gap-8 mb-10 bg-slate-50 p-6 rounded-2xl border border-slate-100">
        <div>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Nama Karyawan</p>
            <p class="font-bold text-slate-900 uppercase">{{ $kpi->user->name }}</p>
            <p class="text-xs text-slate-600 mt-1">NIK: {{ $kpi->user->nik }}</p>
        </div>
        <div class="text-right">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Departemen / Jabatan</p>
            <p class="font-bold text-slate-900 uppercase">{{ $kpi->user->department->name ?? '-' }}</p>
            <p class="text-xs text-slate-600 mt-1">{{ $kpi->user->position->name ?? '-' }}</p>
        </div>
    </div>

    {{-- TABEL NILAI --}}
    <div class="mb-10">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-900 text-white">
                    <th class="p-4 text-[10px] font-black uppercase tracking-widest rounded-tl-xl">Kriteria Indikator</th>
                    <th class="p-4 text-[10px] font-black uppercase tracking-widest text-center">Bobot</th>
                    <th class="p-4 text-[10px] font-black uppercase tracking-widest text-center">Nilai</th>
                    <th class="p-4 text-[10px] font-black uppercase tracking-widest text-right rounded-tr-xl">Hasil Terbobot</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 border-x border-b border-slate-200 font-medium">
                @foreach($kpi->score_details as $detail)
                <tr>
                    <td class="p-4 text-xs uppercase">{{ $detail['indicator_name'] }}</td>
                    <td class="p-4 text-xs text-center">{{ $detail['weight'] }}%</td>
                    <td class="p-4 text-xs text-center">{{ $detail['score'] }}</td>
                    <td class="p-4 text-xs text-right font-bold text-slate-900">{{ number_format($detail['weighted_result'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-slate-50 font-black">
                    <td colspan="3" class="p-4 text-right text-[10px] uppercase tracking-widest border-l border-b border-slate-200">Total Skor Akhir</td>
                    <td class="p-4 text-right text-xl text-slate-900 border-r border-b border-slate-200">{{ number_format($kpi->final_score, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- FEEDBACK --}}
    <div class="mb-16">
        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3">Catatan & Feedback Atasan</p>
        <div class="p-6 border-2 border-dashed border-slate-200 rounded-2xl italic text-slate-600 text-sm leading-relaxed">
            "{{ $kpi->feedback ?? 'Tidak ada catatan tambahan untuk periode ini.' }}"
        </div>
    </div>

    {{-- TANDA TANGAN --}}
    <div class="grid grid-cols-2 gap-20">
        <div class="text-center">
            <p class="text-[10px] font-black uppercase tracking-widest mb-20 text-slate-400">Karyawan Bersangkutan</p>
            <div class="w-48 h-px bg-slate-900 mx-auto"></div>
            <p class="mt-2 font-bold text-xs uppercase">{{ $kpi->user->name }}</p>
        </div>
        <div class="text-center">
            <p class="text-[10px] font-black uppercase tracking-widest mb-20 text-slate-400">Penilai (Reviewer)</p>
            <div class="w-48 h-px bg-slate-900 mx-auto"></div>
            <p class="mt-2 font-bold text-xs uppercase">{{ $kpi->reviewer->name }}</p>
            <p class="text-[9px] font-medium text-slate-500 italic">Dicetak pada: {{ date('d/m/Y H:i') }}</p>
        </div>
    </div>

</body>
</html>