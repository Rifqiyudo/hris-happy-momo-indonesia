@extends('admin.layouts.master')
@section('title', 'Approval Cuti & Izin')

@section('content')
<div class="space-y-6 animate-fadeIn">

    {{-- 1. STATS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-amber-100 shadow-sm flex items-center justify-between relative overflow-hidden group">
            <div class="absolute right-0 top-0 h-full w-1 bg-amber-400"></div>
            <div>
                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Menunggu Persetujuan</p>
                <h3 class="text-3xl font-bold text-slate-800">{{ $stats['pending'] }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center group-hover:scale-110 transition">
                <i class="fas fa-clock text-xl"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-emerald-100 shadow-sm flex items-center justify-between relative overflow-hidden group">
            <div class="absolute right-0 top-0 h-full w-1 bg-emerald-400"></div>
            <div>
                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Disetujui (HRD/PIC)</p>
                <h3 class="text-3xl font-bold text-slate-800">{{ $stats['approved'] }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center group-hover:scale-110 transition">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-rose-100 shadow-sm flex items-center justify-between relative overflow-hidden group">
            <div class="absolute right-0 top-0 h-full w-1 bg-rose-400"></div>
            <div>
                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Ditolak</p>
                <h3 class="text-3xl font-bold text-slate-800">{{ $stats['rejected'] }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center group-hover:scale-110 transition">
                <i class="fas fa-times-circle text-xl"></i>
            </div>
        </div>
    </div>

    {{-- 2. MAIN CONTENT AREA --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        {{-- Folder Tabs Navigation --}}
        <div class="flex items-center gap-1 p-2 bg-gray-50/50 border-b border-gray-100 overflow-x-auto">
            @php $currentStatus = request('status', 'Pending'); @endphp
            
            <a href="{{ route('admin.transaksi.leave.index', ['status' => 'Pending']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2 whitespace-nowrap
               {{ $currentStatus == 'Pending' ? 'bg-white text-amber-600 shadow-sm border border-gray-200' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                <i class="fas fa-hourglass-half text-xs"></i> Menunggu
                @if($stats['pending'] > 0)
                    <span class="bg-amber-100 text-amber-700 text-[10px] px-1.5 py-0.5 rounded-md ml-1">{{ $stats['pending'] }}</span>
                @endif
            </a>

            <a href="{{ route('admin.transaksi.leave.index', ['status' => 'Approved']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2 whitespace-nowrap
               {{ $currentStatus == 'Approved' ? 'bg-white text-emerald-600 shadow-sm border border-gray-200' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                <i class="fas fa-check text-xs"></i> Disetujui
            </a>

            <a href="{{ route('admin.transaksi.leave.index', ['status' => 'Rejected']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2 whitespace-nowrap
               {{ $currentStatus == 'Rejected' ? 'bg-white text-rose-600 shadow-sm border border-gray-200' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                <i class="fas fa-times text-xs"></i> Ditolak
            </a>

            <div class="w-px h-6 bg-gray-300 mx-2"></div>

            <a href="{{ route('admin.transaksi.leave.index', ['status' => 'all']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2 whitespace-nowrap
               {{ $currentStatus == 'all' ? 'bg-white text-indigo-600 shadow-sm border border-gray-200' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                <i class="fas fa-list text-xs"></i> Semua Riwayat
            </a>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-white text-slate-500 font-bold uppercase text-[11px] tracking-wider border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Karyawan</th>
                        <th class="px-6 py-4">Jenis & Alasan</th>
                        <th class="px-6 py-4">Tanggal / Durasi</th>
                        <th class="px-6 py-4 text-center">Bukti</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($leaves as $item)
                    <tr class="hover:bg-slate-50/50 transition cursor-pointer" onclick="showDetail('{{ $item->id }}')">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $item->user->avatar_url }}" 
                                     class="w-10 h-10 rounded-2xl object-cover shadow-sm border border-slate-100 bg-slate-50"
                                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($item->user->name) }}&background=EEEFFF&color=4f46e5&bold=true'">
                                <div>
                                    <p class="font-bold text-slate-800 leading-tight">{{ $item->user->name }}</p>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">
                                        {{ $item->user->position->name ?? 'Staff' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <span class="font-bold text-indigo-600 text-[10px] block mb-1 uppercase tracking-wider">
                                {{ str_replace('_', ' ', $item->type) }}
                            </span>
                            <p class="text-xs text-gray-500 italic line-clamp-1">"{{ Str::limit($item->reason, 40) }}"</p>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="font-mono text-slate-700 font-bold text-xs">
                                    {{ \Carbon\Carbon::parse($item->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($item->end_date)->format('d M Y') }}
                                </span>
                                <span class="inline-flex w-fit items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500">
                                    {{ $item->total_days }} Hari
                                </span>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-center">
                            @if($item->attachment)
                                <a href="{{ asset('storage/'.$item->attachment) }}" target="_blank" onclick="event.stopPropagation()" class="text-indigo-600 hover:text-indigo-800">
                                    <i class="fas fa-file-alt text-lg"></i>
                                </a>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-center">
                            @php
                                $statusStyles = [
                                    'Pending' => 'bg-amber-50 text-amber-600 border-amber-100',
                                    'Approved_by_pic' => 'bg-blue-50 text-blue-600 border-blue-100',
                                    'Approved_by_admin' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                    'Rejected_by_pic' => 'bg-rose-50 text-rose-600 border-rose-100',
                                    'Rejected_by_admin' => 'bg-rose-50 text-rose-600 border-rose-100',
                                ];
                                $currentStyle = $statusStyles[$item->status] ?? 'bg-slate-50 text-slate-600';
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border {{ $currentStyle }}">
                                {{ str_replace('_', ' ', $item->status) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-center" onclick="event.stopPropagation()">
                            @if(
                                (Auth::user()->role === 'pic' && $item->status === 'Pending') || 
                                (Auth::user()->role === 'admin' && ($item->status === 'Pending' || $item->status === 'Approved_by_pic'))
                            )
                                <div class="flex justify-center gap-2">
                                    <button onclick="confirmApprove('{{ $item->id }}', '{{ $item->user->name }}')" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition flex items-center justify-center">
                                        <i class="fas fa-check text-xs"></i>
                                    </button>
                                    <button onclick="confirmReject('{{ $item->id }}', '{{ $item->user->name }}')" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition flex items-center justify-center">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                    
                                    <form id="approve-form-{{ $item->id }}" action="{{ route('admin.transaksi.leave.approve', $item->id) }}" method="POST" class="hidden">@csrf</form>
                                    <form id="reject-form-{{ $item->id }}" action="{{ route('admin.transaksi.leave.reject', $item->id) }}" method="POST" class="hidden">@csrf</form>
                                </div>
                            @else
                                <span class="text-slate-300 text-[10px] font-black italic uppercase">Processed</span>
                            @endif
                        </td>
                    </tr>

                    <div id="data-{{ $item->id }}" class="hidden" 
                         data-name="{{ $item->user->name }}"
                         data-nik="{{ $item->user->nik ?? '—' }}"
                         data-avatar="{{ $item->user->avatar_url }}"
                         data-type="{{ str_replace('_', ' ', $item->type) }}"
                         data-duration="{{ $item->total_days }} Hari"
                         data-dates="{{ \Carbon\Carbon::parse($item->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($item->end_date)->format('d M Y') }}"
                         data-reason="{{ $item->reason }}"
                         data-status="{{ $item->status }}">
                    </div>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center text-slate-400 font-bold uppercase text-[10px] tracking-widest">Folder Kosong</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL DETAIL --}}
<div id="modalDetail" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal()"></div>
        <div class="relative bg-white rounded-[2.5rem] shadow-2xl max-w-md w-full p-8 transform transition-all animate-fadeIn">
            <h3 class="text-lg font-black text-slate-800 mb-6 uppercase">Detail Pengajuan</h3>
            
            <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-50">
                <img id="m-avatar" src="" class="w-12 h-12 rounded-xl object-cover">
                <div>
                    <p id="m-name" class="font-black text-slate-800"></p>
                    <p id="m-nik" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"></p>
                </div>
            </div>

            <div class="space-y-4 mb-8">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-slate-50 p-4 rounded-xl">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Jenis</p>
                        <p id="m-type" class="text-xs font-bold text-indigo-600 uppercase"></p>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-xl">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Durasi</p>
                        <p id="m-duration" class="text-xs font-bold text-slate-700"></p>
                    </div>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl">
                    <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Alasan</p>
                    <p id="m-reason" class="text-xs font-medium text-slate-600 italic"></p>
                </div>
            </div>

            <button onclick="closeModal()" class="w-full py-4 bg-slate-100 text-slate-500 font-black rounded-xl hover:bg-slate-200 transition uppercase text-[10px]">Tutup</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showDetail(id) {
        const data = document.getElementById('data-' + id);
        if(!data) return;
        document.getElementById('m-name').innerText = data.getAttribute('data-name');
        document.getElementById('m-nik').innerText = 'NIK: ' + data.getAttribute('data-nik');
        document.getElementById('m-type').innerText = data.getAttribute('data-type');
        document.getElementById('m-duration').innerText = data.getAttribute('data-duration');
        document.getElementById('m-reason').innerText = data.getAttribute('data-reason');
        document.getElementById('m-avatar').src = data.getAttribute('data-avatar');
        document.getElementById('modalDetail').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('modalDetail').classList.add('hidden');
    }

    function confirmApprove(id, name) {
        Swal.fire({
            title: 'Setujui Pengajuan?',
            text: "Konfirmasi persetujuan untuk " + name,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            confirmButtonText: 'Ya, Setujui',
            customClass: { popup: 'rounded-3xl' }
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('approve-form-' + id).submit();
        });
    }

    function confirmReject(id, name) {
        Swal.fire({
            title: 'Tolak Pengajuan?',
            text: "Berikan alasan penolakan untuk " + name,
            input: 'text',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Tolak',
            inputValidator: (value) => { if (!value) return 'Alasan wajib diisi!' },
            customClass: { popup: 'rounded-3xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('reject-form-' + id);
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = "admin_note"; 
                input.value = result.value;
                form.appendChild(input);
                form.submit();
            }
        });
    }
</script>
@endsection