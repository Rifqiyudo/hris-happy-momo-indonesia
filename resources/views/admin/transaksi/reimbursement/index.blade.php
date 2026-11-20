@extends('admin.layouts.master')
@section('title', 'Persetujuan Klaim & Kasbon')

@section('content')
<div class="space-y-6">
    
    {{-- HEADER & KARTU STATISTIK --}}
    <div class="flex flex-col md:flex-row gap-6">
        <div class="flex-1 bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm flex flex-col justify-center">
            <h2 class="font-black text-slate-800 text-xl tracking-tight">Klaim & Kasbon</h2>
            <p class="text-xs text-slate-500 mt-1 leading-relaxed">Kelola dan validasi permohonan pengeluaran finansial dari karyawan Anda.</p>
        </div>
        
        <div class="flex-1 grid grid-cols-3 gap-4">
            <div class="bg-amber-50 rounded-3xl p-5 border border-amber-100 flex flex-col justify-center shadow-sm">
                <span class="text-[10px] font-black text-amber-500 uppercase tracking-widest mb-1">Menunggu</span>
                <span class="text-2xl font-black text-amber-600">{{ $stats['pending'] ?? 0 }}</span>
            </div>
            <div class="bg-emerald-50 rounded-3xl p-5 border border-emerald-100 flex flex-col justify-center shadow-sm">
                <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-1">Disetujui</span>
                <span class="text-2xl font-black text-emerald-600">{{ $stats['approved'] ?? 0 }}</span>
            </div>
            <div class="bg-rose-50 rounded-3xl p-5 border border-rose-100 flex flex-col justify-center shadow-sm">
                <span class="text-[10px] font-black text-rose-500 uppercase tracking-widest mb-1">Ditolak</span>
                <span class="text-2xl font-black text-rose-600">{{ $stats['rejected'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    {{-- AREA FILTER PENCARIAN --}}
    <div class="bg-white rounded-[2rem] p-4 border border-slate-100 shadow-sm">
        <form action="{{ route('admin.transaksi.reimbursement.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama karyawan, NIK, atau deskripsi klaim..." 
                       class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
            </div>
            <div class="w-full md:w-64">
                <select name="status" onchange="this.form.submit()" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-600 outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all appearance-none cursor-pointer">
                    <option value="">-- Semua Status --</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <a href="{{ route('admin.transaksi.reimbursement.index') }}" class="px-6 py-3 bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-700 font-bold rounded-2xl text-xs uppercase tracking-widest flex items-center justify-center transition-all">
                Reset
            </a>
        </form>
    </div>

    {{-- TABEL DAFTAR KLAIM --}}
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50/50">
                        <th class="px-6 py-5">Tanggal</th>
                        <th class="px-6 py-5">Karyawan</th>
                        <th class="px-6 py-5">Detail Klaim</th>
                        <th class="px-6 py-5">Nominal</th>
                        <th class="px-6 py-5">Status</th>
                        <th class="px-6 py-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($reimbursements as $item)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        {{-- Tanggal --}}
                        <td class="px-6 py-5">
                            <span class="font-bold text-slate-700">{{ $item->created_at->format('d M Y') }}</span>
                            <span class="block text-[10px] text-slate-400 font-bold mt-0.5">{{ $item->created_at->format('H:i') }} WIB</span>
                        </td>
                        
                        {{-- Identitas Karyawan --}}
                        <td class="px-6 py-5">
                            <div class="font-black text-slate-800">{{ $item->user->name ?? 'Karyawan Dihapus' }}</div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">
                                {{ $item->user->nik ?? '-' }} • {{ $item->user->department->name ?? 'Umum' }}
                            </div>
                        </td>

                        {{-- Deskripsi Klaim --}}
                        <td class="px-6 py-5">
                            <div class="font-bold text-indigo-600 bg-indigo-50 inline-block px-2 py-0.5 rounded text-[10px] uppercase tracking-widest mb-1">
                                {{ $item->type ?? 'Reimbursement' }}
                            </div>
                            <div class="text-xs text-slate-600 font-medium line-clamp-2" title="{{ $item->description }}">
                                {{ $item->description ?? 'Tidak ada deskripsi' }}
                            </div>
                            @if($item->attachment)
                                <a href="{{ asset('storage/'.$item->attachment) }}" target="_blank" class="text-[10px] font-bold text-slate-400 hover:text-indigo-600 mt-1 inline-flex items-center gap-1">
                                    <i class="fas fa-paperclip"></i> Lihat Bukti
                                </a>
                            @endif
                        </td>

                        {{-- Nominal --}}
                        <td class="px-6 py-5 font-black text-slate-800 text-base">
                            Rp {{ number_format($item->amount ?? 0, 0, ',', '.') }}
                        </td>

                        {{-- Status Badge --}}
                        <td class="px-6 py-5">
                            @if(strtolower($item->status) == 'approved')
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-emerald-100">
                                    <i class="fas fa-check-circle"></i> Disetujui
                                </div>
                            @elseif(strtolower($item->status) == 'rejected')
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-50 text-rose-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-rose-100" title="{{ $item->rejection_reason }}">
                                    <i class="fas fa-times-circle"></i> Ditolak
                                </div>
                            @else
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-amber-100 animate-pulse">
                                    <i class="fas fa-clock"></i> Pending
                                </div>
                            @endif
                        </td>

                        {{-- Tombol Aksi (Centang untuk Menyetujui, Silang untuk Menolak) --}}
                        <td class="px-6 py-5 text-right">
                            @if(strtolower($item->status) == 'pending')
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Tombol Approve (Tanda Centang Hijau) --}}
                                    <form action="{{ route('admin.transaksi.reimbursement.approve', $item->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menyetujui klaim senilai Rp {{ number_format($item->amount ?? 0, 0, ',', '.') }} ini?');">
                                        @csrf
                                        <button type="submit" class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-500 hover:bg-emerald-500 hover:text-white transition-colors flex items-center justify-center shadow-sm" title="Setujui Pengajuan">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    
                                    {{-- Tombol Reject (Tanda Silang Merah - Membuka Modal Alasan) --}}
                                    <button type="button" onclick="openRejectModal({{ $item->id }}, '{{ $item->user->name ?? '' }}')" class="w-8 h-8 rounded-full bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-colors flex items-center justify-center shadow-sm" title="Tolak Pengajuan">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @else
                                {{-- Tombol Reset Pending (jika status sudah disetujui/ditolak tapi ingin diubah kembali) --}}
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('admin.transaksi.reimbursement.update-status', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="Pending">
                                        <button type="submit" class="px-2.5 py-1 bg-slate-100 hover:bg-amber-500 hover:text-white text-slate-500 rounded-lg text-[9px] font-black uppercase tracking-wider transition-colors shadow-sm" title="Kembalikan ke Pending">
                                            <i class="fas fa-undo mr-1"></i> Reset Pending
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-16">
                            <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center text-2xl mx-auto mb-3">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <p class="text-slate-500 font-black text-sm uppercase tracking-widest">Data Tidak Ditemukan</p>
                            <p class="text-xs text-slate-400 mt-1">Belum ada pengajuan klaim/kasbon dengan filter tersebut.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($reimbursements->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $reimbursements->links() }}
        </div>
        @endif
    </div>
</div>

{{-- MODAL PENOLAKAN KLAIM --}}
<div id="rejectModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity opacity-0" style="transition: opacity 0.3s;">
    <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md overflow-hidden transform scale-95 transition-transform duration-300" id="rejectModalContent">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-rose-50">
            <h3 class="font-black text-rose-600 uppercase tracking-widest text-sm flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i> Tolak Pengajuan
            </h3>
            <button type="button" onclick="closeRejectModal()" class="text-slate-400 hover:text-rose-500 w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="rejectForm" method="POST" action="">
            @csrf
            <div class="p-6">
                <p class="text-xs text-slate-500 mb-4 leading-relaxed">Anda akan menolak klaim dari <strong id="rejectEmployeeName" class="text-slate-800">Karyawan</strong>. Silakan masukkan alasan penolakan agar karyawan tersebut mengetahuinya.</p>
                
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Alasan Penolakan <span class="text-rose-500">*</span></label>
                    <textarea name="reason" rows="3" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-rose-500 resize-none" placeholder="Contoh: Bukti nota tidak valid atau buram..."></textarea>
                </div>
            </div>
            
            <div class="p-6 pt-0 flex gap-3">
                <button type="button" onclick="closeRejectModal()" class="flex-1 py-3 bg-slate-100 text-slate-500 font-bold rounded-xl text-xs uppercase tracking-widest hover:bg-slate-200 transition-colors">Batal</button>
                <button type="submit" class="flex-1 py-3 bg-rose-500 text-white font-black rounded-xl text-xs uppercase tracking-widest hover:bg-rose-600 shadow-md shadow-rose-200 transition-colors">Tolak Klaim</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('rejectModal');
    const modalContent = document.getElementById('rejectModalContent');
    const form = document.getElementById('rejectForm');
    const employeeNameSpan = document.getElementById('rejectEmployeeName');

    function openRejectModal(id, employeeName) {
        // Set Action URL form berdasarkan ID
        form.action = `/admin/transaksi/reimbursement/${id}/reject`;
        employeeNameSpan.textContent = employeeName;
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
        }, 10);
    }

    function closeRejectModal() {
        modal.classList.add('opacity-0');
        modalContent.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            form.reset();
        }, 300);
    }

    // Tutup jika klik diluar area putih
    modal.addEventListener('click', function(e) {
        if(e.target === modal) closeRejectModal();
    });
</script>
@endsection