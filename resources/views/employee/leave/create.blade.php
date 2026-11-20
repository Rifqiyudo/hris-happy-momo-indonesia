@extends('employee.layouts.app')
@section('title', 'Buat Pengajuan')

@section('content')
<div class="p-6 pb-24 max-w-lg mx-auto">

    {{-- HEADER: Tombol Kembali & Judul --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('employee.leave.index') }}" class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 rounded-xl shadow-sm hover:bg-gray-50 transition active:scale-90">
            <i class="fas fa-arrow-left text-slate-600"></i>
        </a>
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight">Form Pengajuan</h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Lengkapi detail izin/cuti Anda</p>
        </div>
    </div>

    {{-- TAMPILAN PESAN ERROR GLOBAL --}}
    @if($errors->any())
        <div class="mb-5 p-4 bg-rose-50 border border-rose-100 rounded-xl">
            <div class="flex gap-3">
                <i class="fas fa-exclamation-circle text-rose-500 mt-0.5"></i>
                <ul class="text-xs text-rose-600 font-bold space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- CARD FORMULIR --}}
    <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50 border border-gray-100">
        
        <form action="{{ route('employee.leave.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- 1. JENIS PENGAJUAN --}}
            <div class="mb-5">
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-2 tracking-widest">Jenis Pengajuan</label>
                <div class="relative">
                    <select name="type" required class="w-full appearance-none bg-slate-50 border @error('type') border-rose-300 @else border-slate-200 @enderror rounded-2xl px-4 py-4 text-sm font-bold text-slate-700 focus:outline-none focus:border-indigo-500 focus:bg-white transition cursor-pointer">
                        <option value="">-- Pilih Jenis --</option>
                        <option value="Sakit" {{ old('type') == 'Sakit' ? 'selected' : '' }}>Sakit (S1)</option>
                        <option value="Izin" {{ old('type') == 'Izin' ? 'selected' : '' }}>Izin (Keperluan Pribadi)</option>
                        <option value="Cuti" {{ old('type') == 'Cuti' ? 'selected' : '' }}>Cuti Tahunan</option>
                        <option value="Dispensation" {{ old('type') == 'Dispensation' ? 'selected' : '' }}>Dispensasi</option>
                        <option value="Lainnya" {{ old('type') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    <div class="absolute right-4 top-5 text-slate-400 pointer-events-none">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            {{-- 2. TANGGAL (GRID 2 KOLOM) --}}
            <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase mb-2 tracking-widest">Dari Tanggal</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required 
                        class="w-full bg-slate-50 border @error('start_date') border-rose-300 @else border-slate-200 @enderror rounded-2xl px-4 py-4 text-sm font-bold text-slate-700 focus:outline-none focus:border-indigo-500 focus:bg-white transition">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase mb-2 tracking-widest">Sampai Tanggal</label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date', date('Y-m-d')) }}" required 
                        class="w-full bg-slate-50 border @error('end_date') border-rose-300 @else border-slate-200 @enderror rounded-2xl px-4 py-4 text-sm font-bold text-slate-700 focus:outline-none focus:border-indigo-500 focus:bg-white transition">
                </div>
            </div>

            {{-- 3. ALASAN --}}
            <div class="mb-5">
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-2 tracking-widest">Alasan Pengajuan</label>
                <textarea name="reason" rows="4" required placeholder="Contoh: Mengalami demam tinggi, keperluan keluarga mendesak, atau rencana cuti tahunan..." 
                    class="w-full bg-slate-50 border @error('reason') border-rose-300 @else border-slate-200 @enderror rounded-2xl px-4 py-4 text-sm font-bold text-slate-700 focus:outline-none focus:border-indigo-500 focus:bg-white transition resize-none">{{ old('reason') }}</textarea>
            </div>

            {{-- 4. UPLOAD BUKTI --}}
            <div class="mb-8">
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-2 tracking-widest">Lampiran / Bukti (Opsional)</label>
                
                <div id="upload-container" class="relative border-2 border-dashed @error('attachment') border-rose-300 bg-rose-50 @else border-slate-200 bg-slate-50 @enderror rounded-2xl p-8 text-center hover:bg-slate-100 hover:border-indigo-300 transition group cursor-pointer">
                    <input type="file" name="attachment" id="file-upload" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept=".jpg,.jpeg,.png,.pdf">
                    
                    <div class="flex flex-col items-center justify-center gap-2">
                        <div id="upload-icon-box" class="w-14 h-14 bg-white text-indigo-500 rounded-2xl shadow-sm flex items-center justify-center group-hover:scale-110 transition duration-300">
                            <i class="fas fa-cloud-upload-alt text-2xl"></i>
                        </div>
                        <p class="text-xs font-black text-slate-600 group-hover:text-indigo-600 transition" id="file-name">
                            Ketuk untuk unggah dokumen
                        </p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Format: JPG, PNG, atau PDF (Maks. 2MB)</p>
                    </div>
                </div>
            </div>

            {{-- TOMBOL SUBMIT --}}
            <button type="submit" class="w-full py-5 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl shadow-xl shadow-indigo-100 transition transform active:scale-95 flex items-center justify-center gap-3">
                <i class="fas fa-paper-plane text-xs"></i> 
                <span class="text-[11px] uppercase tracking-widest">Kirim Pengajuan Ke Admin</span>
            </button>

        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // --- 1. SCRIPT INTERAKSI FILE UPLOAD ---
    const fileInput = document.getElementById('file-upload');
    const fileName = document.getElementById('file-name');
    const uploadContainer = document.getElementById('upload-container');
    const iconBox = document.getElementById('upload-icon-box');

    fileInput.addEventListener('change', function() {
        if(this.files && this.files.length > 0) {
            const name = this.files[0].name;
            const size = (this.files[0].size / 1024 / 1024).toFixed(2);
            
            // Validasi Sederhana di Client Side
            if(this.files[0].size > 2 * 1024 * 1024) {
                alert('File terlalu besar! Maksimal 2MB.');
                this.value = '';
                return;
            }

            // UI Update saat file berhasil dipilih
            fileName.innerHTML = `<i class="fas fa-check-circle text-emerald-500 mr-1"></i> ${name} (${size} MB)`;
            fileName.classList.add('text-indigo-600');
            uploadContainer.classList.add('border-indigo-400', 'bg-indigo-50/30');
            iconBox.classList.replace('text-indigo-500', 'text-emerald-500');
        } else {
            fileName.textContent = "Ketuk untuk unggah dokumen";
            fileName.classList.remove('text-indigo-600');
            uploadContainer.classList.remove('border-indigo-400', 'bg-indigo-50/30');
            iconBox.classList.replace('text-emerald-500', 'text-indigo-500');
        }
    });

    // --- 2. SCRIPT VALIDASI TANGGAL ---
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    startDateInput.addEventListener('change', function() {
        // Otomatis set minimal tanggal selesai = tanggal mulai
        endDateInput.min = this.value;
        if(endDateInput.value < this.value) {
            endDateInput.value = this.value;
        }
    });
</script>
@endsection