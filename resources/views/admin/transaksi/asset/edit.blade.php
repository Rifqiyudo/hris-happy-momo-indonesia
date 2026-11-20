@extends('admin.layouts.master')
@section('title', 'Edit Data Aset - ' . ($asset->name ?? $asset->asset_name))

@section('content')

{{-- ========================================================================= --}}
{{-- PENGATURAN TAMBAHAN UNTUK SELECT2 (Konsistensi UI Dropdown)               --}}
{{-- ========================================================================= --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Kustomisasi Select2 agar menyatu dengan desain Tailwind */
    .select2-container--default .select2-selection--single {
        height: 3.5rem !important;
        border-radius: 1rem !important;
        border: 1px solid #e2e8f0 !important;
        display: flex !important;
        align-items: center !important;
        padding-left: 0.5rem !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        font-weight: 700 !important;
        font-size: 0.875rem !important;
        color: #334155 !important;
        background-color: #f8fafc !important; /* slate-50 */
        transition: all 0.3s ease;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 3.5rem !important;
        right: 1rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #334155 !important;
        line-height: normal !important;
    }
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default.select2-container--open .select2-selection--single {
        outline: none !important;
        border-color: #6366f1 !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1) !important;
    }
    .select2-dropdown {
        border-radius: 1rem !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        overflow: hidden !important;
        margin-top: 0.5rem;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border-radius: 0.5rem !important;
        border: 1px solid #e2e8f0 !important;
        padding: 0.75rem !important;
        outline: none !important;
        font-size: 0.875rem;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1) !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #6366f1 !important;
        color: white !important;
    }
    .select2-results__option {
        padding: 0.75rem 1rem !important;
        font-size: 0.875rem !important;
        font-weight: 600 !important;
    }
</style>

<div class="max-w-5xl mx-auto bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-xl animate-fadeIn">
    
    {{-- HEADER --}}
    <div class="flex items-center gap-4 mb-8 border-b border-slate-100 pb-6">
        <a href="{{ route('admin.transaksi.asset.index') }}" class="w-10 h-10 flex items-center justify-center rounded-2xl bg-slate-50 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tighter">Edit Data Aset</h2>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Pembaruan Informasi & Status Perangkat</p>
        </div>
    </div>

    {{-- Notifikasi Error Validasi --}}
    @if ($errors->any())
        <div class="mb-8 p-5 bg-rose-50 border border-rose-100 rounded-2xl">
            <ul class="list-disc list-inside text-rose-600 text-[10px] font-bold uppercase tracking-widest space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- INFORMASI PEMEGANG ASET (Read-Only Context) --}}
    @if($asset->status === 'assigned' && $asset->user)
        <div class="mb-8 p-6 bg-indigo-50/50 border border-indigo-100 rounded-[2rem] flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-2">Status: Sedang Dipinjam Oleh</p>
                <div class="flex items-center gap-4">
                    <img src="{{ $asset->user->avatar ? asset('storage/'.$asset->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($asset->user->name).'&background=4f46e5&color=fff' }}" 
                         class="w-12 h-12 rounded-xl object-cover border border-slate-200 shadow-sm">
                    <div>
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-tight">{{ $asset->user->name }}</h4>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-0.5">
                            {{ $asset->user->position->name ?? 'Staff' }} • {{ $asset->user->unit_kerja ?? $asset->user->branch->name ?? 'Pusat' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="hidden md:block text-right">
                <a href="{{ route('admin.transaksi.asset.show', $asset->id) }}" class="text-[9px] font-black uppercase tracking-widest text-indigo-600 bg-white px-4 py-2 rounded-xl shadow-sm border border-indigo-100 hover:bg-indigo-600 hover:text-white transition">
                    Lihat Riwayat
                </a>
            </div>
        </div>
    @endif

    {{-- FORM EDIT UTAMA --}}
    <form action="{{ route('admin.transaksi.asset.update', $asset->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="bg-white border-2 border-slate-100 rounded-[2rem] p-8 shadow-sm">
            
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-50">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-edit text-indigo-400 text-lg"></i> <span>Detail Spesifikasi Aset</span>
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- Baris 1: Nama, Brand, Serial --}}
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Aset <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $asset->name ?? $asset->asset_name) }}" required 
                           class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-3.5 font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition focus:bg-white">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Merk / Brand</label>
                    <input type="text" name="brand" value="{{ old('brand', $asset->brand ?? $asset->asset_brand) }}" 
                           class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-3.5 font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition focus:bg-white">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Serial Number / Plat <span class="text-rose-500">*</span></label>
                    <input type="text" name="serial_number" value="{{ old('serial_number', $asset->serial_number ?? $asset->asset_serial_number) }}" required 
                           class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-3.5 font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition focus:bg-white">
                </div>

                {{-- Baris 2: Kategori, Harga, Tanggal Beli --}}
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Kategori <span class="text-rose-500">*</span></label>
                    <select name="category" id="selectCategory" required class="w-full">
                        <option value="electronics" {{ old('category', $asset->category) == 'electronics' ? 'selected' : '' }}>Electronics (Laptop, HP)</option>
                        <option value="furniture" {{ old('category', $asset->category) == 'furniture' ? 'selected' : '' }}>Furniture (Meja, Kursi)</option>
                        <option value="vehicle" {{ old('category', $asset->category) == 'vehicle' ? 'selected' : '' }}>Vehicle (Motor, Mobil)</option>
                        <option value="others" {{ old('category', $asset->category) == 'others' ? 'selected' : '' }}>Others (Lain-lain)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Harga Beli (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="purchase_price" value="{{ old('purchase_price', $asset->purchase_price) }}" required 
                           class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-3.5 font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition focus:bg-white">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Tanggal Beli</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('Y-m-d') : '') }}" 
                           class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-3.5 font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition focus:bg-white">
                </div>

                {{-- Baris 3: Lokasi, Kondisi, Status Aset --}}
                <div>
                    <label class="block text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-2 ml-1">Lokasi Fisik Aset</label>
                    <input type="text" name="location" value="{{ old('location', $asset->location) }}" placeholder="Gudang / Kantor Pusat" 
                           class="w-full bg-indigo-50/50 border border-indigo-100 rounded-2xl p-3.5 font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition focus:bg-white">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-2 ml-1">Kondisi Barang <span class="text-rose-500">*</span></label>
                    <select name="condition" id="selectCondition" required class="w-full">
                        <option value="new" {{ old('condition', $asset->condition) == 'new' ? 'selected' : '' }}>Baru (New)</option>
                        <option value="good" {{ old('condition', $asset->condition) == 'good' ? 'selected' : '' }}>Layak Pakai (Good)</option>
                        <option value="broken" {{ old('condition', $asset->condition) == 'broken' ? 'selected' : '' }}>Rusak (Broken)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-amber-500 uppercase tracking-widest mb-2 ml-1">Status Sistem <span class="text-rose-500">*</span></label>
                    <select name="status" id="selectStatus" required class="w-full">
                        <option value="available" {{ old('status', strtolower($asset->status)) == 'available' ? 'selected' : '' }}>Tersedia / Di Gudang</option>
                        <option value="assigned" {{ old('status', strtolower($asset->status)) == 'assigned' ? 'selected' : '' }}>Sedang Dipakai Karyawan</option>
                        <option value="maintenance" {{ old('status', strtolower($asset->status)) == 'maintenance' ? 'selected' : '' }}>Sedang Diperbaiki (Maintenance)</option>
                        <option value="broken" {{ old('status', strtolower($asset->status)) == 'broken' ? 'selected' : '' }}>Rusak / Hilang Total</option>
                    </select>
                </div>

                {{-- Baris 4: Catatan --}}
                <div class="col-span-1 md:col-span-3">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Catatan Tambahan</label>
                    <textarea name="notes" rows="3" placeholder="Tambahkan catatan khusus..."
                              class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition focus:bg-white resize-none">{{ old('notes', $asset->notes ?? $asset->description) }}</textarea>
                </div>

                {{-- Baris 5: Upload Foto Baru / Foto Lama --}}
                <div class="col-span-1 md:col-span-3 mt-4">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Foto / Dokumentasi Perangkat</label>
                    
                    <div class="flex flex-col md:flex-row gap-6 h-48">
                        {{-- Upload Dropzone Baru --}}
                        <div class="relative w-full md:w-1/2 group h-full">
                            <input type="file" name="image" id="imageInput" accept="image/jpeg,image/png,image/svg+xml" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" onchange="previewImage(this)">
                            <div class="w-full h-full bg-slate-50 border-2 border-dashed border-slate-300 rounded-[2rem] flex flex-col items-center justify-center text-slate-400 group-hover:border-indigo-500 group-hover:bg-indigo-50/50 transition-all duration-300 relative overflow-hidden shadow-sm">
                                <div class="absolute inset-0 bg-indigo-500/5 translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                                <i class="fas fa-cloud-upload-alt text-4xl mb-3 text-slate-300 group-hover:text-indigo-500 group-hover:scale-110 group-hover:-translate-y-1 transition-all"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-600 group-hover:text-indigo-700">Pilih / Seret Gambar Baru</span>
                                <div class="mt-3 flex gap-2">
                                    <span class="text-[8px] font-bold uppercase tracking-widest text-slate-400 bg-white px-3 py-1 rounded-full border border-slate-200 shadow-sm"><i class="fas fa-file-image mr-1"></i> JPG/PNG</span>
                                    <span class="text-[8px] font-bold uppercase tracking-widest text-rose-500 bg-rose-50 px-3 py-1 rounded-full border border-rose-200 shadow-sm"><i class="fas fa-weight-hanging mr-1"></i> Max 100MB</span>
                                </div>
                            </div>
                        </div>

                        {{-- Pengecekan Ganda untuk Field Image (Smart Fallback) --}}
                        @php
                            $assetImagePath = $asset->image ?? $asset->photo ?? $asset->asset_image ?? null;
                        @endphp

                        {{-- Preview Gambar Frame Modern --}}
                        <div class="w-full md:w-1/2 h-full flex justify-center items-center bg-slate-900 border-4 border-slate-100 rounded-[2rem] p-3 relative overflow-hidden shadow-inner">
                            @if($assetImagePath)
                                {{-- Jika Sudah Ada Gambar di Database --}}
                                <div class="w-full h-full relative group/preview flex items-center justify-center">
                                    <div class="absolute top-2 left-2 z-20 bg-emerald-500/90 backdrop-blur text-white text-[8px] font-black uppercase tracking-widest px-3 py-1.5 rounded-full shadow-lg border border-emerald-400">
                                        <i class="fas fa-check-circle mr-1"></i> Foto Tersimpan
                                    </div>
                                    <img id="asset-preview" src="{{ asset('storage/' . $assetImagePath) }}" 
                                         onerror="this.onerror=null; this.src='https://via.placeholder.com/400x300?text=Gambar+Tidak+Ditemukan';" 
                                         class="max-h-full max-w-full object-contain rounded-xl z-10 transition-transform duration-500 group-hover/preview:scale-105">
                                    
                                    {{-- Overlay Hover Preview --}}
                                    <div id="preview-overlay" class="absolute inset-0 bg-black/50 opacity-0 group-hover/preview:opacity-100 transition-opacity duration-300 z-10 rounded-xl flex items-center justify-center backdrop-blur-[2px]">
                                        <span id="preview-badge" class="text-white text-[10px] font-black uppercase tracking-widest border-2 border-white/30 bg-black/40 px-5 py-2.5 rounded-full backdrop-blur-md">
                                            <i class="fas fa-eye mr-1"></i> Tinjauan Gambar
                                        </span>
                                    </div>
                                </div>
                            @else
                                {{-- Jika Belum Ada Gambar Sama Sekali --}}
                                <div id="preview-container" class="w-full h-full relative group/preview flex items-center justify-center">
                                    <img id="asset-preview" src="#" class="hidden max-h-full max-w-full object-contain rounded-xl z-10 transition-transform duration-500 group-hover/preview:scale-105">
                                    
                                    <div id="preview-overlay" class="hidden absolute inset-0 bg-black/50 opacity-0 group-hover/preview:opacity-100 transition-opacity duration-300 z-10 rounded-xl flex items-center justify-center backdrop-blur-[2px]">
                                        <span id="preview-badge" class="text-white text-[10px] font-black uppercase tracking-widest border-2 border-white/30 bg-black/40 px-5 py-2.5 rounded-full backdrop-blur-md">
                                            <i class="fas fa-magic mr-1"></i> Preview Baru
                                        </span>
                                    </div>

                                    <div id="no-photo-text" class="text-center z-0 flex flex-col items-center">
                                        <div class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center mb-3 shadow-inner">
                                            <i class="fas fa-camera-slash text-xl text-slate-500"></i>
                                        </div>
                                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Belum Ada Visual</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- SUBMIT --}}
        <div class="mt-10 flex justify-end">
            <button type="submit" class="w-full md:w-auto px-12 py-5 bg-indigo-600 text-white rounded-[1.5rem] font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition shadow-xl shadow-indigo-200 transform active:scale-[0.98] flex items-center justify-center gap-3">
                <i class="fas fa-save text-sm"></i> Simpan Perubahan
            </button>
        </div>

    </form>
</div>

{{-- SCRIPT DENGAN GLOBAL WINDOW SCOPE (ANTI-GAGAL) --}}
<!-- Script Library jQuery dan Select2 -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Inisialisasi Select2 untuk semua Dropdown pada halaman Edit
    $(document).ready(function() {
        $('#selectCategory, #selectCondition, #selectStatus').select2({
            width: '100%',
            minimumResultsForSearch: 10 // Search bar akan muncul jika item lebih dari 10 (bisa diubah sesuai kebutuhan)
        });
    });

    // Fungsi Preview Gambar
    window.previewImage = function(input) {
        // Validasi Ukuran Maksimal 100MB
        const maxSize = 104857600; 
        
        if (input.files && input.files[0]) {
            if (input.files[0].size > maxSize) {
                alert('Peringatan: Ukuran file gambar terlalu besar!\nMaksimal ukuran yang diizinkan adalah 100 MB.');
                input.value = ''; 
                return;
            }

            const preview = document.getElementById('asset-preview');
            const noPhotoText = document.getElementById('no-photo-text');
            const overlay = document.getElementById('preview-overlay');
            const badge = document.getElementById('preview-badge');
            
            const reader = new FileReader();
            reader.onload = function(e) {
                if(preview) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                
                if (overlay) overlay.classList.remove('hidden');
                if (noPhotoText) noPhotoText.classList.add('hidden');
                
                // Ubah teks overlay jika menimpa gambar lama
                if (badge) {
                    badge.innerHTML = '<i class="fas fa-magic mr-1"></i> Preview Baru';
                    badge.classList.replace('border-white/30', 'border-indigo-400/50');
                    badge.classList.add('text-indigo-100');
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    };
</script>
@endsection