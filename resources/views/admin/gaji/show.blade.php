@extends('admin.layouts.master')
@section('title', 'Preview Slip Gaji - ' . $user->name)

@section('content')
<div class="max-w-4xl mx-auto pb-20 px-4 animate-fadeIn print:max-w-none print:px-0 print:pb-0">
    {{-- 1. BREADCRUMB --}}
    <nav class="flex mb-6 px-2 no-print" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest text-slate-400">
            <li><a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 transition">Home</a></li>
            <li><i class="fas fa-chevron-right text-[8px]"></i></li>
            <li><a href="{{ route('admin.gaji.index') }}" class="hover:text-indigo-600 transition">Gaji</a></li>
            <li><i class="fas fa-chevron-right text-[8px]"></i></li>
            <li class="text-slate-600">Preview Slip Master</li>
        </ol>
    </nav>

    {{-- 2. HEADER SLIP --}}
    <div class="bg-slate-900 rounded-[2.5rem] p-8 mb-8 text-white relative overflow-hidden shadow-2xl shadow-slate-200 print:bg-transparent print:text-slate-900 print:shadow-none print:border-b-2 print:border-slate-800 print:rounded-none print:p-0 print:mb-4 print:pb-4 avoid-break">
        <div class="flex justify-between items-start relative z-20 mb-8 print:mb-3">
            <div class="flex items-center gap-4 print:gap-3">
                <div class="w-16 h-16 bg-white rounded-2xl p-2 shadow-lg border border-slate-100 flex items-center justify-center print:shadow-none print:border-slate-300 print:w-12 print:h-12">
                    <img src="{{ asset('assets/img/logo-happymomo.png') }}" class="w-full h-full object-contain" alt="Logo" onerror="this.src='https://ui-avatars.com/api/?name=HM&background=4f46e5&color=fff'">
                </div>
                <div>
                    <h2 class="font-black text-xl tracking-tighter leading-none print:text-lg">PT. Happy Momo Indonesia</h2>
                    <p class="text-[8px] font-black uppercase tracking-[0.3em] text-indigo-400 mt-1 print:text-slate-500 print:text-[7px]">Jl. Pergudangan Margomulyo Permai, Gedangasin, Surabaya</p>
                </div>
            </div>
            <div class="text-right no-print">
                <span class="px-4 py-2 bg-emerald-500/20 text-emerald-400 rounded-xl text-[10px] font-black uppercase tracking-widest border border-emerald-500/30">
                    Master Payroll
                </span>
            </div>
            <div class="hidden print:block text-right">
                <h1 class="text-base font-black uppercase tracking-widest text-slate-800 leading-none">MASTER GAJI</h1>
                <p class="text-[8px] font-bold text-slate-500 mt-1">DOKUMEN RAHASIA</p>
            </div>
        </div>

        <div class="relative z-10 flex flex-col md:flex-row items-center gap-6 print:flex-row print:items-start print:gap-4">
            <div class="relative no-print">
                <div class="w-24 h-24 rounded-3xl border-4 border-slate-800 overflow-hidden bg-indigo-600 flex items-center justify-center shadow-xl">
                    @if($user->avatar)
                        <img src="{{ asset('storage/'.$user->avatar) }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-2xl font-black text-white">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                    @endif
                </div>
                <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-indigo-500 rounded-xl flex items-center justify-center border-4 border-slate-900">
                    <i class="fas fa-check-circle text-[10px] text-white"></i>
                </div>
            </div>
            <div class="text-center md:text-left print:text-left">
                <h1 class="text-2xl font-black uppercase tracking-tight print:text-base">{{ $user->name }}</h1>
                <p class="text-indigo-400 font-bold text-sm tracking-widest uppercase mt-1 print:text-slate-600 print:text-[9px]">
                    {{ $user->position->name ?? 'Staff' }} • {{ $user->nik }}
                </p>
                <div class="flex flex-wrap justify-center md:justify-start gap-3 mt-4 print:mt-1.5 print:gap-2">
                    <span class="px-4 py-1.5 bg-slate-800 rounded-full text-[10px] font-black uppercase border border-slate-700 print:bg-transparent print:border-slate-300 print:text-slate-700 print:px-2 print:py-0.5 print:text-[8px]">
                        <i class="fas fa-building mr-2 text-indigo-400 no-print"></i> {{ $user->branch->name ?? 'Head Office' }}
                    </span>
                    <span class="px-4 py-1.5 bg-slate-800 rounded-full text-[10px] font-black uppercase border border-slate-700 print:bg-transparent print:border-slate-300 print:text-slate-700 print:px-2 print:py-0.5 print:text-[8px]">
                        <i class="fas fa-calendar-alt mr-2 text-indigo-400 no-print"></i> Updated: {{ now()->translatedFormat('d M Y') }}
                    </span>
                </div>
            </div>
        </div>
        <div class="absolute right-0 bottom-0 opacity-5 translate-x-10 translate-y-10 no-print">
            <i class="fas fa-receipt text-[15rem]"></i>
        </div>
    </div>

    @if(!$user->salary)
        {{-- EMPTY STATE --}}
        <div class="bg-amber-50 border-2 border-dashed border-amber-200 rounded-[2rem] p-12 text-center no-print">
            <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-receipt text-amber-500 text-3xl"></i>
            </div>
            <p class="text-amber-800 font-black uppercase tracking-widest text-sm">Komponen gaji belum diatur</p>
            <p class="text-amber-600/60 text-xs font-bold mt-2">Silakan atur master gaji karyawan ini terlebih dahulu.</p>
            <a href="{{ route('admin.gaji.create') }}" class="mt-6 inline-block bg-slate-900 text-white px-8 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition active:scale-95 shadow-xl shadow-slate-200">
                Atur Master Gaji
            </a>
        </div>
    @else
        @php
            $salary = $user->salary;
            
            $baseSalary = (float) $salary->base_salary;
            $posAllowance = (float) $salary->position_allowance;
            $famAllowance = (float) $salary->family_allowance;
            $mealAllowance = (float) $salary->meal_allowance;
            $transpAllowance = (float) $salary->transport_allowance;
            $bonus = (float) $salary->bonus_amount;
            $thr = (float) $salary->thr_amount;
            $other = (float) $salary->other_allowance;
            $overtime = (float) $salary->overtime_amount;

            $jhtDeduction = (float) $salary->jht_deduction;
            $kesDeduction = (float) $salary->kes_deduction;
            $jpDeduction = (float) $salary->jp_deduction;
            $routineDeduction = (float) $salary->routine_deduction;

            $totalEarning = $baseSalary + $posAllowance + $famAllowance + $mealAllowance + $transpAllowance + $bonus + $thr + $other + $overtime;
            $totalDeduction = $jhtDeduction + $kesDeduction + $jpDeduction + $routineDeduction;
            $netSalary = $totalEarning - $totalDeduction;
        @endphp

        {{-- 3. DETAIL SLIP --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8 print-col-2">
            {{-- PENDAPATAN --}}
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/40 p-8 print-col-item print:shadow-none print:border-slate-300 print:rounded-xl print:p-4 flex flex-col justify-between avoid-break">
                <div>
                    <h3 class="text-xs font-black text-emerald-600 uppercase tracking-[0.2em] mb-6 flex items-center gap-2 print:text-[9px] print:mb-3 print:text-slate-800 border-b border-slate-100 pb-2 print:pb-1">
                        <span class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center no-print"><i class="fas fa-plus-circle"></i></span>
                        Pendapatan (Earnings)
                    </h3>
                    <div class="space-y-4 text-sm print:space-y-1.5 print:text-[9px]">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">Gaji Pokok</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($baseSalary, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">Tj. Jabatan (Tetap)</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($posAllowance, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">Tj. Keluarga (Tetap)</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($famAllowance, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">Uang Makan & Transp.</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($mealAllowance + $transpAllowance, 0, ',', '.') }}</span>
                        </div>
                        @if($overtime > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">Lembur (Overtime)</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($overtime, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">THR & Bonus</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($thr + $bonus + $other, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                <div class="pt-4 mt-6 border-t-2 border-dashed border-slate-200 print:mt-3 print:pt-2">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-800 font-black uppercase text-[11px] print:text-[9px]">Total Bruto Dasar</span>
                        <span class="text-lg font-black text-emerald-600 print:text-xs print:text-slate-900">Rp {{ number_format($totalEarning, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- POTONGAN --}}
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/40 p-8 print-col-item print:shadow-none print:border-slate-300 print:rounded-xl print:p-4 flex flex-col justify-between avoid-break">
                <div>
                    <h3 class="text-xs font-black text-rose-600 uppercase tracking-[0.2em] mb-6 flex items-center gap-2 print:text-[9px] print:mb-3 print:text-slate-800 border-b border-slate-100 pb-2 print:pb-1">
                        <span class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center no-print"><i class="fas fa-minus-circle"></i></span>
                        Potongan (Deductions)
                    </h3>
                    <div class="space-y-4 text-sm print:space-y-1.5 print:text-[9px]">
                        @if($jhtDeduction > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">BPJS JHT</span>
                            <span class="font-bold text-rose-600 print:text-slate-800">-Rp {{ number_format($jhtDeduction, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        @if($kesDeduction > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">BPJS Kesehatan</span>
                            <span class="font-bold text-rose-600 print:text-slate-800">-Rp {{ number_format($kesDeduction, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        @if($jpDeduction > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">BPJS Pensiun (JP)</span>
                            <span class="font-bold text-rose-600 print:text-slate-800">-Rp {{ number_format($jpDeduction, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        @if($routineDeduction > 0)
                        <div class="flex justify-between items-start bg-rose-50/40 p-4 rounded-2xl border border-rose-100 shadow-sm mt-2 print:bg-transparent print:p-0 print:border-none print:mt-0">
                            <div class="flex flex-col">
                                <span class="text-rose-600 font-black uppercase text-[10px] print:text-slate-500 print:font-medium print:normal-case print:text-[9px]">Potongan Manual / Lainnya</span>
                                <span class="text-[9px] font-bold text-slate-500 italic mt-1.5 leading-relaxed max-w-[200px] print:hidden">
                                    Nominal manual. Mencakup denda tidak masuk, kasbon, dll.
                                </span>
                            </div>
                            <span class="font-black text-rose-600 print:text-slate-800 print:font-bold">-Rp {{ number_format($routineDeduction, 0, ',', '.') }}</span>
                        </div>
                        @else
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">Potongan Manual / Lainnya</span>
                            <span class="font-bold text-slate-800">Rp 0</span>
                        </div>
                        @endif

                        {{-- INFO SISTEM --}}
                        <div class="mt-5 p-4 bg-slate-50 rounded-2xl border border-slate-200 border-dashed no-print">
                            <p class="text-[9px] font-black text-indigo-500 uppercase tracking-widest mb-1.5"><i class="fas fa-info-circle mr-1"></i> Transparansi Sistem Aktual</p>
                            <p class="text-[9px] text-slate-500 italic leading-relaxed font-medium">
                                Pada saat <b>Generate Slip Akhir Bulan</b>, sistem akan menambahkan baris <b>Potongan Alpha</b> dan <b>Denda Terlambat (Rp 5.000/15 mnt)</b> sesuai rekap absensi.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="pt-4 mt-6 border-t-2 border-dashed border-slate-200 print:mt-3 print:pt-2">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-800 font-black uppercase text-[11px] print:text-[9px]">Total Potongan Dasar</span>
                        <span class="text-lg font-black text-rose-600 print:text-xs print:text-slate-900">-Rp {{ number_format($totalDeduction, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. TAKE HOME PAY --}}
        <div class="w-full bg-indigo-600 shadow-indigo-200 rounded-[2.5rem] p-8 text-white shadow-2xl transition-all duration-500 print:w-full print:bg-slate-100 print:text-slate-900 print:shadow-none print:border-2 print:border-slate-800 print:rounded-2xl print:p-4 print:mb-2 avoid-break">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6 print:flex-row print:gap-2">
                <div class="text-center md:text-left print:text-left">
                    <h4 class="text-[10px] font-black uppercase tracking-[0.3em] opacity-70 print:text-slate-600 print:opacity-100 print:text-[8px]">Estimasi Take Home Pay</h4>
                    <p class="text-5xl font-black mt-1 tracking-tighter print:text-xl">Rp {{ number_format($netSalary, 0, ',', '.') }}</p>
                </div>
                <div class="flex flex-wrap justify-center gap-4 no-print">
                    <button onclick="window.print()" class="px-8 py-4 bg-white text-indigo-600 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-50 transition shadow-lg active:scale-95">
                        <i class="fas fa-print mr-2"></i> Cetak Pratinjau
                    </button>
                    <a href="{{ route('admin.gaji.edit', $salary->id) }}" class="px-8 py-4 bg-indigo-500 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-400 transition shadow-lg">
                        <i class="fas fa-edit mr-2"></i> Update Gaji
                    </a>
                </div>
            </div>
        </div>

        {{-- 5. SIGNATURE SECTION (HANYA MUNCUL SAAT DIPRINT) --}}
        <div class="hidden print:flex justify-between mt-4 px-10 avoid-break">
            <div class="text-center">
                <p class="text-[8px] font-black uppercase tracking-widest text-slate-500 mb-10 italic">Penerima (Karyawan),</p>
                <div class="border-b border-slate-800 w-36 mx-auto"></div>
                <p class="text-[9px] font-black mt-1.5 uppercase tracking-tight">{{ $user->name }}</p>
            </div>
            <div class="text-center">
                <p class="text-[8px] font-black uppercase tracking-widest text-slate-500 mb-10 italic">Disetujui Oleh (HRD),</p>
                <div class="border-b border-slate-800 w-36 mx-auto"></div>
                <p class="text-[9px] font-black mt-1.5 uppercase tracking-tight">PT. Happy Momo Indonesia</p>
            </div>
        </div>
        <div class="hidden print:block mt-4 text-center">
            <p class="text-[7px] text-slate-400 italic">Dokumen ini merupakan kerangka/pratinjau master gaji dasar.</p>
        </div>
    @endif
</div>

{{-- CSS KHUSUS PRINT --}}
<style>
    @media print {
        @page { 
            size: A4 portrait; 
            margin: 10mm; /* Menyetel margin ke 10mm agar area konten lebih luas */
        }
        body { 
            background-color: white !important; 
            -webkit-print-color-adjust: exact; 
            print-color-adjust: exact;
        }
        
        /* Menyembunyikan elemen bawaan template yang tidak perlu dicetak */
        .no-print, aside, header, nav, footer, .fixed { display: none !important; }
        
        /* Mereset lebar layout utama */
        main, body, html { 
            padding: 0 !important; 
            margin: 0 !important; 
            width: 100% !important; 
            max-width: 100% !important; 
        }
        
        /* Trik Utama Flexbox: Memaksa kolom pendapatan dan potongan bersebelahan */
        .print-col-2 {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            gap: 1.5rem !important; /* Jarak antara kolom kiri dan kanan */
            width: 100% !important;
        }
        
        /* Trik Utama Flexbox: Masing-masing kolom memakan 50% ruang */
        .print-col-item {
            width: 50% !important;
            flex: 1 1 0% !important;
        }
        
        /* Mencegah kotak atau teks terpotong di batas antar halaman kertas */
        .avoid-break { 
            page-break-inside: avoid !important; 
            break-inside: avoid !important; 
        }
    }
</style>
@endsection