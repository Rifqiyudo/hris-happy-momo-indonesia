@extends('admin.layouts.master')
@section('title', 'Manajemen Role & Hak Akses')

@section('content')
<div class="space-y-6 animate-fadeIn">
    
    {{-- HEADER --}}
    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="font-black text-slate-800 text-xl tracking-tight">Role & Hak Akses</h2>
            <p class="text-xs text-slate-500 mt-1">Kelola tingkat otorisasi dan batasan akses pengguna di dalam sistem HRIS.</p>
        </div>
        <button onclick="openModal('addRoleModal')" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-200 transition-all flex items-center gap-2">
            <i class="fas fa-plus"></i> Tambah Role
        </button>
    </div>

    {{-- ROLE CARDS GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($roles as $role)
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg hover:border-indigo-100 transition-all duration-300 flex flex-col overflow-hidden group">
            
            {{-- Card Header --}}
            <div class="p-6 border-b border-slate-50 flex justify-between items-start relative overflow-hidden">
                <div class="z-10">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4 shadow-sm {{ $role->name == 'admin' ? 'bg-rose-50 text-rose-500' : ($role->name == 'pic' ? 'bg-amber-50 text-amber-500' : 'bg-emerald-50 text-emerald-500') }}">
                        <i class="fas {{ $role->name == 'admin' ? 'fa-user-shield' : ($role->name == 'pic' ? 'fa-user-tie' : 'fa-users') }} text-xl"></i>
                    </div>
                    <h3 class="font-black text-slate-800 text-lg">{{ $role->display_name ?? ucfirst($role->name) }}</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">
                        <i class="fas fa-fingerprint mr-1"></i> ID: {{ $role->name }}
                    </p>
                </div>
                <div class="z-10 px-3 py-1 rounded-full bg-slate-50 border border-slate-100 text-[10px] font-black text-slate-500 flex items-center gap-1.5 shadow-sm">
                    <i class="fas fa-users text-indigo-400"></i> {{ $role->users_count ?? 0 }} Pengguna
                </div>
                
                {{-- Background Watermark Icon --}}
                <i class="fas {{ $role->name == 'admin' ? 'fa-shield-alt' : 'fa-id-badge' }} absolute -right-6 -bottom-6 text-8xl text-slate-50 opacity-50 group-hover:scale-110 group-hover:-rotate-12 transition-transform duration-500"></i>
            </div>

            {{-- Permissions List Preview --}}
            <div class="p-6 flex-1 bg-slate-50/50">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Hak Akses Utama</p>
                <ul class="space-y-3 text-xs font-bold text-slate-600">
                    @if($role->name == 'admin')
                        <li class="flex items-center gap-3"><i class="fas fa-check-circle text-emerald-500"></i> Akses Seluruh Modul (Full)</li>
                        <li class="flex items-center gap-3"><i class="fas fa-check-circle text-emerald-500"></i> Manajemen Master Data</li>
                        <li class="flex items-center gap-3"><i class="fas fa-check-circle text-emerald-500"></i> Proses Penggajian (Payroll)</li>
                        <li class="flex items-center gap-3"><i class="fas fa-check-circle text-emerald-500"></i> Konfigurasi Sistem</li>
                    @elseif($role->name == 'pic')
                        <li class="flex items-center gap-3"><i class="fas fa-check-circle text-emerald-500"></i> Dashboard Analitik Tim</li>
                        <li class="flex items-center gap-3"><i class="fas fa-check-circle text-emerald-500"></i> Approve Cuti & Kasbon</li>
                        <li class="flex items-center gap-3 text-slate-400"><i class="fas fa-times-circle text-rose-300"></i> Proses Penggajian</li>
                        <li class="flex items-center gap-3 text-slate-400"><i class="fas fa-times-circle text-rose-300"></i> Konfigurasi Sistem</li>
                    @else
                        <li class="flex items-center gap-3"><i class="fas fa-check-circle text-emerald-500"></i> Absensi Mandiri / Barcode</li>
                        <li class="flex items-center gap-3"><i class="fas fa-check-circle text-emerald-500"></i> Pengajuan Cuti & Lembur</li>
                        <li class="flex items-center gap-3"><i class="fas fa-check-circle text-emerald-500"></i> Unduh Slip Gaji</li>
                        <li class="flex items-center gap-3 text-slate-400"><i class="fas fa-times-circle text-rose-300"></i> Akses Admin Panel</li>
                    @endif
                </ul>
            </div>

            {{-- Actions --}}
            <div class="p-4 bg-white border-t border-slate-50 flex gap-2">
                {{-- Tombol Edit Memicu Modal Edit --}}
                <button type="button" onclick="openEditModal({{ $role->id }}, '{{ $role->name }}', '{{ $role->display_name ?? ucfirst($role->name) }}')" class="flex-1 py-2.5 rounded-xl text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-600 hover:text-white transition-colors">
                    Edit Akses
                </button>
                @if($role->name !== 'admin')
                <form action="#" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus role ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-10 h-10 flex items-center justify-center rounded-xl text-rose-500 bg-rose-50 hover:bg-rose-500 hover:text-white transition-colors" title="Hapus Role">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
                @endif
            </div>

        </div>
        @empty
        <div class="col-span-full bg-white p-12 rounded-[2rem] border border-slate-100 shadow-sm text-center">
            <div class="w-20 h-20 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
                <i class="fas fa-user-shield"></i>
            </div>
            <h3 class="font-black text-slate-700 text-lg">Belum Ada Role</h3>
            <p class="text-sm text-slate-400 mt-1">Silakan tambahkan role baru untuk mengelola akses pengguna.</p>
        </div>
        @endforelse
    </div>
</div>

{{-- ======================================================= --}}
{{-- MODAL TAMBAH ROLE BARU --}}
{{-- ======================================================= --}}
<div id="addRoleModal" class="fixed inset-0 z-[60] hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div id="addRoleModalContent" class="bg-white rounded-[2rem] shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform scale-95 transition-transform duration-300">
        
        <div class="sticky top-0 z-10 p-6 border-b border-slate-100 flex justify-between items-center bg-white/90 backdrop-blur-md rounded-t-[2rem]">
            <h3 class="font-black text-indigo-900 text-lg flex items-center gap-2">
                <i class="fas fa-plus-circle text-indigo-500"></i> Tambah Role Baru
            </h3>
            <button type="button" onclick="closeModal('addRoleModal')" class="text-slate-400 hover:text-rose-500 w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="#" method="POST">
            @csrf
            <div class="p-6 space-y-6">
                {{-- Identitas Role --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nama Role (Internal) <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" required placeholder="Contoh: hrd_staff" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                        <p class="text-[10px] text-slate-400 font-medium mt-1.5">*Gunakan huruf kecil (gunakan underscore).</p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nama Tampilan (Display) <span class="text-rose-500">*</span></label>
                        <input type="text" name="display_name" required placeholder="Contoh: Staff HRD" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    </div>
                </div>

                <hr class="border-slate-100">

                {{-- Pilihan Hak Akses (Checkboxes) --}}
                <div>
                    <h4 class="font-black text-slate-700 text-sm mb-4">Pengaturan Hak Akses (Permissions)</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Group Master Data --}}
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                            <h5 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-3 flex items-center gap-2"><i class="fas fa-database text-indigo-400"></i> Master Data</h5>
                            <div class="space-y-2">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="permissions[]" value="view_master" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                    <span class="text-sm font-semibold text-slate-600 group-hover:text-indigo-600 transition-colors">Lihat Master Data</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="permissions[]" value="manage_master" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                    <span class="text-sm font-semibold text-slate-600 group-hover:text-indigo-600 transition-colors">Kelola Master Data (CRUD)</span>
                                </label>
                            </div>
                        </div>

                        {{-- Group Kehadiran & Transaksi --}}
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                            <h5 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-3 flex items-center gap-2"><i class="fas fa-calendar-check text-emerald-400"></i> Transaksi & Aktivitas</h5>
                            <div class="space-y-2">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="permissions[]" value="approve_leave" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                    <span class="text-sm font-semibold text-slate-600 group-hover:text-indigo-600 transition-colors">Approval Pengajuan Cuti</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="permissions[]" value="approve_claim" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                    <span class="text-sm font-semibold text-slate-600 group-hover:text-indigo-600 transition-colors">Approval Klaim / Kasbon</span>
                                </label>
                            </div>
                        </div>

                        {{-- Group Penggajian --}}
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                            <h5 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-3 flex items-center gap-2"><i class="fas fa-money-bill-wave text-amber-400"></i> Penggajian (Payroll)</h5>
                            <div class="space-y-2">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="permissions[]" value="view_payroll" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                    <span class="text-sm font-semibold text-slate-600 group-hover:text-indigo-600 transition-colors">Lihat Data Penggajian</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="permissions[]" value="manage_payroll" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                    <span class="text-sm font-semibold text-slate-600 group-hover:text-indigo-600 transition-colors">Proses / Publish Gaji</span>
                                </label>
                            </div>
                        </div>

                        {{-- Group Pengaturan Sistem --}}
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                            <h5 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-3 flex items-center gap-2"><i class="fas fa-cog text-rose-400"></i> Pengaturan Sistem</h5>
                            <div class="space-y-2">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="permissions[]" value="manage_settings" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                    <span class="text-sm font-semibold text-slate-600 group-hover:text-indigo-600 transition-colors">Ubah Profil Perusahaan</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="permissions[]" value="manage_roles" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                    <span class="text-sm font-semibold text-slate-600 group-hover:text-indigo-600 transition-colors">Kelola Role & Akses</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-6 border-t border-slate-100 flex gap-3 bg-slate-50 rounded-b-[2rem]">
                <button type="button" onclick="closeModal('addRoleModal')" class="flex-1 py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl text-xs uppercase tracking-widest hover:bg-slate-100 transition-colors">Batal</button>
                <button type="submit" class="flex-1 py-3 bg-indigo-600 text-white font-black rounded-xl text-xs uppercase tracking-widest hover:bg-indigo-700 shadow-md shadow-indigo-200 transition-colors">Simpan Role</button>
            </div>
        </form>
    </div>
</div>

{{-- ======================================================= --}}
{{-- MODAL EDIT ROLE --}}
{{-- ======================================================= --}}
<div id="editRoleModal" class="fixed inset-0 z-[60] hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div id="editRoleModalContent" class="bg-white rounded-[2rem] shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform scale-95 transition-transform duration-300">
        
        <div class="sticky top-0 z-10 p-6 border-b border-slate-100 flex justify-between items-center bg-white/90 backdrop-blur-md rounded-t-[2rem]">
            <h3 class="font-black text-indigo-900 text-lg flex items-center gap-2">
                <i class="fas fa-edit text-indigo-500"></i> Edit Konfigurasi Role
            </h3>
            <button type="button" onclick="closeModal('editRoleModal')" class="text-slate-400 hover:text-rose-500 w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="editRoleForm" action="#" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="role_id" id="edit_role_id">

            <div class="p-6 space-y-6">
                {{-- Identitas Role --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nama Role (Internal) <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="edit_name" required readonly class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-xl text-sm font-bold text-slate-500 outline-none cursor-not-allowed">
                        <p class="text-[10px] text-slate-400 font-medium mt-1.5">*ID Role bawaan sistem tidak dapat diubah.</p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nama Tampilan (Display) <span class="text-rose-500">*</span></label>
                        <input type="text" name="display_name" id="edit_display_name" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    </div>
                </div>

                <hr class="border-slate-100">

                {{-- Pilihan Hak Akses (Edit) --}}
                <div id="permissionsWarning" class="hidden bg-amber-50 p-4 rounded-xl border border-amber-100 mb-4 items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                    <p class="text-xs font-bold text-amber-700 leading-relaxed">Perhatian: Hak akses untuk role <span class="uppercase">Admin</span> bersifat mutlak (Superadmin) dan memiliki akses penuh ke seluruh fitur sistem.</p>
                </div>

                <div id="permissionsContainer">
                    <h4 class="font-black text-slate-700 text-sm mb-4">Pengaturan Hak Akses (Permissions)</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Group Master Data --}}
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                            <h5 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-3 flex items-center gap-2"><i class="fas fa-database text-indigo-400"></i> Master Data</h5>
                            <div class="space-y-2">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="permissions[]" value="view_master" class="edit-permission-checkbox w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                    <span class="text-sm font-semibold text-slate-600 group-hover:text-indigo-600 transition-colors">Lihat Master Data</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="permissions[]" value="manage_master" class="edit-permission-checkbox w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                    <span class="text-sm font-semibold text-slate-600 group-hover:text-indigo-600 transition-colors">Kelola Master Data (CRUD)</span>
                                </label>
                            </div>
                        </div>

                        {{-- Group Transaksi --}}
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                            <h5 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-3 flex items-center gap-2"><i class="fas fa-calendar-check text-emerald-400"></i> Transaksi & Aktivitas</h5>
                            <div class="space-y-2">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="permissions[]" value="approve_leave" class="edit-permission-checkbox w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                    <span class="text-sm font-semibold text-slate-600 group-hover:text-indigo-600 transition-colors">Approval Pengajuan Cuti</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="permissions[]" value="approve_claim" class="edit-permission-checkbox w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                    <span class="text-sm font-semibold text-slate-600 group-hover:text-indigo-600 transition-colors">Approval Klaim / Kasbon</span>
                                </label>
                            </div>
                        </div>

                        {{-- Group Penggajian --}}
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                            <h5 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-3 flex items-center gap-2"><i class="fas fa-money-bill-wave text-amber-400"></i> Penggajian (Payroll)</h5>
                            <div class="space-y-2">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="permissions[]" value="view_payroll" class="edit-permission-checkbox w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                    <span class="text-sm font-semibold text-slate-600 group-hover:text-indigo-600 transition-colors">Lihat Data Penggajian</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="permissions[]" value="manage_payroll" class="edit-permission-checkbox w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                    <span class="text-sm font-semibold text-slate-600 group-hover:text-indigo-600 transition-colors">Proses / Publish Gaji</span>
                                </label>
                            </div>
                        </div>

                        {{-- Group Pengaturan --}}
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                            <h5 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-3 flex items-center gap-2"><i class="fas fa-cog text-rose-400"></i> Pengaturan Sistem</h5>
                            <div class="space-y-2">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="permissions[]" value="manage_settings" class="edit-permission-checkbox w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                    <span class="text-sm font-semibold text-slate-600 group-hover:text-indigo-600 transition-colors">Ubah Profil Perusahaan</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="permissions[]" value="manage_roles" class="edit-permission-checkbox w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                    <span class="text-sm font-semibold text-slate-600 group-hover:text-indigo-600 transition-colors">Kelola Role & Akses</span>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            
            <div class="p-6 border-t border-slate-100 flex gap-3 bg-slate-50 rounded-b-[2rem]">
                <button type="button" onclick="closeModal('editRoleModal')" class="flex-1 py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl text-xs uppercase tracking-widest hover:bg-slate-100 transition-colors">Batal</button>
                <button type="submit" class="flex-1 py-3 bg-indigo-600 text-white font-black rounded-xl text-xs uppercase tracking-widest hover:bg-indigo-700 shadow-md shadow-indigo-200 transition-colors">Update Role</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // FUNGSI MEMBUKA MODAL UMUM
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        const content = document.getElementById(modalId + 'Content');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);
    }

    // FUNGSI MENUTUP MODAL UMUM
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const content = document.getElementById(modalId + 'Content');
        
        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // FUNGSI MEMBUKA MODAL EDIT & MENGISI DATA
    function openEditModal(id, name, displayName) {
        // Isi Form
        document.getElementById('edit_role_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_display_name').value = displayName;
        
        // Logika Khusus Tampilan Form berdasarkan Role Name
        const warnBox = document.getElementById('permissionsWarning');
        const checkboxes = document.querySelectorAll('.edit-permission-checkbox');
        
        // Reset (Uncheck semua) terlebih dahulu
        checkboxes.forEach(box => box.checked = false);

        if(name === 'admin') {
            // Admin: Tampilkan peringatan, check semuanya dan kunci (disabled)
            warnBox.classList.remove('hidden');
            warnBox.classList.add('flex');
            
            checkboxes.forEach(box => {
                box.checked = true;
                box.disabled = true; // Admin tidak boleh dikurangi hak aksesnya
            });
        } else {
            // Selain Admin
            warnBox.classList.add('hidden');
            warnBox.classList.remove('flex');
            
            checkboxes.forEach(box => {
                box.disabled = false;
            });
            
            // Simulasi Auto-check standar untuk PIC
            if(name === 'pic') {
                document.querySelector('input[value="view_master"].edit-permission-checkbox').checked = true;
                document.querySelector('input[value="approve_leave"].edit-permission-checkbox').checked = true;
                document.querySelector('input[value="approve_claim"].edit-permission-checkbox').checked = true;
            }
        }

        // Buka Modalnya
        openModal('editRoleModal');
    }

    // Menutup modal jika area gelap di luar modal di-klik
    window.addEventListener('click', function(e) {
        if(e.target === document.getElementById('addRoleModal')) closeModal('addRoleModal');
        if(e.target === document.getElementById('editRoleModal')) closeModal('editRoleModal');
    });
</script>
@endsection