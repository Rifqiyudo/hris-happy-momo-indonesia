@extends('employee.layouts.app')
@section('title', 'Ajukan Klaim')

@section('content')
<div class="page-fade space-y-8 pb-10 px-4">
    
    {{-- TOP NAVIGATION --}}
    <div class="flex items-center justify-between py-4">
        <a href="{{ route('employee.reimbursement.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-sm border border-gray-100 text-slate-500 active:scale-95 transition">
            <i class="fas fa-chevron-left text-xs"></i>
        </a>
        <h1 class="text-lg font-extrabold text-slate-800 tracking-tight">Form Pengajuan Klaim</h1>
        <div class="w-10"></div>
    </div>

    <form action="{{ route('employee.reimbursement.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- CARD 1: INFORMASI UTAMA --}}
        <div class="bg-white rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-50 space-y-6">
            {{-- Nama Barang --}}
            <div class="space-y-2">
                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Kebutuhan / Barang</label>
                <input type="text" name="item_name" placeholder="Misal: Bensin, Parkir, Medis..." required
                    class="w-full px-5 py-4 bg-slate-50 border-transparent rounded-2xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all placeholder-slate-300 outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Kategori --}}
                <div class="space-y-2">
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Kategori</label>
                    <div class="relative">
                        <select name="category" class="w-full px-5 py-4 bg-slate-50 border-transparent rounded-2xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all appearance-none cursor-pointer outline-none">
                            <option value="Transportasi">Transportasi</option>
                            <option value="Medis">Medis</option>
                            <option value="Makan">Makan</option>
                            <option value="Operasional">Operasional</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-[10px] text-slate-300 pointer-events-none"></i>
                    </div>
                </div>
                {{-- Tanggal --}}
                <div class="space-y-2">
                    <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Tanggal Nota</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}"
                        class="w-full px-5 py-4 bg-slate-50 border-transparent rounded-2xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none">
                </div>
            </div>

            {{-- Nominal --}}
            <div class="space-y-2">
                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Nominal Pengeluaran</label>
                <div class="relative">
                    <div class="absolute left-5 top-1/2 -translate-y-1/2 flex items-center gap-2">
                        <span class="text-xs font-black text-indigo-500">Rp</span>
                        <div class="w-[1px] h-4 bg-slate-200"></div>
                    </div>
                    <input type="number" name="amount" placeholder="0" required
                        class="w-full pl-16 pr-5 py-5 bg-indigo-50/30 border-2 border-transparent rounded-2xl text-xl font-black text-slate-800 focus:border-indigo-500 focus:bg-white focus:ring-0 transition-all placeholder-indigo-200 outline-none">
                </div>
            </div>
        </div>

        {{-- CARD 2: BUKTI FISIK --}}
        <div class="bg-white rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-50">
            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-4 block">Foto Struk / Nota</label>
            
            <div id="preview-box" class="hidden relative mb-4">
                <img id="image-preview" src="" class="w-full aspect-[4/3] object-cover rounded-2xl border-2 border-slate-100 shadow-sm">
                <button type="button" onclick="clearImage()" class="absolute -top-2 -right-2 w-8 h-8 bg-rose-500 text-white rounded-full shadow-lg flex items-center justify-center active:scale-75 transition">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <label for="proof_file" id="dropzone" class="flex flex-col items-center justify-center w-full aspect-[4/3] bg-slate-50 border-2 border-dashed border-slate-200 rounded-3xl cursor-pointer hover:bg-indigo-50/50 hover:border-indigo-200 transition-all group">
                <div class="w-14 h-14 bg-white rounded-2xl shadow-sm flex items-center justify-center text-slate-300 group-hover:text-indigo-500 transition-all mb-3">
                    <i class="fas fa-camera text-xl"></i>
                </div>
                <p class="text-[13px] font-bold text-slate-500">Ambil Foto Struk</p>
                <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-tighter">Format JPG/PNG (Max 2MB)</p>
                <input id="proof_file" name="proof_file" type="file" class="hidden" accept="image/*" onchange="previewFile(this)">
            </label>
        </div>

        {{-- CARD 3: CATATAN --}}
        <div class="bg-white rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-50">
            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Catatan Tambahan</label>
            <textarea name="description" rows="3" placeholder="Opsional: Tambahkan keterangan..."
                class="w-full px-5 py-4 bg-slate-50 border-transparent rounded-2xl text-sm font-medium text-slate-600 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all resize-none outline-none"></textarea>
        </div>

        {{-- ACTION BUTTON --}}
        <div class="px-2 pt-2">
            <button type="submit" class="w-full py-5 bg-indigo-600 text-white font-black rounded-2xl shadow-xl shadow-indigo-100 active:scale-95 transition-all flex items-center justify-center gap-3">
                <i class="fas fa-paper-plane text-xs opacity-70"></i>
                Kirim Pengajuan
            </button>
        </div>

    </form>
</div>
@endsection

@section('scripts')
<script>
    function previewFile(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('preview-box').classList.remove('hidden');
                document.getElementById('dropzone').classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function clearImage() {
        document.getElementById('proof_file').value = "";
        document.getElementById('preview-box').classList.add('hidden');
        document.getElementById('dropzone').classList.remove('hidden');
    }
</script>
@endsection