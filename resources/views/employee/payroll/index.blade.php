@extends('employee.layouts.app')

@section('title', 'Riwayat Gaji')

@section('content')
<div class="p-6 pb-24">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Slip Gaji</h1>
        <p class="text-sm text-slate-500">Riwayat pendapatan bulanan Anda</p>
    </div>

    {{-- List Gaji --}}
    <div class="space-y-4">
        @forelse($payrolls as $payroll)
            <a href="{{ route('employee.payroll.show', $payroll->id) }}" class="block bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition">
                <div class="flex justify-between items-center mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-green-600">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div>
                            {{-- Ubah angka bulan jadi Nama Bulan (01 -> Januari) --}}
                            <h3 class="font-bold text-slate-800">
                                {{ \Carbon\Carbon::createFromDate(null, $payroll->month)->translatedFormat('F') }} {{ $payroll->year }}
                            </h3>
                            <span class="text-xs text-slate-400">
                                Terbit: {{ \Carbon\Carbon::parse($payroll->generated_date)->translatedFormat('d M Y') }}
                            </span>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-slate-300"></i>
                </div>
                
                <div class="border-t border-slate-50 pt-3 flex justify-between items-end">
                    <span class="text-xs text-slate-500">Take Home Pay</span>
                    <span class="text-lg font-bold text-green-600">
                        Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}
                    </span>
                </div>
            </a>
        @empty
            <div class="text-center py-10">
                <img src="https://illustrations.popsy.co/gray/surveillance.svg" class="w-32 h-32 mx-auto opacity-50 mb-4">
                <p class="text-slate-500">Belum ada slip gaji yang diterbitkan.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection