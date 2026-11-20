@extends('admin.layouts.master')
@section('title', 'Manajemen Pengumuman')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-8 animate-fadeIn">
    
    {{-- [BARU] Breadcrumb Navigasi --}}
    <nav class="flex px-1" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
            <li><a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Home</a></li>
            <li><i class="fas fa-chevron-right text-[8px] opacity-50"></i></li>
            <li><span>Master Data</span></li>
            <li><i class="fas fa-chevron-right text-[8px] opacity-50"></i></li>
            <li class="text-slate-600 dark:text-slate-300">Manajemen Pengumuman</li>
        </ol>
    </nav>

    {{-- 1. Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] border border-gray-100 dark:border-slate-800 shadow-sm shadow-indigo-100/20 dark:shadow-none">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-200 dark:shadow-indigo-950/60">
                    <i class="fas fa-bullhorn text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">Board Pengumuman</h2>
                    <p class="text-slate-400 dark:text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mt-0.5">Informasi & Notifikasi Perusahaan</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.master.announcement.create') }}" class="group bg-indigo-600 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-500 shadow-xl shadow-indigo-100 dark:shadow-indigo-950/60 transition-all active:scale-95 flex items-center gap-2">
                <i class="fas fa-plus group-hover:rotate-90 transition-transform"></i> 
                Buat Konten Baru
            </a>
        </div>
    </div>

    {{-- 2. Stats Section --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-gray-50 dark:border-slate-800 shadow-sm dark:shadow-none flex items-center gap-5">
            <div class="w-14 h-14 bg-blue-50 dark:bg-blue-500/15 border border-transparent dark:border-blue-500/25 text-blue-600 dark:text-blue-400 rounded-2xl flex items-center justify-center text-xl shadow-inner dark:shadow-none">
                <i class="fas fa-paper-plane"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest">Total Diterbitkan</p>
                <h4 class="text-xl font-black text-slate-800 dark:text-white">{{ $announcements->total() }}</h4>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-gray-50 dark:border-slate-800 shadow-sm dark:shadow-none flex items-center gap-5">
            <div class="w-14 h-14 bg-emerald-50 dark:bg-emerald-500/15 border border-transparent dark:border-emerald-500/25 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center text-xl shadow-inner dark:shadow-none">
                <i class="fas fa-check-double"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest">Status Aktif</p>
                <h4 class="text-xl font-black text-slate-800 dark:text-white">{{ $announcements->where('is_active', 1)->count() }}</h4>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-gray-50 dark:border-slate-800 shadow-sm dark:shadow-none flex items-center gap-5">
            <div class="w-14 h-14 bg-rose-50 dark:bg-rose-500/15 border border-transparent dark:border-rose-500/25 text-rose-600 dark:text-rose-400 rounded-2xl flex items-center justify-center text-xl shadow-inner dark:shadow-none">
                <i class="fas fa-bolt"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest">Kategori Urgent</p>
                <h4 class="text-xl font-black text-slate-800 dark:text-white">{{ $announcements->where('type', 'danger')->count() }}</h4>
            </div>
        </div>
    </div>

    {{-- 3. Table/List Section --}}
    <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-gray-100 dark:border-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-none overflow-hidden">
        
        {{-- [BARU] Toolbar Pencarian Cepat & Filter Status/Urgensi --}}
        @if($announcements->count() > 0)
        <div class="px-8 py-5 border-b border-gray-100 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-950/60 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="relative w-full lg:w-80">
                <i class="fas fa-search absolute left-4 top-3.5 text-slate-400 dark:text-slate-500 text-xs"></i>
                <input type="text"
                    id="announcementSearchInput"
                    placeholder="Cari judul, isi, atau divisi..."
                    autocomplete="off"
                    class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-indigo-400 dark:hover:border-indigo-500/60 rounded-2xl text-xs font-bold text-slate-700 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
            </div>

            <div class="flex flex-wrap items-center gap-1.5 bg-slate-100 dark:bg-slate-950 p-1.5 rounded-2xl border border-slate-200/60 dark:border-slate-800" id="announcementFilterGroup">
                <button type="button" data-filter="all" class="ann-filter-btn active px-3.5 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest bg-white dark:bg-indigo-600 text-slate-800 dark:text-white shadow-sm transition-all cursor-pointer">
                    Semua
                </button>
                <button type="button" data-filter="active" class="ann-filter-btn px-3.5 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white transition-all cursor-pointer">
                    Aktif
                </button>
                <button type="button" data-filter="draft" class="ann-filter-btn px-3.5 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white transition-all cursor-pointer">
                    Draft
                </button>
                <button type="button" data-filter="danger" class="ann-filter-btn px-3.5 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition-all cursor-pointer">
                    Urgent
                </button>
            </div>
        </div>
        @endif

        <div class="overflow-x-auto text-slate-600 dark:text-slate-300 announcement-table-scroll">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-950/80 border-b border-gray-100 dark:border-slate-800">
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest">Konten Pengumuman</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest text-center">Urgensi</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest text-center">Visibilitas</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest text-center">Publisher</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest text-right whitespace-nowrap">Aksi Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-800/80">
                    @forelse($announcements as $ann)
                    @php
                        $isNew = $ann->created_at->diffInHours(now()) < 24;
                        $typeColors = [
                            'info' => 'bg-blue-50 dark:bg-blue-500/15 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-500/30',
                            'warning' => 'bg-amber-50 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-500/30',
                            'danger' => 'bg-rose-50 dark:bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-500/30',
                            'success' => 'bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/30'
                        ];
                    @endphp
                    <tr class="announcement-row hover:bg-slate-50/80 dark:hover:bg-slate-800/45 transition-all group"
                        data-search="{{ strtolower($ann->title . ' ' . $ann->content . ' ' . ($ann->department->name ?? 'seluruh divisi') . ' ' . ($ann->author->name ?? '')) }}"
                        data-active="{{ $ann->is_active ? 'active' : 'draft' }}"
                        data-type="{{ strtolower($ann->type) }}">
                        <td class="px-8 py-6">
                            <div class="flex items-start gap-3">
                                <div class="mt-1">
                                    <div class="flex flex-wrap items-center gap-2 mb-1">
                                        <p class="font-black text-slate-800 dark:text-white uppercase tracking-tight text-sm group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $ann->title }}</p>
                                        @if($isNew)
                                            <span class="bg-indigo-600 text-white text-[7px] font-black px-2 py-0.5 rounded-full uppercase tracking-widest animate-pulse">Terbaru</span>
                                        @endif
                                        @if($ann->type == 'danger')
                                            <span class="bg-rose-600 text-white text-[7px] font-black px-2 py-0.5 rounded-full uppercase tracking-widest">Urgent</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-400 dark:text-slate-400 line-clamp-1 max-w-md font-medium italic">"{{ Str::limit($ann->content, 80) }}"</p>
                                    <div class="mt-2 flex items-center gap-2">
                                        <span class="text-[9px] font-black bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-300 px-2.5 py-1 rounded-md border border-slate-200/50 dark:border-slate-700 uppercase">
                                            <i class="fas fa-layer-group mr-1 text-indigo-500 dark:text-indigo-400"></i> {{ $ann->department->name ?? 'SELURUH DIVISI' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6 text-center whitespace-nowrap">
                            <span class="{{ $typeColors[strtolower($ann->type)] ?? 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700' }} inline-flex items-center px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest shadow-sm dark:shadow-none">
                                <i class="fas fa-circle text-[6px] mr-1.5 opacity-70"></i>{{ $ann->type }}
                            </span>
                        </td>
                        <td class="px-6 py-6 text-center whitespace-nowrap">
                            <form action="{{ route('admin.master.announcement.toggle', $ann->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" title="Klik untuk ubah status visibilitas" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border transition-all cursor-pointer {{ $ann->is_active ? 'bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border-emerald-200/70 dark:border-emerald-500/30 hover:bg-emerald-100 dark:hover:bg-emerald-500/25' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-300 border-slate-200/70 dark:border-slate-700 hover:bg-slate-200/70 dark:hover:bg-slate-700' }}">
                                    <i class="fas {{ $ann->is_active ? 'fa-eye' : 'fa-eye-slash' }} text-[10px]"></i>
                                    <span class="text-[9px] font-black uppercase tracking-wider">{{ $ann->is_active ? 'Aktif' : 'Draft' }}</span>
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-6 text-center whitespace-nowrap">
                            <div class="flex flex-col items-center">
                                <p class="text-xs font-black text-slate-700 dark:text-slate-200 tracking-tight">{{ $ann->author->name }}</p>
                                <p class="text-[9px] text-slate-400 dark:text-slate-400 font-bold uppercase">{{ $ann->created_at->translatedFormat('d M Y') }}</p>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right whitespace-nowrap">
                            <div class="flex justify-end gap-2 opacity-90 group-hover:opacity-100 transition-opacity">
                                {{-- [BARU] Tombol Pratinjau Cepat Konten Lengkap --}}
                                <button type="button"
                                    onclick="previewAnnouncement(this)"
                                    data-title="{{ $ann->title }}"
                                    data-content="{{ $ann->content }}"
                                    data-dept="{{ $ann->department->name ?? 'SELURUH DIVISI' }}"
                                    data-type="{{ strtoupper($ann->type) }}"
                                    data-status="{{ $ann->is_active ? 'Aktif' : 'Draft' }}"
                                    data-author="{{ $ann->author->name }}"
                                    data-date="{{ $ann->created_at->translatedFormat('d M Y, H:i') }}"
                                    title="Pratinjau Isi Pengumuman"
                                    class="w-9 h-9 flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-indigo-600 dark:hover:bg-indigo-600 hover:text-white dark:hover:text-white border border-transparent dark:border-slate-700 rounded-xl transition-all shadow-sm dark:shadow-none cursor-pointer">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>

                                <a href="{{ route('admin.master.announcement.edit', $ann->id) }}" title="Edit Pengumuman" class="w-9 h-9 flex items-center justify-center bg-blue-50 dark:bg-blue-500/15 text-blue-600 dark:text-blue-400 hover:bg-blue-600 dark:hover:bg-blue-600 hover:text-white dark:hover:text-white border border-transparent dark:border-blue-500/30 rounded-xl transition-all shadow-sm dark:shadow-none">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('admin.master.announcement.destroy', $ann->id) }}" method="POST" class="delete-announcement-form" data-title="{{ $ann->title }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Hapus Pengumuman" class="w-9 h-9 flex items-center justify-center bg-rose-50 dark:bg-rose-500/15 text-rose-600 dark:text-rose-400 hover:bg-rose-600 dark:hover:bg-rose-600 hover:text-white dark:hover:text-white border border-transparent dark:border-rose-500/30 rounded-xl transition-all shadow-sm dark:shadow-none cursor-pointer">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-24 text-center">
                            <div class="flex flex-col items-center justify-center opacity-40 dark:opacity-60">
                                <div class="w-24 h-24 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4 border-2 border-dashed border-slate-200 dark:border-slate-700">
                                    <i class="fas fa-bullhorn text-5xl text-slate-400 dark:text-slate-500"></i>
                                </div>
                                <h3 class="text-sm font-black text-slate-500 dark:text-slate-300 uppercase tracking-[0.3em]">Belum Ada Pengumuman</h3>
                            </div>
                        </td>
                    </tr>
                    @endforelse

                    {{-- [BARU] Baris Kosong Dinamis Saat Filter / Pencarian Tidak Cocok --}}
                    <tr id="emptyAnnouncementFilterRow" class="hidden">
                        <td colspan="5" class="px-8 py-16 text-center">
                            <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-slate-200/70 dark:border-slate-700">
                                <i class="fas fa-search text-2xl text-slate-400 dark:text-slate-500"></i>
                            </div>
                            <p class="text-sm font-black text-slate-700 dark:text-slate-200 uppercase tracking-tight">Pengumuman Tidak Ditemukan</p>
                            <p class="text-xs text-slate-400 dark:text-slate-400 mt-1">Ubah kata kunci pencarian atau pilih tab filter lainnya.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        {{-- 4. Pagination --}}
        @if($announcements->hasPages())
        <div class="px-8 py-6 border-t border-gray-50 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-950/60 announcement-pagination-wrapper">
            {{ $announcements->links() }}
        </div>
        @endif
    </div>
</div>

{{-- [BARU] Modal Pratinjau Cepat Pengumuman --}}
<div id="announcementPreviewModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-950/75 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-[2rem] max-w-xl w-full overflow-hidden shadow-2xl transform transition-all">
        <div class="px-7 py-5 bg-slate-50 dark:bg-slate-950/90 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-4">
            <div class="flex items-center gap-2.5">
                <span id="previewTypeBadge" class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 border border-indigo-200/60 dark:border-indigo-500/30">INFO</span>
                <span id="previewStatusBadge" class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">DRAFT</span>
            </div>
            <button type="button" onclick="closePreviewModal()" class="w-8 h-8 rounded-xl bg-slate-200/70 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-rose-500 dark:hover:text-rose-400 flex items-center justify-center transition-colors cursor-pointer">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
        <div class="p-7 space-y-4">
            <div>
                <span id="previewDeptBadge" class="inline-flex items-center text-[9px] font-black bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 px-2.5 py-1 rounded-md border border-slate-200/50 dark:border-slate-700 uppercase mb-2">
                    SELURUH DIVISI
                </span>
                <h3 id="previewTitle" class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-tight leading-snug"></h3>
            </div>
            <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-950/80 border border-slate-100 dark:border-slate-800 max-h-64 overflow-y-auto">
                <p id="previewContent" class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line font-medium"></p>
            </div>
            <div class="pt-2 flex items-center justify-between text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                <span><i class="fas fa-user-edit mr-1 text-indigo-500"></i> <span id="previewAuthor"></span></span>
                <span><i class="fas fa-clock mr-1"></i> <span id="previewDate"></span></span>
            </div>
        </div>
    </div>
</div>

{{-- [BARU] Dukungan Dark Mode untuk Scrollbar, Pagination Laravel & SweetAlert2 --}}
<style>
    .announcement-table-scroll::-webkit-scrollbar { height: 8px; }
    .announcement-table-scroll::-webkit-scrollbar-track { background: transparent; }
    .announcement-table-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
    html.dark .announcement-table-scroll::-webkit-scrollbar-thumb,
    body.dark .announcement-table-scroll::-webkit-scrollbar-thumb,
    .dark .announcement-table-scroll::-webkit-scrollbar-thumb {
        background: #334155;
    }

    html.dark .announcement-pagination-wrapper nav[role="navigation"] a,
    body.dark .announcement-pagination-wrapper nav[role="navigation"] a,
    .dark .announcement-pagination-wrapper nav[role="navigation"] a,
    html.dark .announcement-pagination-wrapper nav[role="navigation"] span[aria-disabled="true"] > span,
    body.dark .announcement-pagination-wrapper nav[role="navigation"] span[aria-disabled="true"] > span,
    .dark .announcement-pagination-wrapper nav[role="navigation"] span[aria-disabled="true"] > span {
        background-color: #0f172a !important;
        border-color: #1e293b !important;
        color: #cbd5e1 !important;
    }
    html.dark .announcement-pagination-wrapper nav[role="navigation"] a:hover,
    body.dark .announcement-pagination-wrapper nav[role="navigation"] a:hover,
    .dark .announcement-pagination-wrapper nav[role="navigation"] a:hover {
        background-color: #1e293b !important;
        color: #ffffff !important;
    }
    html.dark .announcement-pagination-wrapper nav[role="navigation"] span[aria-current="page"] > span,
    body.dark .announcement-pagination-wrapper nav[role="navigation"] span[aria-current="page"] > span,
    .dark .announcement-pagination-wrapper nav[role="navigation"] span[aria-current="page"] > span {
        background-color: #4f46e5 !important;
        border-color: #4f46e5 !important;
        color: #ffffff !important;
    }

    html.dark .swal2-popup,
    body.dark .swal2-popup,
    .dark .swal2-popup {
        background: #0f172a !important;
        color: #f1f5f9 !important;
        border: 1px solid #1e293b !important;
        border-radius: 1.5rem !important;
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

{{-- [BARU] Script Pencarian Instan, Filter Tab, Pratinjau Modal & Konfirmasi Hapus --}}
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function previewAnnouncement(btn) {
        const modal = document.getElementById('announcementPreviewModal');
        if (!modal) return;

        document.getElementById('previewTitle').innerText = btn.dataset.title || '';
        document.getElementById('previewContent').innerText = btn.dataset.content || '';
        document.getElementById('previewDeptBadge').innerHTML = '<i class="fas fa-layer-group mr-1 text-indigo-500"></i> ' + (btn.dataset.dept || 'SELURUH DIVISI');
        document.getElementById('previewTypeBadge').innerText = btn.dataset.type || 'INFO';
        document.getElementById('previewStatusBadge').innerText = btn.dataset.status || 'DRAFT';
        document.getElementById('previewAuthor').innerText = btn.dataset.author || '-';
        document.getElementById('previewDate').innerText = btn.dataset.date || '-';

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closePreviewModal() {
        const modal = document.getElementById('announcementPreviewModal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('announcementPreviewModal');
        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) closePreviewModal();
            });
        }

        // Live Search & Filter Status/Urgensi
        const searchInput = document.getElementById('announcementSearchInput');
        const filterBtns = document.querySelectorAll('.ann-filter-btn');
        const rows = document.querySelectorAll('.announcement-row');
        const emptyRow = document.getElementById('emptyAnnouncementFilterRow');
        let currentFilter = 'all';

        function filterAnnouncements() {
            const keyword = searchInput ? searchInput.value.toLowerCase().trim() : '';
            let visible = 0;

            rows.forEach(row => {
                const text = row.dataset.search || '';
                const activeState = row.dataset.active || '';
                const typeState = row.dataset.type || '';

                const matchKeyword = text.includes(keyword);
                const matchFilter =
                    currentFilter === 'all' ||
                    (currentFilter === 'active' && activeState === 'active') ||
                    (currentFilter === 'draft' && activeState === 'draft') ||
                    (currentFilter === 'danger' && typeState === 'danger');

                if (matchKeyword && matchFilter) {
                    row.classList.remove('hidden');
                    visible++;
                } else {
                    row.classList.add('hidden');
                }
            });

            if (emptyRow) {
                emptyRow.classList.toggle('hidden', visible > 0 || rows.length === 0);
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', filterAnnouncements);
        }

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                currentFilter = this.dataset.filter;
                filterBtns.forEach(b => {
                    b.classList.remove('active', 'bg-white', 'dark:bg-indigo-600', 'text-slate-800', 'dark:text-white', 'shadow-sm');
                    b.classList.add('text-slate-500', 'dark:text-slate-400');
                });
                this.classList.add('active', 'bg-white', 'dark:bg-indigo-600', 'text-slate-800', 'dark:text-white', 'shadow-sm');
                this.classList.remove('text-slate-500', 'dark:text-slate-400');
                filterAnnouncements();
            });
        });

        // Konfirmasi Hapus Modern (SweetAlert2)
        document.querySelectorAll('.delete-announcement-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                if (typeof Swal === 'undefined') return;
                e.preventDefault();
                const title = this.dataset.title || 'pengumuman ini';
                Swal.fire({
                    title: 'Hapus Pengumuman?',
                    text: 'Konten "' + title + '" akan dihapus secara permanen dari sistem.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection