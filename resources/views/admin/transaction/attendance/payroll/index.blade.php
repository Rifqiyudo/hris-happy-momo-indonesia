@extends('admin.layouts.master')
@section('title', 'Data Penggajian')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h3 class="font-bold text-slate-800">Periode: {{ \Carbon\Carbon::create()->month($month)->locale('id')->monthName }} {{ $year }}</h3>
    </div>
    <div class="space-x-2">
        <a href="{{ route('admin.transaksi.payroll.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-indigo-700">
            <i class="fas fa-magic mr-2"></i> Generate Gaji Baru
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 text-slate-500 font-bold uppercase text-xs">
            <tr>
                <th class="px-6 py-4">Karyawan</th>
                <th class="px-6 py-4">Kehadiran</th>
                <th class="px-6 py-4">Gaji Bersih (THP)</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($payrolls as $payroll)
            <tr>
                <td class="px-6 py-4">
                    <p class="font-bold text-slate-800">{{ $payroll->user->name }}</p>
                    <p class="text-xs text-gray-400">{{ $payroll->user->position->name ?? '-' }}</p>
                </td>
                <td class="px-6 py-4">
                    {{ $payroll->total_days_worked }} Hari
                    @if($payroll->total_late_minutes > 0)
                        <span class="block text-xs text-red-500">Telat: {{ $payroll->total_late_minutes }} menit</span>
                    @endif
                </td>
                <td class="px-6 py-4 font-mono font-bold text-slate-700">
                    Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}
                </td>
                <td class="px-6 py-4">
                    @if($payroll->status == 'paid')
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">Terbit</span>
                    @else
                        <span class="bg-gray-200 text-gray-600 px-2 py-1 rounded text-xs font-bold">Draft</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-center space-x-2">
                    <a href="{{ route('admin.report.print.slip', $payroll->id) }}" target="_blank" class="bg-slate-100 text-slate-600 px-3 py-1 rounded text-xs font-bold hover:bg-slate-200">
                        <i class="fas fa-print"></i> Slip
                    </a>
                    
                    @if($payroll->status == 'draft')
                        <form action="{{ route('admin.transaksi.payroll.publish', $payroll->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded text-xs font-bold hover:bg-green-600">
                                Publish
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-8 text-center text-gray-400">Belum ada data gaji untuk periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection