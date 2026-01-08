@extends('admin.layouts.master')
@section('title', 'Jadwal Shift')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-bold text-slate-700 mb-4">Pengaturan Jam Kerja</h3>
        <p class="text-sm text-gray-500 mb-6">Atur jam masuk dan pulang untuk hari kerja biasa dan hari Sabtu.</p>
        
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 font-bold">
                <tr>
                    <th class="p-3">Hari</th>
                    <th class="p-3">Shift</th>
                    <th class="p-3">Jam Kerja</th>
                    <th class="p-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($shifts as $shift)
                <tr>
                    <td class="p-3 font-medium">{{ \Carbon\Carbon::create()->day($shift->day_number)->locale('id')->dayName }}</td>
                    <td class="p-3 text-xs text-gray-500">{{ $shift->name }}</td>
                    <td class="p-3 font-mono text-indigo-600">
                        {{ \Carbon\Carbon::parse($shift->clock_in)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->clock_out)->format('H:i') }}
                    </td>
                    <td class="p-3 text-right">
                        <a href="{{ route('admin.master.shift.edit', $shift->id) }}" class="text-indigo-600 text-xs font-bold hover:underline">Edit Jam</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection