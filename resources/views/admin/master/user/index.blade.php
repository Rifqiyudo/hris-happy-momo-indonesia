@extends('admin.layouts.master')
@section('title', 'Data Karyawan')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-bold text-slate-700">List Semua Karyawan</h3>
        <a href="{{ route('admin.master.user.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700">
            + Tambah Karyawan
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-gray-50 text-slate-500 font-bold uppercase text-xs">
                <tr>
                    <th class="px-6 py-4">Nama & NIK</th>
                    <th class="px-6 py-4">Jabatan</th>
                    <th class="px-6 py-4">Kontak</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <p class="font-bold text-slate-800">{{ $user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $user->nik }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-blue-50 text-blue-600 px-2 py-1 rounded text-xs font-semibold">
                            {{ $user->position->name ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <p>{{ $user->email }}</p>
                        <p class="text-xs text-gray-400">{{ $user->phone }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @if($user->is_active)
                            <span class="text-green-600 font-bold text-xs">● Aktif</span>
                        @else
                            <span class="text-red-500 font-bold text-xs">● Non-Aktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center space-x-2">
                        <a href="{{ route('admin.master.user.edit', $user->id) }}" class="text-amber-500 hover:text-amber-700"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.master.user.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus karyawan ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection