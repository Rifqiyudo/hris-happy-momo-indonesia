@extends('admin.layouts.master')
@section('title', 'Generate Gaji Bulanan')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[70vh] py-10 animate-fadeIn">
    
    {{-- CONTAINER UTAMA --}}
    <div class="w-full max-w-md bg-white p-10 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden">
        {{-- Aksen Dekoratif --}}
        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-bl-full -mr-10 -mt-10"></div>
        
        {{-- HEADER --}}
        <div class="text-center mb-8 relative z-10">
            <div class="w-16 h-16 bg-indigo-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl shadow-lg shadow-indigo-200">
                <i class="fas fa-calculator"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Generate Payroll</h2>
            <p class="text-[11px] font-bold text-gray-400 mt-1 uppercase tracking-widest">PT. HAPPY MOMO INDONESIA</p>
        </div>

        {{-- NOTIFIKASI --}}
        @if(session('error'))
            <div class="mb-6 p-4 bg-rose-50 border border-rose-100 text-rose-600 rounded-2xl text-[11px] font-bold flex items-center gap-3">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        {{-- FORM --}}
        {{-- Menggunakan type="submit" langsung agar tombol selalu bisa diklik --}}
        <form id="payrollForm" action="{{ route('admin.transaksi.payroll.generate-mass') }}" method="POST" class="space-y-6">
            @csrf
            
            {{-- PILIH BULAN --}}
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.15em] mb-2 ml-1">Bulan Penggajian</label>
                <select name="month" id="month" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-4 text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 transition cursor-pointer">
                    @for($m=1; $m<=12; $m++)
                        @php $mVal = sprintf('%02d', $m); @endphp
                        <option value="{{ $mVal }}" {{ date('m') == $mVal ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            {{-- PILIH TAHUN --}}
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.15em] mb-2 ml-1">Tahun</label>
                <select name="year" id="year" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-4 text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 transition cursor-pointer">
                    @php $currentYear = date('Y'); @endphp
                    @for($y = $currentYear; $y >= $currentYear - 2; $y--)
                        <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            {{-- INFO BOX --}}
            <div class="bg-indigo-50/50 border border-indigo-100 p-5 rounded-[1.5rem] flex gap-3">
                <i class="fas fa-info-circle text-indigo-500 mt-0.5 text-sm"></i>
                <p class="text-[11px] text-indigo-700 leading-relaxed font-medium">
                    Sistem akan mengkalkulasi <b>Gaji Pokok</b>, <b>Tunjangan</b>, <b>Lembur</b>, dan <b>Potongan</b> secara otomatis.
                </p>
            </div>

            {{-- BUTTON SUBMIT --}}
            {{-- Menggunakan type="submit" untuk memastikan form terkirim ke route action --}}
            <button type="submit" class="w-full bg-indigo-600 text-white font-black py-5 rounded-2xl hover:bg-indigo-700 transition shadow-xl shadow-indigo-100 active:scale-[0.98] flex items-center justify-center gap-3">
                <i class="fas fa-magic"></i> Proses Payroll Sekarang
            </button>
        </form>
    </div>

    {{-- FOOTER NAV --}}
    <a href="{{ route('admin.transaksi.payroll.index') }}" class="mt-8 text-xs font-black text-gray-400 hover:text-indigo-600 transition uppercase tracking-widest flex items-center gap-2">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Gaji
    </a>
</div>
@endsection