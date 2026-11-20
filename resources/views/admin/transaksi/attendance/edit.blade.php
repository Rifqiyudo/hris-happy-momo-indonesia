@extends('admin.layouts.master')
@section('title', 'Koreksi Absensi')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-slate-700">Form Koreksi Absen</h3>
        </div>
        
        <form action="{{ route('admin.transaksi.attendance.update', $attendance->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-200 text-blue-700 rounded-full flex items-center justify-center font-bold">
                        {{ substr($attendance->user->name, 0, 2) }}
                    </div>
                    <div>
                        <p class="font-bold text-slate-800">{{ $attendance->user->name }}</p>
                        <p class="text-xs text-gray-500">Tanggal: {{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Jam Masuk</label>
                    <input type="time" name="clock_in" value="{{ $attendance->clock_in }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-xs text-gray-400 mt-1">*Mengubah jam masuk akan menghitung ulang keterlambatan.</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Jam Pulang</label>
                    <input type="time" name="clock_out" value="{{ $attendance->clock_out }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="mb-8">
                <label class="block text-sm font-bold text-gray-700 mb-2">Status Kehadiran</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="present" {{ $attendance->status == 'present' ? 'selected' : '' }}>Hadir (Present)</option>
                    <option value="sick" {{ $attendance->status == 'sick' ? 'selected' : '' }}>Sakit (Sick)</option>
                    <option value="permit" {{ $attendance->status == 'permit' ? 'selected' : '' }}>Izin (Permit)</option>
                    <option value="alpha" {{ $attendance->status == 'alpha' ? 'selected' : '' }}>Alpha (Tanpa Keterangan)</option>
                </select>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.transaksi.attendance.index') }}" class="bg-gray-100 text-gray-600 px-5 py-2.5 rounded-lg text-sm font-bold hover:bg-gray-200">Batal</a>
                <button type="submit" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg text-sm font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection