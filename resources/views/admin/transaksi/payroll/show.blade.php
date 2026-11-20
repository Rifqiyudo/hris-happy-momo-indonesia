@extends('admin.layouts.master')

@php
    // KALKULASI PEMISAHAN PENCAIRAN DANA (FITUR BARU)
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

@section('title', 'Detail Gaji - ' . $payroll->user->name)

@section('content')
<div class="max-w-7xl mx-auto pb-20 px-4 animate-fadeIn">
    
    {{-- 1. HEADER & ACTION BUTTONS --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
        <div class="flex items-center gap-5">
            <a href="{{ route('admin.transaksi.payroll.index') }}" class="w-11 h-11 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 transition-all shadow-sm">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                    Pratinjau Slip Gaji
                    @if($payroll->status == 'published' || $payroll->status == 'paid')
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-600 border border-emerald-100 shadow-sm gap-1.5">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            Terbit (Published)
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest bg-amber-50 text-amber-600 border border-amber-100 shadow-sm gap-1.5">
                            <i class="fas fa-file-signature"></i> Draft (Pending)
                        </span>
                    @endif
                </h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1">Periode: <span class="text-indigo-500">{{ \Carbon\Carbon::create(null, $payroll->month)->translatedFormat('F') }} {{ $payroll->year }}</span></p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.transaksi.payroll.print-slip', $payroll->id) }}" target="_blank" class="bg-white border border-slate-200 text-slate-700 px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 hover:text-white hover:border-slate-800 transition-all flex items-center gap-2 shadow-sm">
                <i class="fas fa-print"></i> Cetak Slip
            </a>

            @if($payroll->status == 'draft')
            <form action="{{ route('admin.transaksi.payroll.publish', $payroll->id) }}" method="POST" onsubmit="return confirm('Menerbitkan gaji ini? Karyawan akan menerima notifikasi otomatis.')">
                @csrf
                <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 flex items-center gap-2 active:scale-95">
                    <i class="fas fa-paper-plane"></i> Publish Gaji
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- 2. MAIN GRID LAYOUT --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {{-- KOLOM KIRI: INFORMASI PERSONEL --}}
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden sticky top-6">
                <div class="bg-slate-50 border-b border-slate-100 px-6 py-4">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Informasi Personel</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 rounded-2xl bg-indigo-100 border-2 border-white shadow-md flex items-center justify-center overflow-hidden shrink-0">
                            <img src="{{ $payroll->user->avatar ? asset('storage/'.$payroll->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($payroll->user->name).'&background=4f46e5&color=fff' }}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-800 tracking-tight leading-tight">{{ $payroll->user->name }}</h2>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $payroll->user->nik }}</p>
                        </div>
                    </div>

                    <ul class="divide-y divide-slate-100">
                        <li class="py-3 flex justify-between items-center">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Jabatan</span>
                            <span class="text-xs font-black text-slate-800 text-right">{{ $payroll->user->position->name ?? 'Staff' }}</span>
                        </li>
                        <li class="py-3 flex justify-between items-center">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Departemen</span>
                            <span class="text-xs font-black text-slate-800 text-right">{{ $payroll->user->department->name ?? 'General' }}</span>
                        </li>
                        <li class="py-3 flex justify-between items-center">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Bank</span>
                            <span class="text-xs font-black text-slate-800 text-right">{{ $payroll->user->bank_name ?? '-' }}</span>
                        </li>
                        <li class="py-3 flex justify-between items-center">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Kehadiran</span>
                            <span class="text-xs font-black text-indigo-600 text-right">{{ $payroll->total_days_worked }} Hari Hadir</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: RINCIAN FINANSIAL --}}
        <div class="lg:col-span-8 space-y-6">
            
            {{-- A. KOTAK LEDGER (PENDAPATAN & POTONGAN) --}}
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-slate-100">
                    
                    {{-- Bagian Pendapatan --}}
                    <div class="p-8 flex flex-col h-full">
                        <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-emerald-500 mb-6 flex items-center gap-2">
                            <i class="fas fa-plus-circle"></i> Pendapatan (Earnings)
                        </h3>
                        
                        <div class="space-y-4 mb-6 flex-1">
                            <div class="flex justify-between items-end">
                                <span class="text-xs font-bold text-slate-500">Gaji Pokok</span>
                                <span class="text-sm font-black text-slate-800 font-mono tracking-tight">Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</span>
                            </div>
                            
                            @if($payroll->position_allowance > 0)
                            <div class="flex justify-between items-end">
                                <span class="text-xs font-bold text-slate-500">Tj. Jabatan (Tetap)</span>
                                <span class="text-sm font-black text-slate-800 font-mono tracking-tight">Rp {{ number_format($payroll->position_allowance, 0, ',', '.') }}</span>
                            </div>
                            @endif

                            @if($payroll->family_allowance > 0)
                            <div class="flex justify-between items-end">
                                <span class="text-xs font-bold text-slate-500">Tj. Keluarga (Tetap)</span>
                                <span class="text-sm font-black text-slate-800 font-mono tracking-tight">Rp {{ number_format($payroll->family_allowance, 0, ',', '.') }}</span>
                            </div>
                            @endif

                            @if(($payroll->meal_allowance_total + $payroll->transport_allowance_total) > 0)
                            <div class="flex justify-between items-start pt-1">
                                <div>
                                    <p class="text-xs font-bold text-slate-500">Uang Makan & Transp.</p>
                                    <p class="text-[9px] text-indigo-500 font-black uppercase italic mt-0.5">{{ $payroll->total_days_worked }} Hari x Rp {{ number_format(($payroll->meal_allowance_per_day + $payroll->transport_allowance_per_day), 0, ',', '.') }}</p>
                                </div>
                                <span class="text-sm font-black text-slate-800 font-mono tracking-tight">Rp {{ number_format($payroll->meal_allowance_total + $payroll->transport_allowance_total, 0, ',', '.') }}</span>
                            </div>
                            @endif

                            @if(($payroll->overtime_amount ?? 0) > 0)
                            <div class="flex justify-between items-end">
                                <span class="text-xs font-bold text-slate-500">Uang Lembur (Overtime)</span>
                                <span class="text-sm font-black text-slate-800 font-mono tracking-tight">Rp {{ number_format($payroll->overtime_amount, 0, ',', '.') }}</span>
                            </div>
                            @endif

                            @if($payroll->thr_amount > 0 || $payroll->bonus_amount > 0 || $payroll->other_allowance > 0)
                            <div class="flex justify-between items-end">
                                <span class="text-xs font-bold text-slate-500">Bonus & Lainnya</span>
                                <span class="text-sm font-black text-slate-800 font-mono tracking-tight">Rp {{ number_format($payroll->thr_amount + $payroll->bonus_amount + $payroll->other_allowance, 0, ',', '.') }}</span>
                            </div>
                            @endif
                        </div>

                        <div class="pt-4 border-t border-dashed border-slate-200 flex justify-between items-center bg-emerald-50/50 -mx-8 px-8 py-4 mt-auto">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Total Bruto</span>
                            <span class="text-base font-black text-emerald-600 font-mono tracking-tight">Rp {{ number_format($payroll->total_allowance + $payroll->base_salary, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- Bagian Potongan --}}
                    <div class="p-8 flex flex-col h-full">
                        <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-rose-500 mb-6 flex items-center gap-2">
                            <i class="fas fa-minus-circle"></i> Potongan (Deductions)
                        </h3>
                        
                        <div class="space-y-4 mb-6 flex-1">
                            @if($payroll->jht_deduction > 0)
                            <div class="flex justify-between items-end">
                                <span class="text-xs font-bold text-slate-500">BPJS JHT</span>
                                <span class="text-sm font-black text-rose-600 font-mono tracking-tight">-Rp {{ number_format($payroll->jht_deduction, 0, ',', '.') }}</span>
                            </div>
                            @endif

                            @if($payroll->kes_deduction > 0)
                            <div class="flex justify-between items-end">
                                <span class="text-xs font-bold text-slate-500">BPJS Kesehatan</span>
                                <span class="text-sm font-black text-rose-600 font-mono tracking-tight">-Rp {{ number_format($payroll->kes_deduction, 0, ',', '.') }}</span>
                            </div>
                            @endif

                            @if($payroll->jp_deduction > 0)
                            <div class="flex justify-between items-end">
                                <span class="text-xs font-bold text-slate-500">BPJS JP</span>
                                <span class="text-sm font-black text-rose-600 font-mono tracking-tight">-Rp {{ number_format($payroll->jp_deduction, 0, ',', '.') }}</span>
                            </div>
                            @endif

                            @if($payroll->late_deduction > 0)
                            <div class="flex justify-between items-start pt-1">
                                <div>
                                    <p class="text-xs font-bold text-slate-500">Denda Keterlambatan</p>
                                    <p class="text-[9px] text-slate-400 italic mt-0.5">Kalkulasi kelipatan waktu otomatis</p>
                                </div>
                                <span class="text-sm font-black text-rose-600 font-mono tracking-tight">-Rp {{ number_format($payroll->late_deduction, 0, ',', '.') }}</span>
                            </div>
                            @endif

                            @if($payroll->routine_deduction > 0)
                            <div class="flex justify-between items-start pt-1">
                                <div>
                                    <p class="text-xs font-bold text-slate-500">Potongan Manual / Lainnya</p>
                                    <p class="text-[9px] text-slate-400 italic mt-0.5">Denda izin, kasbon, iuran dll.</p>
                                </div>
                                <span class="text-sm font-black text-rose-600 font-mono tracking-tight">-Rp {{ number_format($payroll->routine_deduction, 0, ',', '.') }}</span>
                            </div>
                            @endif

                            @if($payroll->total_deduction == 0)
                            <div class="text-center py-4">
                                <span class="text-[10px] font-bold text-slate-400 italic bg-slate-50 px-4 py-2 rounded-lg border border-slate-100">Tidak ada potongan bulan ini.</span>
                            </div>
                            @endif
                        </div>

                        <div class="pt-4 border-t border-dashed border-slate-200 flex justify-between items-center bg-rose-50/50 -mx-8 px-8 py-4 mt-auto">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Total Potongan</span>
                            <span class="text-base font-black text-rose-600 font-mono tracking-tight">Rp {{ number_format($payroll->total_deduction, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- B. JADWAL PENCAIRAN DANA --}}
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 relative overflow-hidden">
                <div class="absolute right-0 top-0 w-32 h-32 bg-indigo-50 rounded-bl-[100px] opacity-50"></div>
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-600 mb-4 flex items-center gap-2 relative z-10">
                    <i class="far fa-calendar-check"></i> Jadwal Pencairan Dana
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 relative z-10">
                    <div class="border border-slate-200 rounded-2xl p-5 hover:border-indigo-300 transition-colors bg-slate-50/50">
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1">Cair Tanggal Akhir Bulan</p>
                        <h4 class="text-xl font-black text-slate-800 tracking-tight font-mono mb-1">Rp {{ number_format($transferAkhirBulan, 0, ',', '.') }}</h4>
                        <p class="text-[9px] font-bold text-slate-400 italic">Gaji Pokok + Tunjangan + Lembur - Potongan</p>
                    </div>
                    <div class="border border-emerald-100 bg-emerald-50/30 rounded-2xl p-5">
                        <p class="text-[9px] font-black uppercase tracking-widest text-emerald-500 mb-1">Cair Tanggal 5 Bulan Depan</p>
                        <h4 class="text-xl font-black text-emerald-600 tracking-tight font-mono mb-1">Rp {{ number_format($transferTanggalLima, 0, ',', '.') }}</h4>
                        <p class="text-[9px] font-bold text-slate-400 italic">Pencairan khusus Bonus / Insentif Omzet</p>
                    </div>
                </div>
            </div>

            {{-- C. TAKE HOME PAY (THP) BANNER --}}
            <div class="bg-slate-900 rounded-3xl shadow-xl shadow-slate-200 p-8 md:p-10 relative overflow-hidden border border-slate-800 flex flex-col md:flex-row justify-between items-center md:items-end gap-6">
                <!-- Ornamen Latar -->
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-500/20 rounded-full blur-3xl"></div>
                <div class="absolute left-0 bottom-0 w-32 h-32 bg-indigo-500/20 rounded-full blur-2xl"></div>

                <div class="relative z-10 text-center md:text-left w-full md:w-auto">
                    <p class="text-[11px] font-black uppercase tracking-[0.3em] text-slate-400 mb-2">Take Home Pay (THP)</p>
                    <h2 class="text-4xl md:text-5xl font-black text-emerald-400 tracking-tighter font-mono" style="text-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);">
                        Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}
                    </h2>
                </div>

                <div class="relative z-10 text-center md:text-right w-full md:w-1/2">
                    <p class="text-[9px] font-bold uppercase tracking-widest text-slate-500 mb-1">Terbilang:</p>
                    <div class="bg-white/10 backdrop-blur-sm border border-white/10 rounded-xl px-5 py-3 text-xs font-black text-white leading-relaxed inline-block text-left md:text-right">
                        {{ ucwords(terbilang($payroll->net_salary)) }} Rupiah
                    </div>
                </div>
            </div>

        </div>
    </div>
    
    {{-- 3. FOOTER AUDIT --}}
    <div class="mt-8 flex flex-col md:flex-row justify-between items-center gap-2 text-center md:text-left">
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">
            <i class="fas fa-fingerprint mr-1"></i> Kalkulasi Gaji: {{ \Carbon\Carbon::parse($payroll->generated_date ?? $payroll->created_at)->format('d/m/Y H:i') }} • Audit ID: #PAY-{{ $payroll->id }}
        </p>
        <p class="text-[10px] text-slate-400 font-bold italic">
            *Dokumen ini bersifat rahasia (Privileged & Confidential)
        </p>
    </div>
</div>
@endsection