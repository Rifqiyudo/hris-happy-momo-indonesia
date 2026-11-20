@extends('admin.layouts.master')
@section('title', 'Master Indikator KPI')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800">Indikator Penilaian KPI</h1>
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mt-1">Konfigurasi Bobot & Kriteria Kinerja</p>
        </div>
        <button onclick="toggleModal('modal-add')" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition">
            <i class="fas fa-plus mr-2"></i> Tambah Indikator
        </button>
    </div>

    {{-- Progress Bobot --}}
    <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Total Akumulasi Bobot</p>
            <span class="text-sm font-black {{ $totalWeight == 100 ? 'text-emerald-500' : 'text-amber-500' }}">{{ $totalWeight }}% / 100%</span>
        </div>
        <div class="w-full h-4 bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full {{ $totalWeight == 100 ? 'bg-emerald-500' : 'bg-amber-500' }} transition-all duration-500" style="width: {{ $totalWeight }}%"></div>
        </div>
        @if($totalWeight != 100)
            <p class="text-[10px] font-bold text-amber-600 mt-2 italic">* Total bobot harus 100% agar penilaian dapat dilakukan.</p>
        @endif
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase">Indikator</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase">Deskripsi</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase text-center">Bobot</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($indicators as $item)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-6 py-4">
                        <p class="text-xs font-black text-slate-800 uppercase">{{ $item->name }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-[11px] text-slate-500 leading-relaxed">{{ $item->description ?? '-' }}</p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg font-black text-xs">
                            {{ $item->weight }}%
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <form action="{{ route('admin.master.kpi-indicator.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus indikator ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-rose-400 hover:text-rose-600 transition">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL ADD --}}
<div id="modal-add" class="fixed inset-0 bg-slate-900/50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-[2.5rem] p-8 shadow-2xl">
        <h3 class="text-lg font-black text-slate-800 mb-6 uppercase tracking-tight">Tambah Kriteria KPI</h3>
        <form action="{{ route('admin.master.kpi-indicator.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">Nama Indikator</label>
                    <input type="text" name="name" required class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-xs font-bold focus:ring-2 focus:ring-indigo-500" placeholder="Misal: Kedisiplinan">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">Bobot (%)</label>
                    <input type="number" name="weight" required class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-xs font-bold focus:ring-2 focus:ring-indigo-500" placeholder="0 - 100">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">Deskripsi</label>
                    <textarea name="description" rows="3" class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-xs font-bold focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
            </div>
            <div class="flex gap-3 mt-8">
                <button type="button" onclick="toggleModal('modal-add')" class="flex-1 py-4 text-[10px] font-black uppercase text-slate-400 hover:text-slate-600">Batal</button>
                <button type="submit" class="flex-1 bg-indigo-600 py-4 rounded-2xl text-[10px] font-black uppercase text-white shadow-lg shadow-indigo-100">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal(id) {
        document.getElementById(id).classList.toggle('hidden');
    }
</script>
@endsection