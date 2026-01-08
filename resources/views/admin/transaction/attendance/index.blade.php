@extends('admin.layouts.master')
@section('title', 'Monitoring Absensi')

@section('content')
<div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
    <form action="{{ route('admin.transaksi.attendance.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
        <div class="w-full md:w-1/4">
            <label class="text-xs font-bold text-gray-500">Mulai Tanggal</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="w-full mt-1 border border-gray-300 rounded-lg p-2 text-sm">
        </div>
        <div class="w-full md:w-1/4">
            <label class="text-xs font-bold text-gray-500">Sampai Tanggal</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="w-full mt-1 border border-gray-300 rounded-lg p-2 text-sm">
        </div>
        <div class="w-full md:w-auto">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-indigo-700">
                Filter Data
            </button>
            <a href="{{ route('admin.report.export.attendance') }}?month={{ date('m') }}&year={{ date('Y') }}" class="ml-2 text-indigo-600 text-sm font-bold hover:underline">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 text-slate-500 font-bold uppercase text-xs">
            <tr>
                <th class="px-6 py-4">Karyawan</th>
                <th class="px-6 py-4">Tanggal</th>
                <th class="px-6 py-4">Jam Masuk</th>
                <th class="px-6 py-4">Jam Pulang</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4">Foto</th>
                <th class="px-6 py-4">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($attendances as $row)
            <tr>
                <td class="px-6 py-4 font-bold text-slate-800">{{ $row->user->name }}</td>
                <td class="px-6 py-4">{{ \Carbon\Carbon::parse($row->date)->format('d/m/Y') }}</td>
                <td class="px-6 py-4 font-mono {{ $row->is_late ? 'text-red-500 font-bold' : 'text-green-600' }}">
                    {{ $row->clock_in }}
                    @if($row->is_late) <span class="text-[10px] bg-red-100 px-1 rounded ml-1">Telat {{ $row->late_minutes }}m</span> @endif
                </td>
                <td class="px-6 py-4 font-mono">{{ $row->clock_out ?? '--:--' }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded text-xs font-bold {{ $row->status == 'present' ? 'bg-green-100 text-green-700' : 'bg-gray-100' }}">
                        {{ ucfirst($row->status) }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    @if($row->photo_in)
                        <a href="{{ asset('storage/'.$row->photo_in) }}" target="_blank">
                            <img src="{{ asset('storage/'.$row->photo_in) }}" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm">
                        </a>
                    @else
                        -
                    @endif
                </td>
                <td class="px-6 py-4">
                    <a href="{{ route('admin.transaksi.attendance.edit', $row->id) }}" class="text-indigo-600 text-xs font-bold">Koreksi</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection