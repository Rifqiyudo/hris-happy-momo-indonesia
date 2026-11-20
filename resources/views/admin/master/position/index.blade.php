@extends('admin.layouts.master')
@section('title', 'Data Jabatan - PT. Happy Momo Indonesia')

@section('content')
{{-- [BARU] Scoped Style untuk Kartu Level Jabatan (Kebal Override Dark/Light Mode) --}}
<style>
    /* State Default (Mode Terang) */
    .level-option-card {
        position: relative;
        transition: all 0.2s ease;
    }
    html:not(.dark) .level-option-card {
        background-color: #f8fafc !important;
        border-color: #e2e8f0 !important;
    }
    html:not(.dark) .level-option-card:hover {
        border-color: #818cf8 !important;
        background-color: #eef2ff !important;
    }

    /* State Terpilih / Aktif (Mode Terang) */
    html:not(.dark) .level-option-card.is-level-active,
    html:not(.dark) input[name="level"]:checked + .level-option-card {
        background-color: #eef2ff !important;
        border-color: #4f46e5 !important;
        box-shadow: 0 0 0 1px #4f46e5, 0 8px 20px -6px rgba(79, 70, 229, 0.35) !important;
    }
    html:not(.dark) .level-option-card.is-level-active .level-title,
    html:not(.dark) input[name="level"]:checked + .level-option-card .level-title {
        color: #4338ca !important;
    }
    html:not(.dark) .level-option-card.is-level-active .level-subtitle,
    html:not(.dark) input[name="level"]:checked + .level-option-card .level-subtitle {
        color: #4f46e5 !important;
    }

    /* State Default (Mode Malam / Dark Mode) */
    html.dark .level-option-card,
    .dark .level-option-card {
        background-color: rgba(2, 6, 23, 0.75) !important;
        border-color: #1e293b !important;
    }
    html.dark .level-option-card:hover,
    .dark .level-option-card:hover {
        border-color: #4f46e5 !important;
        background-color: rgba(15, 23, 42, 0.95) !important;
    }

    /* State Terpilih / Aktif (Mode Malam / Dark Mode) */
    html.dark .level-option-card.is-level-active,
    .dark .level-option-card.is-level-active,
    html.dark input[name="level"]:checked + .level-option-card,
    .dark input[name="level"]:checked + .level-option-card {
        background-color: rgba(79, 70, 229, 0.2) !important;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 1px #6366f1, 0 10px 25px -8px rgba(99, 102, 241, 0.6) !important;
    }
    html.dark .level-option-card.is-level-active .level-title,
    .dark .level-option-card.is-level-active .level-title,
    html.dark input[name="level"]:checked + .level-option-card .level-title,
    .dark input[name="level"]:checked + .level-option-card .level-title {
        color: #ffffff !important;
    }
    html.dark .level-option-card.is-level-active .level-subtitle,
    .dark .level-option-card.is-level-active .level-subtitle,
    html.dark input[name="level"]:checked + .level-option-card .level-subtitle,
    .dark input[name="level"]:checked + .level-option-card .level-subtitle {
        color: #a5b4fc !important;
    }

    /* Indikator Centang Kecil di Pojok Kanan Atas Kartu */
    .level-check-badge {
        opacity: 0;
        transform: scale(0.75);
        transition: all 0.2s ease;
    }
    .level-option-card.is-level-active .level-check-badge,
    input[name="level"]:checked + .level-option-card .level-check-badge {
        opacity: 1;
        transform: scale(1);
    }
</style>

<div class="max-w-7xl mx-auto px-4 space-y-8 pb-20 animate-fadeIn">
    
    {{-- HEADER TITLE --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <div class="w-10 h-10 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-md shadow-indigo-200 dark:shadow-indigo-950/50">
                    <i class="fas fa-briefcase text-sm"></i>
                </div>
                <h1 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">Manajemen Jabatan</h1>
            </div>
            <p class="text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider ml-13">Kelola struktur jabatan, tingkatan level, dan penempatan departemen struktural.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- LEFT COLUMN: DATA TABLE --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Header & Search --}}
            <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl shadow-sm dark:shadow-none border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div>
                    <h2 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Daftar Jabatan (Positions)</h2>
                    <p class="text-[10px] text-slate-400 dark:text-slate-400 font-black uppercase tracking-widest mt-0.5">Total: <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ $positions->count() }}</span> Jabatan Struktural</p>
                </div>
                
                <form action="{{ route('admin.master.position.index') }}" method="GET" class="w-full sm:w-auto relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        class="w-full sm:w-64 pl-10 pr-4 py-3 border border-slate-200 dark:border-slate-800 rounded-2xl text-xs font-bold text-slate-700 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 bg-slate-50/50 dark:bg-slate-950/70 transition-all outline-none placeholder-slate-400 dark:placeholder-slate-500 shadow-sm dark:shadow-none" 
                        placeholder="Cari Jabatan...">
                    <i class="fas fa-search absolute left-3.5 top-3.5 text-slate-400 dark:text-slate-500 text-xs"></i>
                </form>
            </div>

            {{-- Data Table --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm dark:shadow-none border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300 border-collapse">
                        <thead class="bg-slate-50/80 dark:bg-slate-950/60 text-slate-400 dark:text-slate-400 font-black uppercase text-[10px] tracking-[0.2em] border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="px-6 py-5">Nama Jabatan & Level</th>
                                <th class="px-6 py-5">Departemen</th>
                                <th class="px-6 py-5 text-center">Pegawai</th>
                                <th class="px-6 py-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            @forelse($positions as $pos)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors duration-200 group">
                                
                                {{-- Name & Level --}}
                                <td class="px-6 py-5 align-middle">
                                    <div class="flex items-start gap-4">
                                        <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 shadow-sm dark:shadow-none font-black text-xs border mt-0.5
                                            {{ $pos->level == 'manager' || $pos->level == 'director' ? 'bg-purple-50 dark:bg-purple-500/15 text-purple-600 dark:text-purple-400 border-purple-100 dark:border-purple-500/30' : 
                                              ($pos->level == 'supervisor' ? 'bg-amber-50 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 border-amber-100 dark:border-amber-500/30' : 'bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 border-indigo-100 dark:border-indigo-500/30') }}">
                                            <i class="fas fa-briefcase"></i>
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-800 dark:text-slate-100 text-xs uppercase tracking-tight group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition mb-1.5">{{ $pos->name }}</p>
                                            
                                            {{-- Badge Level --}}
                                            @if($pos->level == 'director')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-rose-50 dark:bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-500/30">Director / C-Level</span>
                                            @elseif($pos->level == 'manager')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-purple-50 dark:bg-purple-500/15 text-purple-600 dark:text-purple-400 border border-purple-100 dark:border-purple-500/30">Manager</span>
                                            @elseif($pos->level == 'supervisor')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-amber-50 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-500/30">Supervisor / SPV</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">Staff / Operasional</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Department --}}
                                <td class="px-6 py-5 align-middle">
                                    @if($pos->department)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-800/90 text-slate-600 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                                            <i class="fas fa-layer-group text-[9px] text-slate-400 dark:text-indigo-400"></i> {{ $pos->department->name }}
                                        </span>
                                    @else
                                        <span class="text-xs text-rose-400 dark:text-rose-400 italic font-bold">No Dept</span>
                                    @endif
                                </td>

                                {{-- Employee Count --}}
                                <td class="px-6 py-5 align-middle text-center">
                                    @php $count = $pos->users ? $pos->users->count() : 0; @endphp
                                    @if($count > 0)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/30">
                                            <i class="fas fa-users text-[9px]"></i> {{ $count }}
                                        </span>
                                    @else
                                        <span class="text-slate-300 dark:text-slate-600 font-bold text-xs">-</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-5 align-middle text-center">
                                    <div class="flex justify-center items-center gap-2">
                                        <button type="button" 
                                            onclick="editPosition(this)"
                                            data-id="{{ $pos->id }}"
                                            data-name="{{ $pos->name }}"
                                            data-dept="{{ $pos->department_id }}"
                                            data-level="{{ $pos->level }}"
                                            data-desc="{{ $pos->description }}"
                                            class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-500/15 hover:border-amber-200 dark:hover:border-amber-500/30 transition-all flex items-center justify-center shadow-sm dark:shadow-none" title="Edit">
                                            <i class="fas fa-pencil-alt text-xs"></i>
                                        </button>
                                        
                                        <button type="button" onclick="confirmDelete('{{ $pos->id }}', '{{ $pos->name }}')" 
                                            class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/15 hover:border-rose-200 dark:hover:border-rose-500/30 transition-all flex items-center justify-center shadow-sm dark:shadow-none" title="Hapus">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>

                                        <form id="delete-form-{{ $pos->id }}" action="{{ route('admin.master.position.destroy', $pos->id) }}" method="POST" class="hidden">
                                            @csrf @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center text-slate-400 dark:text-slate-500">
                                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800/60 text-slate-300 dark:text-slate-500 rounded-3xl flex items-center justify-center text-2xl mx-auto mb-3 border-2 border-dashed border-slate-200 dark:border-slate-700">
                                        <i class="fas fa-briefcase"></i>
                                    </div>
                                    <p class="font-black text-slate-700 dark:text-slate-200 uppercase tracking-tight text-sm">Belum Ada Data Jabatan</p>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Silakan tambahkan jabatan baru melalui form di samping.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                @if(method_exists($positions, 'links'))
                    <div class="px-6 py-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40">
                        {{ $positions->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT COLUMN: INPUT FORM --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm dark:shadow-none border border-slate-200 dark:border-slate-800 sticky top-6 overflow-hidden">
                
                <div class="bg-slate-900 dark:bg-slate-950/80 px-6 py-5 border-b border-slate-800 flex justify-between items-center">
                    <h3 class="font-black text-white uppercase tracking-wider text-xs flex items-center gap-2.5" id="formTitle">
                        <i class="fas fa-plus-circle text-emerald-400"></i> Tambah Jabatan
                    </h3>
                </div>

                <div class="p-6">
                    <form id="positionForm" action="{{ route('admin.master.position.store') }}" method="POST">
                        @csrf
                        <div id="methodField"></div>
                        
                        <div class="space-y-5">
                            
                            {{-- 1. Job Title --}}
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nama Jabatan <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" id="nameInput" required 
                                    class="w-full bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-xs font-bold text-slate-700 dark:text-slate-100 outline-none focus:ring-2 focus:ring-indigo-500 transition-all placeholder-slate-400 dark:placeholder-slate-500" 
                                    placeholder="Contoh: Digital Marketing Spv">
                            </div>

                            {{-- 2. Department (MANDATORY) --}}
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Departemen <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <select name="department_id" id="deptInput" required 
                                        class="w-full bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-xs font-bold text-slate-700 dark:text-slate-100 outline-none focus:ring-2 focus:ring-indigo-500 appearance-none cursor-pointer">
                                        <option value="">-- Pilih Departemen --</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-400 dark:text-slate-500"><i class="fas fa-chevron-down text-xs"></i></div>
                                </div>
                                <p class="text-[9px] text-slate-400 dark:text-slate-500 italic mt-1">Jabatan ini berada di bawah naungan departemen apa?</p>
                            </div>

                            {{-- 3. Job Level (HIERARCHY) --}}
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Level / Tingkatan <span class="text-rose-500">*</span></label>
                                @php $currentLevel = old('level', 'staff'); @endphp
                                <div class="grid grid-cols-2 gap-3" id="levelGrid">
                                    <label for="level_staff" class="cursor-pointer block" onclick="selectLevelCard('staff')">
                                        <input type="radio" id="level_staff" name="level" value="staff" class="peer sr-only" {{ $currentLevel === 'staff' ? 'checked' : '' }} required>
                                        <div data-level-card="staff" class="level-option-card {{ $currentLevel === 'staff' ? 'is-level-active' : '' }} p-3.5 rounded-2xl border-2 border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/70 peer-checked:border-indigo-600 dark:peer-checked:border-indigo-500 peer-checked:bg-indigo-50/40 dark:peer-checked:bg-indigo-500/15 hover:border-slate-300 dark:hover:border-slate-700 transition-all">
                                            <span class="level-check-badge absolute top-2.5 right-2.5 w-4 h-4 rounded-full bg-indigo-600 dark:bg-indigo-500 text-white flex items-center justify-center shadow-sm">
                                                <i class="fas fa-check text-[8px]"></i>
                                            </span>
                                            <p class="level-title text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">Staff</p>
                                            <p class="level-subtitle text-[9px] text-slate-400 dark:text-slate-400 font-bold mt-0.5">Pelaksana Teknis</p>
                                        </div>
                                    </label>
                                    <label for="level_supervisor" class="cursor-pointer block" onclick="selectLevelCard('supervisor')">
                                        <input type="radio" id="level_supervisor" name="level" value="supervisor" class="peer sr-only" {{ $currentLevel === 'supervisor' ? 'checked' : '' }}>
                                        <div data-level-card="supervisor" class="level-option-card {{ $currentLevel === 'supervisor' ? 'is-level-active' : '' }} p-3.5 rounded-2xl border-2 border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/70 peer-checked:border-indigo-600 dark:peer-checked:border-indigo-500 peer-checked:bg-indigo-50/40 dark:peer-checked:bg-indigo-500/15 hover:border-slate-300 dark:hover:border-slate-700 transition-all">
                                            <span class="level-check-badge absolute top-2.5 right-2.5 w-4 h-4 rounded-full bg-indigo-600 dark:bg-indigo-500 text-white flex items-center justify-center shadow-sm">
                                                <i class="fas fa-check text-[8px]"></i>
                                            </span>
                                            <p class="level-title text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">Supervisor</p>
                                            <p class="level-subtitle text-[9px] text-slate-400 dark:text-slate-400 font-bold mt-0.5">Team Leader</p>
                                        </div>
                                    </label>
                                    <label for="level_manager" class="cursor-pointer block" onclick="selectLevelCard('manager')">
                                        <input type="radio" id="level_manager" name="level" value="manager" class="peer sr-only" {{ $currentLevel === 'manager' ? 'checked' : '' }}>
                                        <div data-level-card="manager" class="level-option-card {{ $currentLevel === 'manager' ? 'is-level-active' : '' }} p-3.5 rounded-2xl border-2 border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/70 peer-checked:border-indigo-600 dark:peer-checked:border-indigo-500 peer-checked:bg-indigo-50/40 dark:peer-checked:bg-indigo-500/15 hover:border-slate-300 dark:hover:border-slate-700 transition-all">
                                            <span class="level-check-badge absolute top-2.5 right-2.5 w-4 h-4 rounded-full bg-indigo-600 dark:bg-indigo-500 text-white flex items-center justify-center shadow-sm">
                                                <i class="fas fa-check text-[8px]"></i>
                                            </span>
                                            <p class="level-title text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">Manager</p>
                                            <p class="level-subtitle text-[9px] text-slate-400 dark:text-slate-400 font-bold mt-0.5">Kepala Divisi</p>
                                        </div>
                                    </label>
                                    <label for="level_director" class="cursor-pointer block" onclick="selectLevelCard('director')">
                                        <input type="radio" id="level_director" name="level" value="director" class="peer sr-only" {{ $currentLevel === 'director' ? 'checked' : '' }}>
                                        <div data-level-card="director" class="level-option-card {{ $currentLevel === 'director' ? 'is-level-active' : '' }} p-3.5 rounded-2xl border-2 border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/70 peer-checked:border-indigo-600 dark:peer-checked:border-indigo-500 peer-checked:bg-indigo-50/40 dark:peer-checked:bg-indigo-500/15 hover:border-slate-300 dark:hover:border-slate-700 transition-all">
                                            <span class="level-check-badge absolute top-2.5 right-2.5 w-4 h-4 rounded-full bg-indigo-600 dark:bg-indigo-500 text-white flex items-center justify-center shadow-sm">
                                                <i class="fas fa-check text-[8px]"></i>
                                            </span>
                                            <p class="level-title text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight">C-Level</p>
                                            <p class="level-subtitle text-[9px] text-slate-400 dark:text-slate-400 font-bold mt-0.5">Pimpinan</p>
                                        </div>
                                    </label>
                                </div>
                                @error('level')
                                    <p class="text-[10px] font-bold text-rose-500 mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- 4. Description --}}
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Deskripsi / Jobdesc Singkat</label>
                                <textarea name="description" id="descInput" rows="3" 
                                    class="w-full bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-xs font-bold text-slate-700 dark:text-slate-100 outline-none focus:ring-2 focus:ring-indigo-500 transition-all resize-none placeholder-slate-400 dark:placeholder-slate-500" 
                                    placeholder="Tanggung jawab utama..."></textarea>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex items-center gap-3 pt-2">
                                <button type="button" id="btnCancel" onclick="resetForm()" 
                                    class="hidden flex-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 py-3.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                                    Batal
                                </button>
                                <button type="submit" id="btnSubmit" 
                                    class="flex-1 bg-indigo-600 text-white py-3.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-indigo-200 dark:shadow-indigo-950/50 hover:bg-indigo-700 transition-all active:scale-95 flex items-center justify-center gap-2">
                                    <i class="fas fa-save"></i> <span>Simpan</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // [BARU] Normalisasi & Sinkronisasi Visual Kartu Level Jabatan (Staff / Supervisor / Manager / C-Level)
    function normalizeLevelValue(rawLevel) {
        if (!rawLevel) return 'staff';
        const val = String(rawLevel).trim().toLowerCase();
        if (val === 'director' || val === 'c-level' || val === 'clevel' || val.includes('direktur')) {
            return 'director';
        }
        if (val === 'manager' || val.includes('manajer')) {
            return 'manager';
        }
        if (val === 'supervisor' || val === 'spv' || val.includes('leader')) {
            return 'supervisor';
        }
        return 'staff';
    }

    function selectLevelCard(rawLevel) {
        const targetLevel = normalizeLevelValue(rawLevel);

        document.querySelectorAll('input[name="level"]').forEach(radio => {
            radio.checked = (radio.value === targetLevel);
        });

        document.querySelectorAll('[data-level-card]').forEach(card => {
            const isMatch = card.getAttribute('data-level-card') === targetLevel;
            card.classList.toggle('is-level-active', isMatch);
        });
    }

    // [BARU] Sinkronkan tampilan kartu saat halaman pertama kali dibuka & jika radio berubah lewat keyboard
    document.addEventListener('DOMContentLoaded', function () {
        const checkedRadio = document.querySelector('input[name="level"]:checked');
        selectLevelCard(checkedRadio ? checkedRadio.value : 'staff');

        document.querySelectorAll('input[name="level"]').forEach(radio => {
            radio.addEventListener('change', function () {
                selectLevelCard(this.value);
            });
        });
    });

    function editPosition(btn) {
        // Get data
        const id = btn.dataset.id;
        const name = btn.dataset.name;
        const dept = btn.dataset.dept;
        const level = btn.dataset.level;
        const desc = btn.dataset.desc;

        // Update UI Form
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-pencil-alt text-amber-400 mr-2"></i> Edit Jabatan';
        document.getElementById('nameInput').value = name;
        document.getElementById('deptInput').value = dept;
        document.getElementById('descInput').value = desc || '';

        // Select Level Radio Button & Sync Visual Card
        selectLevelCard(level);

        // Update Action
        let baseUrl = "{{ route('admin.master.position.index') }}"; 
        document.getElementById('positionForm').action = baseUrl + '/' + id;
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        // Update Button
        let btnSubmit = document.getElementById('btnSubmit');
        btnSubmit.innerHTML = '<i class="fas fa-sync-alt"></i> <span>Update Data</span>';
        btnSubmit.classList.replace('bg-indigo-600', 'bg-amber-500');
        btnSubmit.classList.replace('hover:bg-indigo-700', 'hover:bg-amber-600');
        btnSubmit.classList.replace('shadow-indigo-200', 'shadow-amber-200');
        btnSubmit.classList.replace('dark:shadow-indigo-950/50', 'dark:shadow-amber-950/50');
        
        document.getElementById('btnCancel').classList.remove('hidden');

        // Scroll Top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function resetForm() {
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus-circle text-emerald-400 mr-2"></i> Tambah Jabatan';
        document.getElementById('positionForm').reset();
        document.getElementById('positionForm').action = "{{ route('admin.master.position.store') }}";
        document.getElementById('methodField').innerHTML = '';

        // [BARU] Kembalikan pilihan kartu level ke default 'staff' secara visual
        selectLevelCard('staff');

        let btnSubmit = document.getElementById('btnSubmit');
        btnSubmit.innerHTML = '<i class="fas fa-save"></i> <span>Simpan</span>';
        btnSubmit.classList.replace('bg-amber-500', 'bg-indigo-600');
        btnSubmit.classList.replace('hover:bg-amber-600', 'hover:bg-indigo-700');
        btnSubmit.classList.replace('shadow-amber-200', 'shadow-indigo-200');
        btnSubmit.classList.replace('dark:shadow-amber-950/50', 'dark:shadow-indigo-950/50');
        
        document.getElementById('btnCancel').classList.add('hidden');
    }

    function confirmDelete(id, name) {
        const isDark = document.documentElement.classList.contains('dark');
        Swal.fire({
            title: 'Hapus Jabatan?',
            text: "Jabatan " + name + " akan dihapus. Pastikan tidak ada karyawan yang menggunakan jabatan ini.",
            icon: 'warning',
            background: isDark ? '#0f172a' : '#ffffff',
            color: isDark ? '#f8fafc' : '#1e293b',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-3xl border border-slate-200 dark:border-slate-800',
                confirmButton: 'rounded-xl text-xs font-black uppercase tracking-wider px-5 py-3',
                cancelButton: 'rounded-xl text-xs font-black uppercase tracking-wider px-5 py-3'
            }
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('delete-form-' + id).submit();
        })
    }
</script>
@endsection