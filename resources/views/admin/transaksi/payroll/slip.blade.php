@extends('admin.layouts.master')

@php
    // KALKULASI PEMISAHAN PENCAIRAN DANA
    $transferTanggalLima = $payroll->bonus_amount ?? 0;
    // Pencairan akhir bulan adalah total Take Home Pay dikurangi Bonus yang akan cair tgl 5
    $transferAkhirBulan = ($payroll->net_salary ?? 0) - $transferTanggalLima;

    function terbilang($angka) {
        $angka = abs((int)$angka);
        $huruf = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        $hasil = "";
        if ($angka < 12) $hasil = " " . $huruf[$angka];
        elseif ($angka < 20) $hasil = terbilang($angka - 10) . " Belas";
        elseif ($angka < 100) $hasil = terbilang($angka / 10) . " Puluh" . terbilang($angka % 10);
        elseif ($angka < 200) $hasil = " Seratus" . terbilang($angka - 100);
        elseif ($angka < 1000) $hasil = terbilang($angka / 100) . " Ratus" . terbilang($angka % 100);
        elseif ($angka < 2000) $hasil = " Seribu" . terbilang($angka - 1000);
        elseif ($angka < 1000000) $hasil = terbilang($angka / 1000) . " Ribu" . terbilang($angka % 1000);
        elseif ($angka < 1000000000) $hasil = terbilang($angka / 1000000) . " Juta" . terbilang($angka % 1000000);
        return $hasil;
    }
@endphp

@section('title', 'Detail Gaji - ' . ($payroll->user->name ?? 'Karyawan'))

@section('content')
<div class="max-w-4xl mx-auto py-10 print:py-0 print:max-w-full">
    
    {{-- Tombol Aksi (Sembunyi saat Print) --}}
    <div class="flex justify-end gap-3 mb-6 print:hidden">
        <button onclick="window.print()" class="bg-white border border-slate-200 px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-50 transition shadow-sm flex items-center">
            <i class="fas fa-print mr-2 text-indigo-600"></i> Cetak Slip
        </button>

        @if(($payroll->status ?? 'draft') == 'draft')
        <form action="{{ route('admin.transaksi.payroll.publish', $payroll->id) }}" method="POST" class="inline" onsubmit="return confirm('Menerbitkan slip gaji ini secara resmi?')">
            @csrf
            <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 flex items-center">
                <i class="fas fa-paper-plane mr-2"></i> Publish
            </button>
        </form>
        @endif

        <a href="{{ route('admin.transaksi.payroll.index') }}" class="bg-slate-900 text-white px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-slate-200 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    {{-- Main Container Slip --}}
    <div class="bg-white border border-slate-100 rounded-[3rem] shadow-2xl shadow-slate-200/50 overflow-hidden print:border-none print:shadow-none print:rounded-none">
        
        {{-- Header Slip --}}
        <div class="p-12 bg-slate-50 border-b border-slate-100 flex justify-between items-center print:bg-white print:p-6">
            <div class="flex items-center gap-6">
                {{-- Logo Perusahaan --}}
                <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center p-2 border border-slate-100">
                    <img src="{{ asset('assets/img/logo-happymomo.png') }}" alt="Logo" class="w-full h-auto object-contain" onerror="this.src='https://ui-avatars.com/api/?name=Happy+Momo&background=4f46e5&color=fff'">
                </div>
                <div>
                    <h1 class="font-black text-2xl tracking-tighter text-slate-800 uppercase">Happy Momo <span class="text-indigo-600">Indonesia</span></h1>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Official Monthly Payslip</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Take Home Pay</p>
                <h2 class="font-black text-3xl text-indigo-600 tracking-tight">Rp {{ number_format($payroll->net_salary ?? 0, 0, ',', '.') }}</h2>
                <p class="text-emerald-500 font-bold text-[10px] uppercase tracking-widest mt-1">
                    @if(($payroll->status ?? 'draft') == 'published' || ($payroll->status ?? 'draft') == 'paid')
                        <i class="fas fa-check-circle mr-1"></i> Validated Document
                    @else
                        <i class="fas fa-exclamation-triangle mr-1 text-amber-500"></i> <span class="text-amber-500">Draft Document</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- Info Karyawan & Foto --}}
        <div class="p-12 flex flex-col md:flex-row gap-10 border-b border-slate-50 bg-white print:p-6">
            <div class="flex-shrink-0">
                <div class="w-28 h-28 rounded-[2rem] border-4 border-slate-50 shadow-sm overflow-hidden bg-slate-100">
                    <img src="{{ isset($payroll->user->avatar) ? asset('storage/'.$payroll->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($payroll->user->name ?? 'User').'&background=f1f5f9&color=64748b' }}" 
                         alt="Employee" 
                         class="w-full h-full object-cover">
                </div>
            </div>

            <div class="flex-1 grid grid-cols-2 md:grid-cols-3 gap-8">
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Nama Karyawan</p>
                    <p class="font-black text-slate-800 uppercase tracking-tight">{{ $payroll->user->name ?? 'Tidak Ditemukan' }}</p>
                    <p class="text-[10px] font-bold text-slate-400 mt-0.5">{{ $payroll->user->nik ?? '-' }}</p>
                </div>
                <div>
                    {{-- MENGGUNAKAN RELASI AMAN (?->) AGAR TIDAK ERROR JIKA KOSONG --}}
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Jabatan / Departemen</p>
                    <p class="font-black text-slate-800 uppercase tracking-tight">{{ $payroll->user?->position?->name ?? 'Staff' }}</p>
                    <p class="text-[10px] font-bold text-indigo-500 mt-0.5">{{ $payroll->user?->department?->name ?? 'Tidak Ada Departemen' }}</p>
                </div>
                <div class="col-span-2 md:col-span-1">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Periode Gaji</p>
                    <div class="inline-flex items-center px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg font-black text-[10px] uppercase tracking-wider">
                        {{ \Carbon\Carbon::create(null, $payroll->month ?? 1)->translatedFormat('F') }} {{ $payroll->year ?? date('Y') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Rincian Gaji --}}
        <div class="p-12 grid grid-cols-1 md:grid-cols-2 gap-16 bg-white print:p-6 print:gap-10">
            
            {{-- Kolom Pendapatan (Earnings) --}}
            <div class="space-y-6">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                    <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xs">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <h4 class="font-black text-[11px] text-slate-800 uppercase tracking-widest">Earnings / Pendapatan</h4>
                </div>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-600">Gaji Pokok</span>
                        <span class="font-black text-slate-800 text-sm">Rp {{ number_format($payroll->base_salary ?? 0, 0, ',', '.') }}</span>
                    </div>

                    @if(($payroll->position_allowance ?? 0) > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-600">Tj. Jabatan (Tetap)</span>
                        <span class="font-black text-slate-800 text-sm">Rp {{ number_format($payroll->position_allowance, 0, ',', '.') }}</span>
                    </div>
                    @endif

                    @if(($payroll->family_allowance ?? 0) > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-600">Tj. Keluarga (Tetap)</span>
                        <span class="font-black text-slate-800 text-sm">Rp {{ number_format($payroll->family_allowance, 0, ',', '.') }}</span>
                    </div>
                    @endif

                    {{-- Transparansi Harian --}}
                    @if((($payroll->meal_allowance_total ?? 0) + ($payroll->transport_allowance_total ?? 0)) > 0)
                    <div class="flex justify-between items-start pt-2">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-slate-600">Uang Makan & Transport</span>
                            <span class="text-[9px] text-indigo-500 font-black uppercase">{{ $payroll->total_days_worked ?? 0 }} Hari Kerja Aktif</span>
                        </div>
                        <span class="font-black text-slate-800 text-sm">Rp {{ number_format(($payroll->meal_allowance_total ?? 0) + ($payroll->transport_allowance_total ?? 0), 0, ',', '.') }}</span>
                    </div>
                    @endif

                    @if(($payroll->thr_amount ?? 0) > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-600">Tunjangan Hari Raya (THR)</span>
                        <span class="font-black text-slate-800 text-sm">Rp {{ number_format($payroll->thr_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif

                    @if(($payroll->bonus_amount ?? 0) > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-emerald-600">Bonus / Insentif Kinerja</span>
                        <span class="font-black text-emerald-600 text-sm">+ Rp {{ number_format($payroll->bonus_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif

                    <div class="pt-4 border-t border-dashed border-slate-200 flex justify-between items-center">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Gross Earnings (Bruto)</span>
                        <span class="font-black text-indigo-600 text-lg">Rp {{ number_format(($payroll->total_allowance ?? 0) + ($payroll->base_salary ?? 0), 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Kolom Potongan (Deductions) --}}
            <div class="space-y-6">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                    <div class="w-8 h-8 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center text-xs">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <h4 class="font-black text-[11px] text-slate-800 uppercase tracking-widest">Deductions / Potongan</h4>
                </div>

                <div class="space-y-4">
                    {{-- Potongan Alpha/Mangkir --}}
                    @if(($payroll->unpaid_deduction_amount ?? 0) > 0)
                    <div class="flex justify-between items-start">
                        <div class="flex flex-col text-rose-600">
                            <span class="text-xs font-black">Potongan Absen / Alpha</span>
                            <span class="text-[9px] font-bold uppercase italic">Kehadiran kurang</span>
                        </div>
                        <span class="font-black text-rose-600 text-sm">- Rp {{ number_format($payroll->unpaid_deduction_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif

                    {{-- PERBAIKAN: BPJS SELALU TAMPIL AGAR TRANSPARAN MESKI 0 (TANPA PERSENTASE) --}}
                    
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-600">BPJS Ketenagakerjaan (JHT)</span>
                        <span class="font-black text-rose-600 text-sm">- Rp {{ number_format($payroll->jht_deduction ?? 0, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-600">BPJS Kesehatan (Kes)</span>
                        <span class="font-black text-rose-600 text-sm">- Rp {{ number_format($payroll->kes_deduction ?? 0, 0, ',', '.') }}</span>
                    </div>

                    @if(($payroll->jp_deduction ?? 0) > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-600">BPJS Pensiun (JP)</span>
                        <span class="font-black text-rose-600 text-sm">- Rp {{ number_format($payroll->jp_deduction, 0, ',', '.') }}</span>
                    </div>
                    @endif

                    @if(($payroll->late_deduction ?? 0) > 0)
                    <div class="flex justify-between items-start">
                        <div class="flex flex-col text-rose-600">
                            <span class="text-xs font-bold">Denda Keterlambatan</span>
                            <span class="text-[9px] font-black uppercase">{{ ($payroll->late_deduction ?? 0) / 500 }} Menit Terdeteksi</span>
                        </div>
                        <span class="font-black text-rose-600 text-sm">- Rp {{ number_format($payroll->late_deduction, 0, ',', '.') }}</span>
                    </div>
                    @endif

                    {{-- POTONGAN KASBON TETAP TAMPIL JELAS --}}
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-600">Potongan Rutin / Kasbon</span>
                        <span class="font-black text-rose-600 text-sm">- Rp {{ number_format($payroll->routine_deduction ?? 0, 0, ',', '.') }}</span>
                    </div>

                    <div class="pt-4 border-t border-dashed border-slate-200 flex justify-between items-center">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Total Deductions</span>
                        <span class="font-black text-rose-600 text-lg">- Rp {{ number_format($payroll->total_deduction ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- JADWAL PENCAIRAN DANA (FITUR BARU) --}}
        <div class="mx-8 mb-6 p-6 bg-slate-50 border border-slate-100 rounded-[2rem] print:mx-4 print:mb-4">
            <h4 class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-4 flex items-center">
                <i class="fas fa-calendar-check mr-2"></i> Jadwal Pencairan Dana
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Cair Tanggal Akhir Bulan</p>
                    <p class="text-xl font-black text-slate-800">Rp {{ number_format($transferAkhirBulan, 0, ',', '.') }}</p>
                    <p class="text-[8px] font-bold text-slate-400 mt-1 italic">Gaji Pokok + Tunjangan Tetap - Total Potongan</p>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-emerald-100 shadow-sm">
                    <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest mb-1">Cair Tanggal 5 Bulan Depan</p>
                    <p class="text-xl font-black text-emerald-600">Rp {{ number_format($transferTanggalLima, 0, ',', '.') }}</p>
                    <p class="text-[8px] font-bold text-slate-400 mt-1 italic">Pendapatan Bonus / Insentif Kinerja</p>
                </div>
            </div>
        </div>

        {{-- Footer THP --}}
        <div class="mx-8 mb-8 p-10 bg-slate-900 rounded-[2.5rem] text-white flex flex-col md:flex-row justify-between items-center gap-6 print:mx-4 print:mb-4 print:rounded-[1.5rem] print:bg-slate-800 print:text-white print:border-y print:border-slate-200">
            <div>
                <p class="text-[10px] font-black text-indigo-300 uppercase tracking-[0.3em] mb-2 print:text-indigo-200">Total Net Salary (THP)</p>
                <p class="text-xs font-bold italic text-slate-400 print:text-slate-300">Terbilang: {{ ucwords(terbilang($payroll->net_salary ?? 0)) }} Rupiah</p>
            </div>
            <div class="text-right">
                <span class="text-5xl font-black text-emerald-400 tracking-tighter print:text-white">Rp {{ number_format($payroll->net_salary ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Signature Section --}}
        <div class="p-12 pt-0 flex justify-between text-center print:p-8">
            <div class="w-48">
                <p class="text-[9px] font-black text-slate-400 uppercase mb-20 tracking-widest italic">Diterima Oleh,</p>
                <div class="border-b-2 border-slate-300 w-full mx-auto print:border-slate-900"></div>
                <p class="text-[11px] font-black mt-3 uppercase tracking-tighter text-slate-800">{{ $payroll->user->name ?? 'Karyawan' }}</p>
                <p class="text-[8px] font-bold text-slate-400 uppercase">Karyawan</p>
            </div>
            <div class="w-48">
                <p class="text-[9px] font-black text-slate-400 uppercase mb-20 tracking-widest italic">Disetujui Oleh,</p>
                <div class="border-b-2 border-slate-300 w-full mx-auto print:border-slate-900"></div>
                <p class="text-[11px] font-black mt-3 uppercase tracking-tighter text-slate-800">FINANCE MANAGER</p>
                <p class="text-[8px] font-bold text-slate-400 uppercase">PT. Happy Momo Indonesia</p>
            </div>
        </div>

        {{-- Print Note --}}
        <div class="hidden print:block text-center pb-6">
            <p class="text-[8px] text-slate-400 italic">Dokumen ini diterbitkan secara elektronik melalui HRIS Happy Momo Indonesia dan sah secara hukum tanpa tanda tangan basah.</p>
        </div>
    </div>
</div>

<style>
    @media print {
        @page { size: portrait; margin: 5mm; }
        body { background: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .sidebar, .navbar, footer, .print\:hidden, nav { display: none !important; }
        main { padding: 0 !important; margin: 0 !important; width: 100% !important; }
        .max-w-4xl { max-width: 100% !important; width: 100% !important; padding: 0 !important; }
    }
</style>
@endsection