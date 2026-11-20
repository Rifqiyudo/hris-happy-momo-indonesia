@extends('admin.layouts.master')
@section('title', 'Data Departemen - PT. Happy Momo Indonesia')

@section('content')
<div class="max-w-7xl mx-auto px-4 space-y-8 pb-20 animate-fadeIn">
    
    {{-- [BARU] BREADCRUMB NAVIGASI --}}
    <nav class="flex px-1" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
            <li><a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Home</a></li>
            <li><i class="fas fa-chevron-right text-[8px] opacity-50"></i></li>
            <li><span>Master Data</span></li>
            <li><i class="fas fa-chevron-right text-[8px] opacity-50"></i></li>
            <li class="text-slate-600 dark:text-slate-300">Data Departemen</li>
        </ol>
    </nav>

    {{-- BREADCRUMB / HEADER OPTIONAL --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <div class="w-10 h-10 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-md shadow-indigo-200 dark:shadow-indigo-950/60">
                    <i class="fas fa-sitemap text-sm"></i>
                </div>
                <h1 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">Manajemen Departemen</h1>
            </div>
            <p class="text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider ml-13">Kelola divisi, unit kerja, dan struktur organisasi perusahaan secara terpusat.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- KOLOM KIRI: DAFTAR DEPARTEMEN --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Header & Search --}}
            <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl shadow-sm dark:shadow-none border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div>
                    <h2 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Daftar Divisi / Departemen</h2>
                    <p class="text-[10px] text-slate-400 dark:text-slate-400 font-black uppercase tracking-widest mt-0.5">Total: <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ $departments->total() }}</span> Unit Kerja</p>
                </div>
                
                <form action="{{ route('admin.master.department.index') }}" method="GET" class="w-full sm:w-auto relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        class="w-full sm:w-64 pl-10 {{ request('search') ? 'pr-9' : 'pr-4' }} py-3 border border-slate-200 dark:border-slate-800 hover:border-indigo-400 dark:hover:border-indigo-500/60 rounded-2xl text-xs font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500 bg-slate-50/50 dark:bg-slate-950 transition-all outline-none placeholder-slate-400 dark:placeholder-slate-500 shadow-sm dark:shadow-none" 
                        placeholder="Cari Departemen...">
                    <i class="fas fa-search absolute left-3.5 top-3.5 text-slate-400 dark:text-slate-500 text-xs"></i>

                    {{-- [BARU] Tombol Hapus Filter Pencarian Cepat --}}
                    @if(request('search'))
                        <a href="{{ route('admin.master.department.index') }}"
                            class="absolute right-2.5 top-2.5 w-6 h-6 rounded-lg bg-slate-200/70 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-rose-500 dark:hover:text-rose-400 flex items-center justify-center transition-colors"
                            title="Reset Pencarian">
                            <i class="fas fa-times text-[10px]"></i>
                        </a>
                    @endif
                </form>
            </div>

            {{-- Tabel Data --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm dark:shadow-none border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="overflow-x-auto dept-table-scroll">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300 border-collapse">
                        <thead class="bg-slate-50/80 dark:bg-slate-950/80 text-slate-400 dark:text-slate-400 font-black uppercase text-[10px] tracking-[0.2em] border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="px-6 py-5">Nama Departemen</th>
                                <th class="px-6 py-5 text-center">Jumlah Personil</th>
                                <th class="px-6 py-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            @forelse($departments as $dept)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/45 transition-colors duration-200 group">
                                
                                {{-- Nama Departemen --}}
                                <td class="px-6 py-5 align-middle">
                                    <div class="flex items-center gap-4">
                                        <div class="w-11 h-11 rounded-2xl bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 shadow-sm dark:shadow-none font-black text-xs border border-indigo-100 dark:border-indigo-500/30 group-hover:bg-indigo-600 dark:group-hover:bg-indigo-600 group-hover:text-white dark:group-hover:text-white group-hover:border-indigo-600 dark:group-hover:border-indigo-500 transition-all">
                                            {{ strtoupper(substr($dept->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-800 dark:text-white text-xs uppercase tracking-tight group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">{{ $dept->name }}</p>
                                            @if($dept->description)
                                                <p class="text-[11px] text-slate-400 dark:text-slate-400 font-medium mt-0.5 line-clamp-1">{{ $dept->description }}</p>
                                            @else
                                                <p class="text-[10px] text-slate-300 dark:text-slate-600 italic font-medium mt-0.5">Tidak ada deskripsi</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Jumlah Personil --}}
                                <td class="px-6 py-5 align-middle text-center">
                                    @if(isset($dept->users_count) && $dept->users_count > 0)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/30">
                                            <i class="fas fa-users text-[9px]"></i> {{ $dept->users_count }} Orang
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-800/90 text-slate-400 dark:text-slate-400 border border-slate-200 dark:border-slate-700/80">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>
                                            Kosong
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-5 align-middle text-center">
                                    <div class="flex justify-center items-center gap-2">
                                        <button type="button" 
                                            onclick="editDepartment(this)"
                                            data-id="{{ $dept->id }}"
                                            data-name="{{ $dept->name }}"
                                            data-desc="{{ $dept->description ?? '' }}"
                                            class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-300 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-500/15 hover:border-amber-200 dark:hover:border-amber-500/40 transition-all flex items-center justify-center shadow-sm dark:shadow-none cursor-pointer" 
                                            title="Edit Departemen">
                                            <i class="fas fa-pencil-alt text-xs"></i>
                                        </button>
                                        
                                        <button type="button" onclick="confirmDelete('{{ $dept->id }}', '{{ $dept->name }}')" 
                                            class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-300 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/15 hover:border-rose-200 dark:hover:border-rose-500/40 transition-all flex items-center justify-center shadow-sm dark:shadow-none cursor-pointer" 
                                            title="Hapus Departemen">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>

                                        <form id="delete-form-{{ $dept->id }}" action="{{ route('admin.master.department.destroy', $dept->id) }}" method="POST" class="hidden">
                                            @csrf @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-16 text-center text-slate-400 dark:text-slate-500">
                                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800/70 text-slate-300 dark:text-slate-600 rounded-3xl flex items-center justify-center text-2xl mx-auto mb-3 border-2 border-dashed border-slate-200 dark:border-slate-700">
                                        <i class="fas fa-folder-open"></i>
                                    </div>
                                    <p class="font-black text-slate-700 dark:text-slate-200 uppercase tracking-tight text-sm">Belum Ada Data Departemen</p>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Silakan tambahkan unit kerja atau divisi baru melalui form di samping.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                @if($departments->hasPages())
                    <div class="px-6 py-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/60 dept-pagination-wrapper">
                        {{ $departments->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- KOLOM KANAN: FORM INPUT (STICKY) --}}
        <div class="lg:col-span-1">
            <div id="departmentFormCard" class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm dark:shadow-none border border-slate-200 dark:border-slate-800 sticky top-6 overflow-hidden transition-all duration-300">
                
                <div class="bg-slate-900 dark:bg-slate-950/90 px-6 py-5 border-b border-slate-800 flex items-center justify-between gap-2">
                    <h3 class="font-black text-white uppercase tracking-wider text-xs flex items-center gap-2.5" id="formTitle">
                        <i class="fas fa-plus-circle text-emerald-400"></i> Tambah Departemen
                    </h3>
                    {{-- [BARU] Badge Indikator Mode Form --}}
                    <span id="formModeBadge" class="px-2.5 py-1 rounded-lg bg-emerald-500/15 border border-emerald-500/30 text-[9px] font-black uppercase tracking-widest text-emerald-400">
                        Baru
                    </span>
                </div>

                <div class="p-6">
                    <form id="departmentForm" action="{{ route('admin.master.department.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <div id="methodField"></div>
                        
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest mb-2">Nama Departemen <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" id="nameInput" required 
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-indigo-400 dark:hover:border-indigo-500/60 rounded-xl px-4 py-3 text-xs font-bold text-slate-700 dark:text-slate-100 outline-none focus:ring-2 focus:ring-indigo-500 transition-all placeholder-slate-400 dark:placeholder-slate-500" 
                                placeholder="Contoh: Information Technology (IT)">
                            @error('name') <span class="text-[10px] text-rose-500 dark:text-rose-400 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest">Deskripsi Singkat</label>
                                {{-- [BARU] Penghitung Karakter Deskripsi --}}
                                <span id="descCharCount" class="text-[9px] font-bold text-slate-400 dark:text-slate-500">0 karakter</span>
                            </div>
                            <textarea name="description" id="descInput" rows="4" 
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-indigo-400 dark:hover:border-indigo-500/60 rounded-xl px-4 py-3 text-xs font-bold text-slate-700 dark:text-slate-100 outline-none focus:ring-2 focus:ring-indigo-500 transition-all resize-none placeholder-slate-400 dark:placeholder-slate-500" 
                                placeholder="Jelaskan fungsi utama departemen ini..."></textarea>
                        </div>

                        {{-- Info Box --}}
                        <div class="bg-indigo-50/60 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/25 rounded-2xl p-4 flex gap-3">
                            <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-[10px] shrink-0 mt-0.5">
                                <i class="fas fa-info"></i>
                            </div>
                            <div class="text-[11px] text-indigo-900 dark:text-slate-300 leading-relaxed font-medium">
                                <strong class="font-black uppercase tracking-wider block text-[10px] text-indigo-700 dark:text-indigo-400 mb-0.5">Tips HRIS:</strong> 
                                Gunakan nama standar agar pengelompokan laporan gaji & absensi karyawan lebih akurat.
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-3 pt-2">
                            <button type="button" id="btnCancel" onclick="resetForm()" 
                                class="hidden flex-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-transparent dark:border-slate-700 py-3.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 dark:hover:bg-slate-700 transition-all cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" id="btnSubmit" 
                                class="flex-1 bg-indigo-600 text-white py-3.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-indigo-200 dark:shadow-indigo-950/60 hover:bg-indigo-700 transition-all active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fas fa-save"></i> <span>Simpan Data</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- [BARU] Dukungan Dark Mode untuk Pagination, Scrollbar, dan SweetAlert2 --}}
<style>
    .dept-table-scroll::-webkit-scrollbar {
        height: 8px;
    }
    .dept-table-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .dept-table-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 9999px;
    }
    html.dark .dept-table-scroll::-webkit-scrollbar-thumb,
    body.dark .dept-table-scroll::-webkit-scrollbar-thumb,
    .dark .dept-table-scroll::-webkit-scrollbar-thumb {
        background: #334155;
    }

    /* Mencegah tombol pagination Laravel bocor putih di Dark Mode */
    html.dark .dept-pagination-wrapper nav[role="navigation"] a,
    body.dark .dept-pagination-wrapper nav[role="navigation"] a,
    .dark .dept-pagination-wrapper nav[role="navigation"] a,
    html.dark .dept-pagination-wrapper nav[role="navigation"] span[aria-disabled="true"] > span,
    body.dark .dept-pagination-wrapper nav[role="navigation"] span[aria-disabled="true"] > span,
    .dark .dept-pagination-wrapper nav[role="navigation"] span[aria-disabled="true"] > span {
        background-color: #0f172a !important;
        border-color: #1e293b !important;
        color: #cbd5e1 !important;
    }
    html.dark .dept-pagination-wrapper nav[role="navigation"] a:hover,
    body.dark .dept-pagination-wrapper nav[role="navigation"] a:hover,
    .dark .dept-pagination-wrapper nav[role="navigation"] a:hover {
        background-color: #1e293b !important;
        color: #ffffff !important;
    }
    html.dark .dept-pagination-wrapper nav[role="navigation"] span[aria-current="page"] > span,
    body.dark .dept-pagination-wrapper nav[role="navigation"] span[aria-current="page"] > span,
    .dark .dept-pagination-wrapper nav[role="navigation"] span[aria-current="page"] > span {
        background-color: #4f46e5 !important;
        border-color: #4f46e5 !important;
        color: #ffffff !important;
    }

    /* SweetAlert2 Dark Mode Support */
    html.dark .swal2-popup,
    body.dark .swal2-popup,
    .dark .swal2-popup {
        background: #0f172a !important;
        color: #f1f5f9 !important;
        border: 1px solid #1e293b !important;
    }
    html.dark .swal2-title,
    body.dark .swal2-title,
    .dark .swal2-title {
        color: #ffffff !important;
    }
    html.dark .swal2-html-container,
    body.dark .swal2-html-container,
    .dark .swal2-html-container {
        color: #94a3b8 !important;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // [BARU] Helper Penghitung Karakter Deskripsi
    function updateDescCounter() {
        const descInput = document.getElementById('descInput');
        const counter = document.getElementById('descCharCount');
        if (descInput && counter) {
            counter.innerText = `${descInput.value.length} karakter`;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const descInput = document.getElementById('descInput');
        if (descInput) {
            descInput.addEventListener('input', updateDescCounter);
        }
    });

    // --- 1. EDIT MODE ---
    function editDepartment(btn) {
        const id = btn.dataset.id;
        const name = btn.dataset.name;
        const desc = btn.dataset.desc;

        // Ubah Judul & Action Form
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-pencil-alt text-amber-400"></i> Edit Departemen';
        let baseUrl = "{{ route('admin.master.department.index') }}";
        document.getElementById('departmentForm').action = baseUrl + '/' + id;
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        // [BARU] Perbarui Badge & Highlight Kartu Form agar Mode Edit Terlihat Jelas
        const badge = document.getElementById('formModeBadge');
        if (badge) {
            badge.innerText = 'Edit';
            badge.className = 'px-2.5 py-1 rounded-lg bg-amber-500/15 border border-amber-500/30 text-[9px] font-black uppercase tracking-widest text-amber-400';
        }
        const formCard = document.getElementById('departmentFormCard');
        if (formCard) {
            formCard.classList.add('ring-2', 'ring-amber-500/40');
        }

        // Isi Data ke Input
        document.getElementById('nameInput').value = name;
        document.getElementById('descInput').value = desc;
        updateDescCounter();

        // Sesuaikan Styling Tombol Submit menjadi Amber/Update
        let btnSubmit = document.getElementById('btnSubmit');
        btnSubmit.classList.replace('bg-indigo-600', 'bg-amber-500');
        btnSubmit.classList.replace('hover:bg-indigo-700', 'hover:bg-amber-600');
        btnSubmit.classList.replace('shadow-indigo-200', 'shadow-amber-200');
        btnSubmit.classList.replace('dark:shadow-indigo-950/60', 'dark:shadow-amber-950/60');
        btnSubmit.querySelector('span').innerText = 'Update Data';
        
        // Tampilkan Tombol Batal
        document.getElementById('btnCancel').classList.remove('hidden');

        // Scroll halus ke atas form & focus input
        window.scrollTo({ top: 0, behavior: 'smooth' });
        document.getElementById('nameInput').focus();
    }

    // --- 2. RESET MODE ---
    function resetForm() {
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus-circle text-emerald-400"></i> Tambah Departemen';
        document.getElementById('departmentForm').reset();
        document.getElementById('departmentForm').action = "{{ route('admin.master.department.store') }}";
        document.getElementById('methodField').innerHTML = '';
        updateDescCounter();

        // [BARU] Kembalikan Badge & Ring Kartu Form ke Mode Baru
        const badge = document.getElementById('formModeBadge');
        if (badge) {
            badge.innerText = 'Baru';
            badge.className = 'px-2.5 py-1 rounded-lg bg-emerald-500/15 border border-emerald-500/30 text-[9px] font-black uppercase tracking-widest text-emerald-400';
        }
        const formCard = document.getElementById('departmentFormCard');
        if (formCard) {
            formCard.classList.remove('ring-2', 'ring-amber-500/40');
        }

        // Kembalikan Tombol ke Mode Simpan Utama
        let btnSubmit = document.getElementById('btnSubmit');
        btnSubmit.classList.replace('bg-amber-500', 'bg-indigo-600');
        btnSubmit.classList.replace('hover:bg-amber-600', 'hover:bg-indigo-700');
        btnSubmit.classList.replace('shadow-amber-200', 'shadow-indigo-200');
        btnSubmit.classList.replace('dark:shadow-amber-950/60', 'dark:shadow-indigo-950/60');
        btnSubmit.querySelector('span').innerText = 'Simpan Data';

        document.getElementById('btnCancel').classList.add('hidden');
    }

    // --- 3. DELETE ALERT (SweetAlert2) ---
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Departemen?',
            text: "Anda akan menghapus unit kerja: " + name + ". Pastikan tidak ada karyawan aktif di dalamnya.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-3xl',
                confirmButton: 'rounded-xl text-xs font-black uppercase tracking-wider px-5 py-3',
                cancelButton: 'rounded-xl text-xs font-black uppercase tracking-wider px-5 py-3'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endsection