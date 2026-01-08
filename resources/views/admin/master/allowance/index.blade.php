@extends('admin.layouts.master')
@section('title', 'Master Tunjangan')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-bold text-slate-700">Komponen Tunjangan</h3>
        <a href="{{ route('admin.master.allowance.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700">
            + Tambah
        </a>
    </div>

    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 text-slate-500 font-bold uppercase text-xs">
            <tr>
                <th class="px-6 py-4">Nama Tunjangan</th>
                <th class="px-6 py-4">Tipe Hitungan</th>
                <th class="px-6 py-4">Nominal (Rp)</th>
                <th class="px-6 py-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($allowances as $item)
            <tr>
                <td class="px-6 py-4 font-medium text-slate-800">{{ $item->name }}</td>
                <td class="px-6 py-4">
                    @if($item->type == 'daily')
                        <span class="bg-purple-50 text-purple-700 px-2 py-1 rounded text-xs font-bold">Harian (Per Kehadiran)</span>
                    @else
                        <span class="bg-sky-50 text-sky-700 px-2 py-1 rounded text-xs font-bold">Bulanan (Fixed)</span>
                    @endif
                </td>
                <td class="px-6 py-4 font-mono">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                <td class="px-6 py-4 text-center space-x-2">
                    <a href="{{ route('admin.master.allowance.edit', $item->id) }}" class="text-amber-500"><i class="fas fa-edit"></i></a>
                    </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection