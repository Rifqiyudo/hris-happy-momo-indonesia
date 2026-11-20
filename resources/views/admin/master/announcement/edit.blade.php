@extends('admin.layouts.master')
@section('title', 'Edit Pengumuman - ' . $announcement->title)

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 pb-24">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Edit Pengumuman</h1>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mt-1">Perbarui informasi sistem untuk karyawan</p>
        </div>
        <a href="{{ route('admin.master.announcement.index') }}" class="bg-white border border-slate-200 px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-500 hover:bg-slate-50 transition shadow-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke List
        </a>
    </div>

    {{-- Alert Error Validation --}}
    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-100 text-rose-600 p-6 rounded-[2rem] mb-8 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <i class="fas fa-exclamation-circle text-lg"></i>
                <h4 class="text-xs font-black uppercase tracking-widest">Terjadi Kesalahan Input</h4>
            </div>
            <ul class="list-disc list-inside text-[10px] font-bold uppercase tracking-tight opacity-80">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden">
        {{-- FORM DITAMBAHKAN enctype="multipart/form-data" --}}
        <form action="{{ route('admin.master.announcement.update', $announcement->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="p-8 space-y-8">
                {{-- Row 1: Judul --}}
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Judul Pengumuman</label>
                    <input type="text" name="title" value="{{ old('title', $announcement->title) }}" required 
                           class="w-full border-slate-100 bg-slate-50 rounded-2xl py-4 px-6 text-sm font-bold focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all outline-none"
                           placeholder="Contoh: Jadwal Libur Lebaran 2024">
                </div>

                {{-- Row 2: Konten --}}
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Isi Pengumuman</label>
                    <textarea name="content" rows="6" required 
                              class="w-full border-slate-100 bg-slate-50 rounded-2xl py-4 px-6 text-sm font-bold focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all outline-none"
                              placeholder="Tuliskan detail pengumuman di sini...">{{ old('content', $announcement->content) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Urgensi/Tipe --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Tingkat Urgensi</label>
                        <select name="type" class="w-full border-slate-100 bg-slate-50 rounded-2xl py-4 px-6 text-xs font-black text-slate-700 outline-none focus:ring-4 focus:ring-blue-500/10">
                            <option value="info" {{ old('type', $announcement->type) == 'info' ? 'selected' : '' }}>🔵 INFO (Biasa)</option>
                            <option value="warning" {{ old('type', $announcement->type) == 'warning' ? 'selected' : '' }}>🟡 WARNING (Penting)</option>
                            <option value="danger" {{ old('type', $announcement->type) == 'danger' ? 'selected' : '' }}>🔴 SANGAT PENTING (Mendesak)</option>
                        </select>
                    </div>

                    {{-- Target Audience --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Target Audiens</label>
                        <select name="target" class="w-full border-slate-100 bg-slate-50 rounded-2xl py-4 px-6 text-xs font-black text-slate-700 outline-none focus:ring-4 focus:ring-blue-500/10">
                            <option value="all" {{ old('target', $announcement->target) == 'all' ? 'selected' : '' }}>Semua Pengguna</option>
                            <option value="employee" {{ old('target', $announcement->target) == 'employee' ? 'selected' : '' }}>Khusus Staff/Employee</option>
                            <option value="pic" {{ old('target', $announcement->target) == 'pic' ? 'selected' : '' }}>Khusus Supervisor/PIC</option>
                        </select>
                    </div>

                    {{-- Department --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Filter Departemen</label>
                        <select name="department_id" class="w-full border-slate-100 bg-slate-50 rounded-2xl py-4 px-6 text-xs font-black text-slate-700 outline-none focus:ring-4 focus:ring-blue-500/10">
                            <option value="">Semua Departemen (Global)</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $announcement->department_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Status Visibilitas</label>
                        <select name="is_active" class="w-full border-slate-100 bg-slate-50 rounded-2xl py-4 px-6 text-xs font-black text-slate-700 outline-none focus:ring-4 focus:ring-blue-500/10">
                            <option value="1" {{ old('is_active', $announcement->is_active) == 1 ? 'selected' : '' }}>AKTIF (Tampilkan)</option>
                            <option value="0" {{ old('is_active', $announcement->is_active) == 0 ? 'selected' : '' }}>DRAFT (Sembunyikan)</option>
                        </select>
                    </div>
                </div>

                {{-- LAMPIRAN FILE (BARU) --}}
                <div class="bg-slate-50 p-6 rounded-3xl border border-dashed border-slate-300">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Lampiran File (Opsional)</label>
                    
                    @if($announcement->file_path)
                        <div class="mb-3 p-3 bg-white rounded-xl border border-blue-100 flex items-center justify-between text-xs font-bold text-blue-600">
                            <span class="truncate"><i class="fas fa-file-alt mr-2"></i> File Saat Ini: {{ basename($announcement->file_path) }}</span>
                        </div>
                    @endif

                    <input type="file" name="attachment" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-all cursor-pointer">
                    <p class="text-[9px] text-slate-400 font-bold mt-2 uppercase tracking-wider">Format: PDF, JPG, PNG, DOCX (Max 5MB)</p>
                </div>
            </div>

            {{-- Footer Button --}}
            <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
                <p class="text-[9px] text-slate-400 font-bold uppercase italic">* Terakhir diperbarui: {{ $announcement->updated_at->format('d/m/Y H:i') }}</p>
                <button type="submit" class="bg-blue-600 text-white font-black px-12 py-4 rounded-2xl shadow-xl shadow-blue-200 hover:bg-blue-700 hover:scale-105 active:scale-95 transition-all uppercase text-[10px] tracking-widest">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection