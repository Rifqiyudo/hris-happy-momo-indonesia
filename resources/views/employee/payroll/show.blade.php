@extends('employee.layouts.app')

@section('title', 'Detail Slip Gaji')

@section('content')
<div class="p-4 pb-24 max-w-2xl mx-auto space-y-6">

    {{-- TOMBOL NAVIGASI (HILANG SAAT DI-PRINT) --}}
    <div class="flex justify-between items-center print:hidden">
        <a href="{{ route('employee.payroll.index') }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-100 rounded-2xl shadow-sm hover:bg-slate-50 text-slate-500 transition">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <button onclick="window.print()" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 text-white text-xs font-black uppercase tracking-wider rounded-2xl shadow-lg shadow-indigo-200 flex items-center gap-2 hover:opacity-95 transition active:scale-95">
            <i class="fas fa-print text-sm"></i> Cetak / Simpan PDF
        </button>
    </div>

    {{-- KERTAS SLIP GAJI (Desain Modern & Estetik) --}}
    <div class="bg-white p-6 sm:p-10 rounded-[2.5rem] shadow-sm border border-slate-100 space-y-8 relative overflow-hidden print:shadow-none print:border-0 print:p-0">
        
        {{-- Aksen Dekoratif Background (Hidden saat print) --}}
        <div class="absolute right-0 top-0 w-48 h-48 bg-indigo-50/60 rounded-bl-full pointer-events-none print:hidden"></div>

        {{-- KOP SURAT & LOGO PERUSAHAAN --}}
        <div class="flex flex-col sm:flex-row items-center sm:items-start justify-between gap-4 border-b border-slate-100 pb-6 relative z-10 text-center sm:text-left">
            <div class="flex items-center gap-4">
                {{-- Logo Perusahaan (Inisial Elegan) --}}
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-700 text-white flex items-center justify-center font-black text-xl shadow-lg shadow-indigo-100 shrink-0">
                    HM
                </div>
                <div>
                    <h1 class="text-base sm:text-lg font-black text-slate-900 tracking-tight uppercase">PT. Happy Momo Indonesia</h1>
                    <p class="text-[10px] font-bold text-slate-400 tracking-wider mt-0.5">JL. PERGUDANGAN MARGOMULYO PERMAI, SURABAYA</p>
                </div>
            </div>
            
            <div class="sm:text-right">
                <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-600 rounded-xl text-[10px] font-black uppercase tracking-wider border border-indigo-100">
                    Slip Gaji Resmi
                </span>
                <p class="text-xs font-black text-slate-700 mt-2 uppercase tracking-tight">
                    Periode: {{ \Carbon\Carbon::createFromDate(null, $payroll->month)->translatedFormat('F') }} {{ $payroll->year }}
                </p>
            </div>
        </div>

        {{-- INFO KARYAWAN --}}
        <div class="bg-slate-50/70 rounded-2xl p-5 border border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs relative z-10">
            <div class="space-y-2">
                <div class="flex justify-between sm:justify-start gap-4">
                    <span class="font-bold text-slate-400 w-24">Nama Karyawan</span>
                    <span class="font-black text-slate-800">: {{ $user->name }}</span>
                </div>
                <div class="flex justify-between sm:justify-start gap-4">
                    <span class="font-bold text-slate-400 w-24">NIK</span>
                    <span class="font-black text-slate-800">: {{ $user->nik ?? '-' }}</span>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between sm:justify-start gap-4">
                    <span class="font-bold text-slate-400 w-24">Jabatan</span>
                    <span class="font-black text-slate-800">: {{ $user->position->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between sm:justify-start gap-4">
                    <span class="font-bold text-slate-400 w-24">Cabang</span>
                    <span class="font-black text-slate-800">: {{ $user->branch->name ?? 'Pusat' }}</span>
                </div>
            </div>
        </div>

        {{-- TABEL RINCIAN --}}
        <div class="space-y-5 relative z-10">
            
            {{-- PEMASUKAN (EARNINGS) --}}
            <div class="space-y-2">
                <div class="flex items-center justify-between text-[10px] font-black text-emerald-600 uppercase tracking-widest px-1">
                    <span><i class="fas fa-arrow-down mr-1"></i> Pendapatan</span>
                    <span>Jumlah</span>
                </div>
                <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden divide-y divide-slate-50 text-xs shadow-sm">
                    <div class="flex justify-between items-center px-5 py-3.5">
                        <span class="font-medium text-slate-700">Gaji Pokok</span>
                        <span class="font-black text-slate-800">Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center px-5 py-3.5">
                        <span class="font-medium text-slate-700">Tunjangan</span>
                        <span class="font-black text-slate-800">Rp {{ number_format($payroll->total_allowance, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center px-5 py-3.5">
                        <span class="font-medium text-slate-700">Lembur</span>
                        <span class="font-black text-slate-800">Rp {{ number_format($payroll->overtime_pay, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- POTONGAN (DEDUCTIONS) --}}
            <div class="space-y-2">
                <div class="flex items-center justify-between text-[10px] font-black text-rose-600 uppercase tracking-widest px-1">
                    <span><i class="fas fa-arrow-up mr-1"></i> Potongan</span>
                    <span>Jumlah</span>
                </div>
                <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden divide-y divide-slate-50 text-xs shadow-sm">
                    <div class="flex justify-between items-center px-5 py-3.5">
                        <span class="font-medium text-slate-700">Keterlambatan / Absen / Lainnya</span>
                        <span class="font-black text-rose-500">- Rp {{ number_format($payroll->deductions, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- TOTAL GAJI BERSIH (TAKE HOME PAY) --}}
        <div class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-violet-700 rounded-2xl p-6 text-white shadow-lg shadow-indigo-100 flex flex-col sm:flex-row items-center justify-between gap-4 relative z-10">
            <div>
                <span class="text-[10px] font-black uppercase tracking-widest opacity-80 block mb-0.5">Total Gaji Bersih</span>
                <h3 class="text-sm font-bold tracking-tight">TAKE HOME PAY</h3>
            </div>
            <div class="text-2xl sm:text-3xl font-black tracking-tight">
                Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}
            </div>
        </div>

        {{-- Keterangan Transfer Bank --}}
        <div class="text-center italic text-xs text-slate-400 bg-slate-50 py-3 px-4 rounded-xl border border-slate-100 relative z-10">
            *Gaji ditransfer ke rekening <span class="font-bold text-slate-600">{{ $user->bank_name ?? 'Bank' }} - {{ $user->bank_account ?? '...' }}</span>
        </div>

        {{-- FOOTER TTD --}}
        <div class="grid grid-cols-2 gap-8 pt-6 border-t border-slate-100 text-center text-xs relative z-10">
            <div>
                <p class="font-bold text-slate-400 mb-16">Penerima,</p>
                <p class="font-black text-slate-800 uppercase tracking-tight underline decoration-indigo-300 underline-offset-4">{{ $user->name }}</p>
            </div>
            <div>
                <p class="font-bold text-slate-400 mb-16">
                    Surabaya, {{ isset($payroll->generated_date) ? \Carbon\Carbon::parse($payroll->generated_date)->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}<br>
                    Finance Dept,
                </p>
                <p class="font-black text-slate-800 uppercase tracking-tight underline decoration-indigo-300 underline-offset-4">PT. HAPPY MOMO INDONESIA</p>
            </div>
        </div>

    </div>
</div>

{{-- STYLE KHUSUS PRINT --}}
<style>
    @media print {
        body { background: white !important; }
        nav, header, footer, .pb-24, .print\:hidden { display: none !important; }
        .print\:shadow-none { box-shadow: none !important; }
        .print\:border-0 { border: none !important; }
        .print\:p-0 { padding: 0 !important; }
    }
</style>
@endsection