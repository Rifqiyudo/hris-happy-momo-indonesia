@extends('admin.layouts.master')
@section('title', 'Tambah Aset')

@section('content')

{{-- ========================================================================= --}}
{{-- PENGATURAN TAMBAHAN UNTUK SELECT2 (Fitur Search Dropdown)                 --}}
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
        background-color: #ffffff !important;
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

{{-- ========================================================================= --}}
{{-- BYPASS LOGIC: Memaksa baca data Cabang dan Avatar secara aman dan valid --}}
{{-- ========================================================================= --}}
@php
    $empData = [];
    if(isset($employees) && $employees->count() > 0) {
        foreach($employees as $emp) {
            $branchName = 'Pusat / Tidak Terdaftar';
            if ($emp->branch_id) {
                $branchDb = \Illuminate\Support\Facades\DB::table('branches')->where('id', $emp->branch_id)->first();
                if ($branchDb) {
                    $branchName = $branchDb->name ?? $branchDb->nama_cabang ?? $branchDb->nama ?? $branchDb->company_name ?? 'Pusat';
                }
            }
            $avatar = $emp->avatar ? asset('storage/'.$emp->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($emp->name).'&background=4f46e5&color=fff';
            
            $empData[] = (object)[
                'id' => $emp->id,
                'name' => $emp->name,
                'nik' => $emp->nik,
                'position' => $emp->position->name ?? 'Staff',
                'branch' => $branchName,
                'avatar' => $avatar
            ];
        }
    }
@endphp

<div class="max-w-5xl mx-auto bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-xl animate-fadeIn">
    
    <div class="flex items-center gap-4 mb-8 border-b border-slate-100 pb-6">
        <a href="{{ route('admin.transaksi.asset.index') }}" class="w-10 h-10 flex items-center justify-center rounded-2xl bg-slate-50 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tighter">Tambah Aset Baru</h2>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Alokasi Perangkat Multi-Item untuk Karyawan</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-8 p-5 bg-rose-50 border border-rose-100 rounded-2xl">
            <ul class="list-disc list-inside text-rose-600 text-[10px] font-bold uppercase tracking-widest space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.transaksi.asset.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        {{-- ========================================================= --}}
        {{-- 1. PEMILIHAN KARYAWAN (INDUK DARI SEMUA ASET) --}}
        {{-- ========================================================= --}}
        <div class="mb-10 p-6 bg-indigo-50/50 border border-indigo-100 rounded-[2rem]">
            <label class="block text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-3 ml-1">Alokasi Pemegang (Karyawan)</label>
            
            <div class="flex flex-col md:flex-row gap-6 items-start">
                <div class="w-full md:w-2/3">
                    <select name="user_id" id="employeeSelect" class="w-full">
                        <option value="" data-avatar="" data-branch="" data-position="" data-name="">-- Aset Tersedia (Disimpan Di Gudang) --</option>
                        @if(count($empData) > 0)
                            @foreach($empData as $emp)
                                <option value="{{ $emp->id }}" 
                                        data-avatar="{{ $emp->avatar }}" 
                                        data-branch="{{ $emp->branch }}" 
                                        data-position="{{ $emp->position }}" 
                                        data-name="{{ $emp->name }}"
                                        {{ old('user_id') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->nik }} — {{ $emp->name }} | Cabang: {{ $emp->branch }}
                                </option>
                            @endforeach
                        @else
                            <option value="" disabled>Data karyawan tidak tersedia</option>
                        @endif
                    </select>
                    <p class="text-[9px] text-slate-400 mt-2 ml-1 uppercase font-bold italic">Kosongkan pilihan ini jika aset berstatus barang persediaan kantor.</p>
                </div>

                <div class="w-full md:w-1/3 bg-white border border-indigo-100 rounded-2xl p-4 flex items-center gap-4 shadow-sm hidden transition-all duration-300 animate-fadeIn" id="employeePreviewCard">
                    <img src="" id="empPreviewAvatar" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shadow-sm flex-shrink-0">
                    <div class="flex-1 overflow-hidden">
                        <h4 id="empPreviewName" class="text-xs font-black text-slate-800 uppercase tracking-tight truncate">Nama Karyawan</h4>
                        <p id="empPreviewPosition" class="text-[9px] font-bold text-indigo-500 uppercase tracking-widest mt-0.5 truncate">Jabatan</p>
                        <p id="empPreviewBranch" class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5 truncate"><i class="fas fa-map-marker-alt text-rose-400 mr-1"></i> Cabang</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- 2. WRAPPER UNTUK MULTI-ASET (BLOK YANG AKAN DIGANDAKAN) --}}
        {{-- ========================================================= --}}
        <div id="dynamic-items-container" class="space-y-8">
            
            {{-- BLOK ASET PERTAMA --}}
            <div class="asset-block bg-white border-2 border-slate-100 rounded-[2rem] p-8 shadow-sm relative transition-all duration-300">
                
                {{-- TOMBOL HAPUS TONG SAMPAH (Tersembunyi di blok pertama) --}}
                <button type="button" onclick="hapusBlokAset(this)" class="btn-remove-item hidden absolute top-6 right-6 w-9 h-9 flex items-center justify-center bg-rose-50 text-rose-500 border border-rose-100 rounded-xl hover:bg-rose-500 hover:text-white transition shadow-sm cursor-pointer" title="Hapus Aset Ini">
                    <i class="fas fa-trash-alt"></i>
                </button>

                <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-50">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-box-open text-indigo-400 text-lg"></i> <span>Data Spesifikasi Aset</span>
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    {{-- Baris 1 --}}
                    <div>
                        <div class="flex justify-between items-end mb-2 ml-1">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Aset <span class="text-rose-500">*</span></label>
                            
                            {{-- TOMBOL TAMBAH (Menggunakan onclick langsung) --}}
                            <button type="button" onclick="tambahBlokAset()" class="btn-add-item bg-indigo-50 text-indigo-600 border border-indigo-100 px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-indigo-600 hover:text-white transition flex items-center gap-1 shadow-sm active:scale-95 cursor-pointer">
                                <i class="fas fa-plus"></i> Tambah
                            </button>
                            
                        </div>
                        <input type="text" name="name[]" placeholder="Contoh: Laptop ASUS ROG" required 
                               class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-3.5 font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Merk / Brand</label>
                        <input type="text" name="asset_brand[]" placeholder="Contoh: Apple, Honda" 
                               class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-3.5 font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Serial Number / Plat <span class="text-rose-500">*</span></label>
                        <input type="text" name="serial_number[]" placeholder="SN-123456789" required 
                               class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-3.5 font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition focus:bg-white">
                    </div>

                    {{-- Baris 2 --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Kategori <span class="text-rose-500">*</span></label>
                        <select name="category[]" required class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-3.5 font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition cursor-pointer focus:bg-white">
                            <option value="electronics">Electronics (Laptop, HP)</option>
                            <option value="furniture">Furniture (Meja, Kursi)</option>
                            <option value="vehicle">Vehicle (Motor, Mobil)</option>
                            <option value="others">Others (Lain-lain)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Harga Beli (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="purchase_price[]" placeholder="0" required 
                               class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-3.5 font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Tanggal Beli</label>
                        <input type="date" name="purchase_date[]" value="{{ date('Y-m-d') }}" 
                               class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-3.5 font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition focus:bg-white">
                    </div>

                    {{-- Baris 3 --}}
                    <div>
                        <label class="block text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-2 ml-1">Lokasi Fisik Aset</label>
                        <input type="text" name="location[]" placeholder="Gudang / Kantor Pusat" 
                               class="w-full bg-indigo-50/50 border border-indigo-100 rounded-2xl p-3.5 font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-2 ml-1">Kondisi Barang <span class="text-rose-500">*</span></label>
                        <select name="condition[]" required class="w-full bg-indigo-50/50 border border-indigo-100 rounded-2xl p-3.5 font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition cursor-pointer focus:bg-white">
                            <option value="new">Baru (New)</option>
                            <option value="good" selected>Layak Pakai (Good)</option>
                            <option value="broken">Rusak (Broken)</option>
                        </select>
                    </div>
                    <div class="row-span-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Catatan Tambahan</label>
                        <textarea name="description[]" rows="4" placeholder="Contoh: Lengkap dengan charger, dus, dan tas..."
                                  class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition focus:bg-white resize-none"></textarea>
                    </div>

                    {{-- Baris 4: Upload Foto --}}
                    <div class="col-span-1 md:col-span-2 mt-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Foto / Dokumentasi Perangkat</label>
                        <div class="flex flex-col md:flex-row gap-6 h-48">
                            <div class="relative w-full md:w-1/2 group h-full">
                                <input type="file" name="image[]" accept="image/jpeg,image/png,image/svg+xml" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" onchange="previewImageBaru(this)">
                                <div class="w-full h-full bg-slate-50 border-2 border-dashed border-slate-300 rounded-[2rem] flex flex-col items-center justify-center text-slate-400 group-hover:border-indigo-500 group-hover:bg-indigo-50/50 transition-all duration-300 relative overflow-hidden shadow-sm">
                                    <i class="fas fa-cloud-upload-alt text-4xl mb-3 text-slate-300 group-hover:text-indigo-500 group-hover:scale-110 group-hover:-translate-y-1 transition-all"></i>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-600 group-hover:text-indigo-700">Pilih / Seret Gambar</span>
                                </div>
                            </div>
                            <div class="w-full md:w-1/2 h-full flex justify-center items-center bg-slate-900 border-4 border-slate-100 rounded-[2rem] p-3 relative overflow-hidden shadow-inner">
                                <div class="preview-container w-full h-full relative group/preview flex items-center justify-center">
                                    <img src="#" class="asset-preview hidden max-h-full max-w-full object-contain rounded-xl z-10 transition-transform duration-500 group-hover/preview:scale-105">
                                    <div class="no-photo-text text-center z-0 flex flex-col items-center">
                                        <div class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center mb-3 shadow-inner">
                                            <i class="fas fa-camera-slash text-xl text-slate-500"></i>
                                        </div>
                                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Belum Ada Visual</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- AKHIR BLOK ASET --}}
        </div>

        <div class="mt-10 pt-6 flex justify-end border-t border-slate-100">
            <button type="submit" class="w-full md:w-auto px-12 py-5 bg-slate-900 text-white rounded-[1.5rem] font-black text-xs uppercase tracking-widest hover:bg-black transition shadow-xl shadow-slate-200 transform active:scale-[0.98] flex items-center justify-center gap-3">
                <i class="fas fa-save text-sm"></i> Simpan Seluruh Aset
            </button>
        </div>

    </form>
</div>

{{-- SCRIPT JAVASCRIPT DIRECT (ANTI-GAGAL) --}}
<!-- Script Library jQuery dan Select2 -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // INISIALISASI SELECT2 KETIKA HALAMAN DIMUAT
    $(document).ready(function() {
        $('#employeeSelect').select2({
            placeholder: "-- Aset Tersedia (Disimpan Di Gudang) --",
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() {
                    return "Karyawan tidak ditemukan";
                }
            }
        });

        // Trigger fungsi preview ketika opsi karyawan dipilih/dihapus via Select2
        $('#employeeSelect').on('change', function() {
            updateEmployeePreview(this);
        });
        
        // Panggil saat load jika ada old value (sudah ada karyawan yang terpilih)
        if ($('#employeeSelect').val() !== "") {
            updateEmployeePreview(document.getElementById('employeeSelect'));
        }
    });

    // FUNGSI 1: MENGGANDAKAN BLOK FORMULIR ASET (DIJALANKAN OLEH TOMBOL + TAMBAH)
    function tambahBlokAset() {
        const container = document.getElementById("dynamic-items-container");
        
        // Ambil blok pertama sebagai template utama
        const firstBlock = container.querySelector(".asset-block");
        if (!firstBlock) return;
        
        // Gandakan (Clone) seluruh isi blok tersebut
        const newBlock = firstBlock.cloneNode(true);

        // Bersihkan seluruh data input teks/angka di blok hasil gandakan (Kecuali Tanggal)
        const inputs = newBlock.querySelectorAll("input");
        for (let i = 0; i < inputs.length; i++) {
            if (inputs[i].type !== 'hidden' && inputs[i].type !== 'date') {
                inputs[i].value = "";
            }
        }

        // Bersihkan data textarea (catatan)
        const textareas = newBlock.querySelectorAll("textarea");
        for (let i = 0; i < textareas.length; i++) {
            textareas[i].value = "";
        }
        
        // Kembalikan Dropdown Select ke pilihan awal
        const selects = newBlock.querySelectorAll("select");
        for (let i = 0; i < selects.length; i++) {
            selects[i].selectedIndex = 0;
        }

        // Sembunyikan dan hapus gambar preview di blok yang baru digandakan
        const preview = newBlock.querySelector('.asset-preview');
        if (preview) {
            preview.src = "#";
            preview.classList.add('hidden');
        }
        const noPhotoText = newBlock.querySelector('.no-photo-text');
        if (noPhotoText) {
            noPhotoText.classList.remove('hidden');
        }

        // Sembunyikan tombol [+] Tambah di blok baru, tampilkan tombol Tong Sampah (Hapus)
        const btnAdd = newBlock.querySelector(".btn-add-item");
        if (btnAdd) btnAdd.classList.add("hidden");
        
        const btnRemove = newBlock.querySelector(".btn-remove-item");
        if (btnRemove) btnRemove.classList.remove("hidden");

        // Masukkan blok baru ke dalam halaman
        container.appendChild(newBlock);
    }

    // FUNGSI 2: MENGHAPUS BLOK FORMULIR (DIJALANKAN OLEH TOMBOL TONG SAMPAH)
    function hapusBlokAset(btnElement) {
        // Cari blok aset yang membungkus tombol hapus ini
        const block = btnElement.closest(".asset-block");
        if (block) {
            block.remove(); // Hapus dari HTML
        }
    }

    // FUNGSI 3: PREVIEW KARTU KARYAWAN (DIJALANKAN SAAT DROPDOWN DIUBAH)
    function updateEmployeePreview(selectElement) {
        const empCard = document.getElementById("employeePreviewCard");
        const selected = selectElement.options[selectElement.selectedIndex];
        
        if (!selected || selectElement.value === "") {
            empCard.classList.add('hidden');
        } else {
            empCard.classList.remove('hidden');
            document.getElementById("empPreviewAvatar").src = selected.getAttribute('data-avatar');
            document.getElementById("empPreviewName").textContent = selected.getAttribute('data-name');
            document.getElementById("empPreviewPosition").textContent = selected.getAttribute('data-position');
            document.getElementById("empPreviewBranch").innerHTML = '<i class="fas fa-map-marker-alt text-rose-400 mr-1"></i> ' + selected.getAttribute('data-branch');
        }
    }

    // FUNGSI 4: PREVIEW GAMBAR ASET
    function previewImageBaru(inputElement) {
        const maxSize = 104857600; // Maksimal 100MB
        if (inputElement.files && inputElement.files[0]) {
            if (inputElement.files[0].size > maxSize) {
                alert('Peringatan: Ukuran gambar terlalu besar!\nMaksimal 100 MB.');
                inputElement.value = ''; 
                return;
            }
            // Cari elemen img dan text di dalam satu kotak yang sama dengan file input yang diklik
            const container = inputElement.closest('.asset-block');
            const preview = container.querySelector('.asset-preview');
            const noPhotoText = container.querySelector('.no-photo-text');
            
            const reader = new FileReader();
            reader.onload = function(e) {
                if(preview) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                if(noPhotoText) noPhotoText.classList.add('hidden');
            }
            reader.readAsDataURL(inputElement.files[0]);
        }
    }
</script>
@endsection