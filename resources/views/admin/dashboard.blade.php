@extends('admin.layouts.master')
@section('title', 'Dashboard Overview')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Karyawan</p>
            <h3 class="text-3xl font-bold text-slate-800 mt-1">{{ $totalEmployees }}</h3>
            <p class="text-xs text-gray-400 mt-1">Orang terdaftar aktif</p>
        </div>
        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-xl">
            <i class="fas fa-users"></i>
        </div>
    </div>

    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Hadir Hari Ini</p>
            <h3 class="text-3xl font-bold text-green-600 mt-1">{{ $presentToday }}</h3>
            <p class="text-xs text-gray-400 mt-1">Dari {{ $totalEmployees }} karyawan</p>
        </div>
        <div class="w-12 h-12 bg-green-50 text-green-600 rounded-lg flex items-center justify-center text-xl">
            <i class="fas fa-check-circle"></i>
        </div>
    </div>

    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Terlambat</p>
            <h3 class="text-3xl font-bold text-red-500 mt-1">{{ $lateToday }}</h3>
            <p class="text-xs text-gray-400 mt-1">Perlu pembinaan</p>
        </div>
        <div class="w-12 h-12 bg-red-50 text-red-500 rounded-lg flex items-center justify-center text-xl">
            <i class="fas fa-clock"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-slate-700">Aktivitas Absensi Terbaru</h3>
            <a href="{{ route('admin.transaksi.attendance.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">Lihat Semua</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Karyawan</th>
                        <th class="px-6 py-3">Jam Masuk</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentAttendances as $log)
                    <tr>
                        <td class="px-6 py-4 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs">
                                {{ substr($log->user->name, 0, 2) }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-700">{{ $log->user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $log->user->position->name ?? '-' }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-mono text-slate-600">
                            {{ $log->clock_in }}
                        </td>
                        <td class="px-6 py-4">
                            @if($log->is_late)
                                <span class="bg-red-50 text-red-600 px-2 py-1 rounded text-xs font-bold">Terlambat</span>
                            @else
                                <span class="bg-green-50 text-green-600 px-2 py-1 rounded text-xs font-bold">Tepat Waktu</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-400">Belum ada yang absen hari ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 h-fit">
        <h3 class="font-bold text-slate-700 mb-4">Persentase Kehadiran</h3>
        
        <div class="relative pt-1">
            <div class="flex mb-2 items-center justify-between">
                <div>
                    <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-indigo-600 bg-indigo-200">
                        Progress Hari Ini
                    </span>
                </div>
                <div class="text-right">
                    <span class="text-xs font-semibold inline-block text-indigo-600">
                        {{ round($attendancePercentage) }}%
                    </span>
                </div>
            </div>
            <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-indigo-100">
                <div style="width: {{ $attendancePercentage }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-indigo-500 transition-all duration-500"></div>
            </div>
        </div>

        <div class="mt-6 border-t border-gray-100 pt-6">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Aksi Cepat</h4>
            <div class="space-y-3">
                <a href="{{ route('admin.master.user.create') }}" class="block w-full text-center py-2 border border-gray-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-gray-50 hover:border-indigo-300 hover:text-indigo-600 transition">
                    + Tambah Karyawan
                </a>
                <a href="{{ route('admin.transaksi.payroll.create') }}" class="block w-full text-center py-2 border border-gray-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-gray-50 hover:border-indigo-300 hover:text-indigo-600 transition">
                    $ Generate Gaji
                </a>
            </div>
        </div>
    </div>

</div>
@endsection