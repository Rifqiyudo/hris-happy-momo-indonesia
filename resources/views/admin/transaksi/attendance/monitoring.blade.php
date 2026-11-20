@extends('admin.layouts.master')

@section('title', 'Monitoring Real-time')

@section('content')
<div id="monitoring-wrapper">
    {{-- Bagian Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Sudah Hadir</p>
            <h3 class="text-3xl font-black text-indigo-600" id="stat-present">{{ $presentCount }}</h3>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Belum Absen</p>
            <h3 class="text-3xl font-black text-rose-500" id="stat-missing">{{ $missingEmployees->count() }}</h3>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Total Karyawan</p>
            <h3 class="text-3xl font-black text-slate-800">{{ $presentCount + $missingEmployees->count() }}</h3>
        </div>
    </div>

    {{-- Tabel Monitoring --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center">
            <div>
                <h4 class="font-black text-slate-800">Daftar Karyawan Belum Hadir</h4>
                <p class="text-xs text-slate-400 mt-1">Menampilkan data karyawan aktif yang belum melakukan scan barcode hari ini.</p>
            </div>
            <span class="px-4 py-1 bg-rose-50 text-rose-600 text-[10px] font-bold rounded-full animate-pulse flex items-center gap-2">
                <span class="w-2 h-2 bg-rose-600 rounded-full"></span> LIVE MONITORING
            </span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="missing-table">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Karyawan</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Departemen</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Kontak</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($missingEmployees as $emp)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-8 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $emp->avatar_url }}" class="w-10 h-10 rounded-xl object-cover border-2 border-white shadow-sm">
                                <div>
                                    <p class="text-sm font-bold text-slate-800">{{ $emp->name }}</p>
                                    <p class="text-[10px] text-slate-400 uppercase font-bold">{{ $emp->position->name ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-4">
                            <span class="text-xs font-semibold text-slate-500">{{ $emp->department->name ?? '-' }}</span>
                        </td>
                        <td class="px-8 py-4">
                            <a href="https://wa.me/{{ $emp->phone }}" target="_blank" class="text-sm font-medium text-indigo-600 hover:underline">
                                {{ $emp->phone ?? '-' }}
                            </a>
                        </td>
                        <td class="px-8 py-4">
                            <span class="px-3 py-1 bg-amber-50 text-amber-600 text-[10px] font-bold rounded-lg uppercase">Belum Scan</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-check-circle text-4xl text-emerald-500 mb-3"></i>
                                <p class="text-slate-400 font-bold italic">Semua karyawan sudah hadir hari ini!</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    /**
     * AJAX Polling: Update data tanpa reload halaman
     * Sangat berguna saat Kiosk Barcode sedang digunakan di depan kantor.
     */
    function refreshMonitoring() {
        fetch("{{ route('admin.transaksi.attendance.monitoring') }}", {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Update bagian stat & tabel saja agar tidak kedip
            document.getElementById('monitoring-wrapper').innerHTML = doc.getElementById('monitoring-wrapper').innerHTML;
        })
        .catch(error => console.warn('Monitoring refresh failed:', error));
    }

    // Refresh setiap 10 detik
    setInterval(refreshMonitoring, 10000);
</script>
@endsection