@extends('admin.layouts.master')

@section('title', 'Pengajuan Lembur Karyawan')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8 animate-fadeIn">
    
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 sm:p-8 rounded-[2rem] shadow-xl shadow-slate-100/80 border border-slate-100 relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-50/50 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 text-xs font-black uppercase tracking-widest mb-3">
                <i class="fas fa-business-time"></i> Modul Operasional & SDM
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Pengajuan Lembur Karyawan</h1>
            <p class="text-slate-500 text-sm mt-1 max-w-xl">Kelola, tinjau, dan berikan persetujuan permohonan lembur seluruh pegawai secara real-time dengan sistem verifikasi cepat.</p>
        </div>
        <div class="flex items-center gap-3 relative z-10">
            <div class="px-5 py-3 bg-slate-900 text-white rounded-2xl shadow-lg shadow-slate-200 flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-indigo-600 flex items-center justify-center text-xs font-bold shadow-md shadow-indigo-500/50">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest font-black">Total Pengajuan</p>
                    <p class="text-base font-black leading-none mt-0.5">{{ $overtimes->count() }} Pegawai</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Notifikasi Flash Message Sukses --}}
    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-5 rounded-2xl shadow-sm flex items-center gap-4 animate-fadeIn" role="alert">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-emerald-200 flex-shrink-0">
                <i class="fas fa-check"></i>
            </div>
            <div>
                <p class="text-xs font-black uppercase tracking-wider text-emerald-600">Berhasil Disimpan</p>
                <p class="text-sm font-semibold mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- Control & Filter Bar (Live Search & Quick Tabs) --}}
    <div class="bg-white p-4 rounded-3xl shadow-sm border border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
        {{-- Status Filter Pills --}}
        <div class="flex items-center gap-1.5 w-full sm:w-auto overflow-x-auto pb-2 sm:pb-0 no-scrollbar">
            <button onclick="filterStatus('all')" id="btn-all" class="filter-btn px-4 py-2 rounded-2xl text-xs font-bold bg-indigo-600 text-white shadow-md shadow-indigo-200 transition-all">
                Semua Status
            </button>
            <button onclick="filterStatus('pending')" id="btn-pending" class="filter-btn px-4 py-2 rounded-2xl text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">
                Menunggu
            </button>
            <button onclick="filterStatus('approved')" id="btn-approved" class="filter-btn px-4 py-2 rounded-2xl text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">
                Disetujui
            </button>
            <button onclick="filterStatus('rejected')" id="btn-rejected" class="filter-btn px-4 py-2 rounded-2xl text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">
                Ditolak
            </button>
        </div>

        {{-- Search Input Box --}}
        <div class="w-full sm:w-72 relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fas fa-search text-xs"></i>
            </span>
            <input type="text" id="search-input" onkeyup="searchTable()" placeholder="Cari nama atau NIK..." 
                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
        </div>
    </div>

    {{-- Tabel Daftar Pengajuan Lembur Modern --}}
    <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-100 border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="overtime-table">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-black uppercase tracking-wider text-slate-400">
                        <th class="py-4 px-6">Karyawan</th>
                        <th class="py-4 px-6">Cabang / Jabatan</th>
                        <th class="py-4 px-6">Jadwal & Waktu Lembur</th>
                        <th class="py-4 px-6">Alasan / Keterangan</th>
                        <th class="py-4 px-6 text-center">Status</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($overtimes as $overtime)
                        <tr class="overtime-row hover:bg-slate-50/80 transition-all duration-200 group" data-status="{{ strtolower($overtime->status) }}">
                            {{-- Kolom Karyawan & NIK --}}
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-500 to-violet-600 text-white font-bold flex items-center justify-center text-xs shadow-md shadow-indigo-100 flex-shrink-0">
                                        {{ strtoupper(substr($overtime->user->name ?? 'U', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">
                                            {{ $overtime->user->name ?? '-' }}
                                        </div>
                                        <div class="text-xs font-semibold text-slate-400 mt-0.5">
                                            NIK: <span class="text-slate-600 font-mono">{{ $overtime->user->nik ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Kolom Cabang & Posisi --}}
                            <td class="py-4 px-6">
                                <div class="font-semibold text-slate-800">{{ $overtime->user->branch->name ?? 'Kantor Pusat' }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ $overtime->user->position->name ?? '-' }}</div>
                            </td>

                            {{-- Kolom Tanggal & Jam --}}
                            <td class="py-4 px-6">
                                <div class="inline-flex items-center gap-1.5 font-bold text-indigo-600 bg-indigo-50/80 px-2.5 py-1 rounded-xl text-xs mb-1">
                                    <i class="far fa-calendar-alt"></i> 
                                    {{ \Carbon\Carbon::parse($overtime->date)->translatedFormat('d M Y') }}
                                </div>
                                <div class="text-xs font-semibold text-slate-500 flex items-center gap-1.5">
                                    <i class="far fa-clock text-slate-400"></i> {{ $overtime->start_time }} - {{ $overtime->end_time }} WIB
                                </div>
                            </td>

                            {{-- Kolom Alasan Lembur --}}
                            <td class="py-4 px-6 max-w-xs">
                                <p class="text-slate-600 italic bg-slate-50/80 p-3 rounded-2xl text-xs border border-slate-100/80 leading-relaxed">
                                    "{{ $overtime->reason }}"
                                </p>
                            </td>

                            {{-- Kolom Status Badge --}}
                            <td class="py-4 px-6 text-center">
                                @if($overtime->status == 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-black bg-amber-50 text-amber-600 border border-amber-200/60 shadow-sm animate-pulse">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu
                                    </span>
                                @elseif($overtime->status == 'approved')
                                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-black bg-emerald-50 text-emerald-600 border border-emerald-200/60 shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-black bg-rose-50 text-rose-600 border border-rose-200/60 shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Ditolak
                                    </span>
                                @endif
                            </td>

                            {{-- Kolom Tombol Aksi (Approve / Reject) --}}
                            <td class="py-4 px-6 text-center">
                                @if($overtime->status == 'pending')
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Form Aksi Setujui --}}
                                        <form action="{{ route('admin.transaksi.overtime.approve', $overtime->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                    onclick="return confirm('Apakah Anda yakin ingin menyetujui lembur ini?')"
                                                    class="w-10 h-10 bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white rounded-2xl flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md hover:scale-105 active:scale-95 group/btn"
                                                    title="Setujui Pengajuan">
                                                <i class="fas fa-check text-sm group-hover/btn:rotate-12 transition-transform"></i>
                                            </button>
                                        </form>

                                        {{-- Form Aksi Tolak --}}
                                        <form action="{{ route('admin.transaksi.overtime.reject', $overtime->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                    onclick="return confirm('Apakah Anda yakin ingin menolak lembur ini?')"
                                                    class="w-10 h-10 bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white rounded-2xl flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md hover:scale-105 active:scale-95 group/btn"
                                                    title="Tolak Pengajuan">
                                                <i class="fas fa-times text-sm group-hover/btn:rotate-90 transition-transform"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs font-bold text-slate-400 bg-slate-100 px-3.5 py-2 rounded-xl inline-block">
                                        Telah Diproses
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-16">
                                <div class="w-20 h-20 bg-indigo-50 text-indigo-500 rounded-3xl flex items-center justify-center mx-auto mb-4 text-3xl shadow-inner">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <h3 class="text-slate-800 font-bold text-base">Belum Ada Data Pengajuan Lembur</h3>
                                <p class="text-slate-400 text-xs mt-1 max-w-sm mx-auto">Data permohonan lembur dari pegawai akan muncul secara otomatis di sini saat diajukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Skrip Interaktif Tambahan untuk Search & Filter Modern --}}
<script>
    function searchTable() {
        const input = document.getElementById('search-input').value.toLowerCase();
        const rows = document.querySelectorAll('.overtime-row');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            if (text.includes(input)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function filterStatus(status) {
        // Atur tombol aktif
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-md', 'shadow-indigo-200');
            btn.classList.add('bg-slate-100', 'text-slate-600');
        });

        const activeBtn = document.getElementById(`btn-${status}`);
        if(activeBtn) {
            activeBtn.classList.remove('bg-slate-100', 'text-slate-600');
            activeBtn.classList.add('bg-indigo-600', 'text-white', 'shadow-md', 'shadow-indigo-200');
        }

        const rows = document.querySelectorAll('.overtime-row');
        rows.forEach(row => {
            const rowStatus = row.getAttribute('data-status');
            if (status === 'all' || rowStatus === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
@endsection