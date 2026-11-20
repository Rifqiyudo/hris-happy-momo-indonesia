@extends('admin.layouts.master')

@section('title', 'Tambah Pengumuman Baru')

@section('content')
<div class="max-w-4xl mx-auto pb-20 px-4">
    {{-- Breadcrumb --}}
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest text-slate-400">
            <li><a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 transition">Home</a></li>
            <li><i class="fas fa-chevron-right text-[8px]"></i></li>
            <li><a href="{{ route('admin.master.announcement.index') }}" class="hover:text-indigo-600 transition">Master Pengumuman</a></li>
            <li><i class="fas fa-chevron-right text-[8px]"></i></li>
            <li class="text-slate-600">Buat Baru</li>
        </ol>
    </nav>

    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50 bg-gradient-to-r from-white to-slate-50/50">
            <h1 class="text-2xl font-black text-slate-700 tracking-tight">Buat Pengumuman</h1>
            <p class="text-slate-400 text-xs mt-1">Informasi ini akan muncul di dashboard user yang dipilih.</p>
        </div>

        {{-- Error Handling --}}
        @if ($errors->any())
            <div class="mx-8 mt-6 p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-xl">
                <p class="text-xs font-black text-rose-700 uppercase tracking-widest">Periksa Kembali Input Anda:</p>
                <ul class="mt-1 list-disc list-inside text-xs text-rose-600 font-bold">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM DITAMBAHKAN enctype="multipart/form-data" --}}
        <form action="{{ route('admin.master.announcement.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Judul Pengumuman</label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Libur Hari Raya" 
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-4 px-6 text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Tipe Label</label>
                    <select name="type" class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-4 px-6 text-sm font-bold outline-none cursor-pointer focus:ring-4 focus:ring-indigo-500/10 transition-all">
                        <option value="info">Info (Biru)</option>
                        <option value="warning">Peringatan (Kuning)</option>
                        <option value="danger">Penting (Merah)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Target Role</label>
                    <select name="target" class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-4 px-6 text-sm font-bold outline-none cursor-pointer focus:ring-4 focus:ring-indigo-500/10 transition-all">
                        <option value="all">Semua Karyawan</option>
                        <option value="employee">Staff Saja</option>
                        <option value="pic">Manager / PIC Saja</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Khusus Departemen</label>
                    <select name="department_id" class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-4 px-6 text-sm font-bold outline-none cursor-pointer focus:ring-4 focus:ring-indigo-500/10 transition-all">
                        <option value="">Semua Departemen</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Status Publikasi</label>
                    <select name="is_active" class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-4 px-6 text-sm font-bold outline-none cursor-pointer focus:ring-4 focus:ring-indigo-500/10 transition-all">
                        <option value="1">Aktif (Terbitkan)</option>
                        <option value="0">Draft (Sembunyikan)</option>
                    </select>
                </div>
            </div>

            {{-- Isi Pesan --}}
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Isi Pesan</label>
                <textarea name="content" rows="6" required placeholder="Tuliskan detail pengumuman..." 
                    class="w-full bg-slate-50 border border-slate-200 rounded-3xl py-4 px-6 text-sm font-medium focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">{{ old('content') }}</textarea>
            </div>

            {{-- LAMPIRAN FILE --}}
            <div class="bg-slate-50 p-6 rounded-3xl border border-dashed border-slate-300">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Lampiran File (Opsional)</label>
                <input type="file" name="attachment" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-all cursor-pointer">
                <p class="text-[9px] text-slate-400 font-bold mt-2 uppercase tracking-wider">Format: PDF, JPG, PNG, DOCX (Max 5MB)</p>
            </div>

            <div class="pt-4 flex items-center justify-between border-t border-slate-50">
                <button type="submit" class="bg-indigo-600 text-white px-10 py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-indigo-700 transition shadow-xl shadow-indigo-100 active:scale-95 flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i> Terbitkan Sekarang
                </button>
                <a href="{{ route('admin.master.announcement.index') }}" class="text-slate-400 font-bold text-xs hover:text-slate-600 transition">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection