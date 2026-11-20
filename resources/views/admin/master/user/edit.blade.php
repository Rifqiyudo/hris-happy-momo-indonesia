@extends('admin.layouts.master')
@section('title', 'Edit Profil - ' . $user->name)

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 pb-24 animate-fadeIn">
    {{-- Header Page --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">Perbarui Data Karyawan</h1>
            <p class="text-slate-400 dark:text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">ID Karyawan: {{ $user->nik }}</p>
        </div>
        <a href="{{ route('admin.master.user.index') }}" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-500 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition shadow-sm dark:shadow-none flex items-center gap-2 w-fit">
            <i class="fas fa-arrow-left"></i> Kembali ke List
        </a>
    </div>

    {{-- 1. FORM UPDATE PROFIL UTAMA --}}
    <form action="{{ route('admin.master.user.update', $user->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- KOLOM KIRI: AVATAR & AKSES --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Card Foto (Bersih Tanpa Thumbnail Error) --}}
                <div class="bg-white dark:bg-slate-900 p-6 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-none text-center relative overflow-hidden group">
                    <div class="absolute top-0 inset-x-0 h-1.5 bg-indigo-600"></div>
                    <div class="mb-6 mt-4 relative inline-block">
                        {{-- Bingkai Pas Foto 3x4 --}}
                        <label for="avatar-input" class="w-40 h-52 rounded-[2rem] border-2 border-dashed border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/70 hover:border-indigo-500 dark:hover:border-indigo-500 mx-auto shadow-inner flex flex-col items-center justify-center overflow-hidden cursor-pointer transition-all relative">
                            {{-- Placeholder Bersih (Tampil Jika Belum Ada Avatar / Gagal Muat) --}}
                            <div id="photo-placeholder" class="{{ $user->avatar ? 'hidden' : 'flex' }} flex-col items-center justify-center p-4 text-center">
                                <div class="w-12 h-12 rounded-2xl bg-slate-200/70 dark:bg-slate-800 text-slate-400 dark:text-slate-500 flex items-center justify-center mb-2.5 group-hover:text-indigo-500 transition-colors">
                                    <i class="fas fa-user text-xl"></i>
                                </div>
                                <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pilih Foto</span>
                                <span class="text-[8px] font-bold text-slate-400 dark:text-slate-600 uppercase tracking-widest mt-0.5">JPG / PNG</span>
                            </div>

                            {{-- Gambar Avatar / Preview --}}
                            <img id="preview-image"
                                 src="{{ $user->avatar ? asset('storage/' . $user->avatar) : '' }}"
                                 alt="{{ $user->name }}"
                                 onerror="this.classList.add('hidden'); document.getElementById('photo-placeholder').classList.remove('hidden');"
                                 class="{{ $user->avatar ? '' : 'hidden' }} w-full h-full object-cover transition-transform group-hover:scale-105 duration-500">
                        </label>

                        <label for="avatar-input" class="absolute -bottom-2 -right-2 w-10 h-10 bg-indigo-600 text-white rounded-full flex items-center justify-center cursor-pointer border-4 border-white dark:border-slate-900 hover:bg-indigo-700 transition shadow-lg dark:shadow-indigo-950/50">
                            <i class="fas fa-camera text-xs"></i>
                            <input id="avatar-input" type="file" name="avatar" accept="image/*" class="hidden" onchange="previewPhoto(this)">
                        </label>
                    </div>
                    <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tighter">{{ $user->name }}</h3>
                    <p class="text-[9px] text-slate-400 dark:text-slate-500 font-bold mt-1 uppercase tracking-widest leading-relaxed">{{ $user->position->name ?? 'Staff' }}</p>
                </div>

                {{-- Kredensial Login Card --}}
                <div class="bg-slate-900 dark:bg-slate-900 border border-slate-800 p-8 rounded-[2.5rem] shadow-xl dark:shadow-none text-white relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em]">Akses Sistem</h4>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="admin_toggle" name="is_admin" class="sr-only peer" onchange="toggleAdminFields()" {{ $user->role !== 'employee' ? 'checked' : '' }}>
                            <div class="w-10 h-5 bg-slate-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                    <div id="admin_fields" class="space-y-5 {{ $user->role == 'employee' ? 'opacity-40 pointer-events-none' : '' }} transition-all duration-300">
                        <div>
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-wider mb-2">Level Otoritas</label>
                            <select name="role" class="w-full bg-slate-800 dark:bg-slate-950/80 border border-slate-700/80 rounded-xl text-xs font-bold text-white py-3 px-4 focus:ring-2 focus:ring-indigo-500 outline-none cursor-pointer">
                                <option value="employee" {{ old('role', $user->role) == 'employee' ? 'selected' : '' }}>Staff Biasa</option>
                                <option value="pic" {{ old('role', $user->role) == 'pic' ? 'selected' : '' }}>Supervisor / PIC</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-wider mb-2">Email Perusahaan</label>
                            <input type="email" name="email" autocomplete="off" value="{{ old('email', $user->email) }}" class="w-full bg-slate-800 dark:bg-slate-950/80 border border-slate-700/80 rounded-xl text-xs font-bold text-white py-3 px-4 focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-wider mb-2">Ganti Password</label>
                            <input type="password" name="password" autocomplete="new-password" class="w-full bg-slate-800 dark:bg-slate-950/80 border border-slate-700/80 rounded-xl text-xs font-bold text-white placeholder-slate-500 py-3 px-4 focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="••••••••">
                            <p class="text-[7px] text-slate-400 mt-2 uppercase font-bold italic">Kosongkan jika tidak ingin mengubah</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: FORM DETAIL --}}
            <div class="lg:col-span-3 space-y-8">
                {{-- SECTION 1: BIODATA --}}
                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-none overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/60">
                        <h3 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-[0.2em] flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-indigo-600 dark:bg-indigo-500/20 text-white dark:text-indigo-400 dark:border dark:border-indigo-500/30 flex items-center justify-center text-[10px] shadow-lg shadow-indigo-100 dark:shadow-none"><i class="fas fa-user"></i></span>
                            Informasi Personal & Identitas
                        </h3>
                    </div>
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-6">
                        <div class="lg:col-span-3">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nama Lengkap (Sesuai KTP)</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-2xl py-4 px-5 text-sm font-bold focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500 transition-all uppercase outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">ID Karyawan</label>
                            <input type="text" name="nik" value="{{ $user->nik }}" readonly class="w-full border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800/80 rounded-2xl py-4 px-5 text-sm font-black cursor-not-allowed border-dashed text-slate-500 dark:text-slate-400 outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">NPWP</label>
                            <input type="text" name="npwp_no" value="{{ old('npwp_no', $user->npwp_no) }}" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-2xl py-4 px-5 text-sm font-bold focus:bg-white dark:focus:bg-slate-900 transition-all outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">No. WhatsApp</label>
                            <input type="number" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-2xl py-4 px-5 text-sm font-bold focus:bg-white dark:focus:bg-slate-900 transition-all outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tempat & Tanggal Lahir</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="text" name="birth_place" value="{{ old('birth_place', $user->birth_place) }}" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600 rounded-xl py-4 px-4 text-xs font-bold outline-none focus:bg-white dark:focus:bg-slate-900 transition" placeholder="Kota">
                                <input type="date" name="birth_date" value="{{ old('birth_date', $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('Y-m-d') : '') }}" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 dark:[color-scheme:dark] rounded-xl py-4 px-4 text-xs font-bold outline-none focus:bg-white dark:focus:bg-slate-900 transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Jenis Kelamin & Agama</label>
                            <div class="grid grid-cols-2 gap-2">
                                <select name="sex" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-xl py-4 px-4 text-xs font-bold outline-none focus:bg-white dark:focus:bg-slate-900 cursor-pointer transition">
                                    <option value="F" {{ old('sex', $user->sex) == 'F' ? 'selected' : '' }}>Perempuan</option>
                                    <option value="M" {{ old('sex', $user->sex) == 'M' ? 'selected' : '' }}>Laki-Laki</option>
                                </select>
                                <select name="religion" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-xl py-4 px-4 text-xs font-bold outline-none focus:bg-white dark:focus:bg-slate-900 cursor-pointer transition">
                                    @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Khonghucu'] as $rel)
                                        <option value="{{ $rel }}" {{ old('religion', $user->religion) == $rel ? 'selected' : '' }}>{{ $rel }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pendidikan Terakhir</label>
                            <select name="last_education" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-2xl py-4 px-5 text-[11px] font-bold outline-none focus:bg-white dark:focus:bg-slate-900 cursor-pointer transition">
                                <option value="">Pilih Pendidikan</option>
                                @foreach(['SD', 'SMP', 'SMA', 'SMK', 'D3', 'D4', 'S1', 'S2', 'S3'] as $edu)
                                    <option value="{{ $edu }}" {{ old('last_education', $user->last_education) == $edu ? 'selected' : '' }}>{{ $edu }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="lg:col-span-2">
                            <label class="block text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                                <i class="fas fa-graduation-cap"></i> Bidang Jurusan / Keahlian <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="educational_major" value="{{ old('educational_major', $user->educational_major) }}" required 
                                   class="w-full border border-indigo-100 dark:border-indigo-500/30 bg-indigo-50/30 dark:bg-indigo-950/30 text-slate-800 dark:text-indigo-100 placeholder-slate-400 dark:placeholder-slate-500 rounded-2xl py-4 px-5 text-xs font-bold focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500 transition-all uppercase outline-none" 
                                   placeholder="Contoh: Akuntansi, Teknik Mesin, Pemasaran">
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: LEGAL & BANK --}}
                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-none overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/60">
                        <h3 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-[0.2em] flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-amber-500 dark:bg-amber-500/20 text-white dark:text-amber-400 dark:border dark:border-amber-500/30 flex items-center justify-center text-[10px] shadow-lg shadow-amber-100 dark:shadow-none"><i class="fas fa-university"></i></span>
                            Legal & Informasi Perbankan
                        </h3>
                    </div>
                    <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nomor KTP (16 Digit)</label>
                            <div class="relative group">
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 dark:text-slate-600 group-focus-within:text-indigo-500 transition-colors">
                                    <i class="fas fa-id-card-alt"></i>
                                </div>
                                <input type="number" name="ktp_no" value="{{ old('ktp_no', $user->ktp_no) }}" class="w-full pl-11 pr-4 py-4 bg-slate-50 dark:bg-slate-950/70 border border-slate-100 dark:border-slate-800 text-slate-800 dark:text-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 focus:bg-white dark:focus:bg-slate-900 outline-none transition shadow-inner dark:shadow-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Bank Lengkap</label>
                            <select name="bank_name" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-2xl py-4 px-4 text-xs font-bold outline-none focus:ring-4 focus:ring-indigo-500/10 cursor-pointer">
                                <option value="">-- Pilih Bank --</option>
                                <optgroup label="Bank Umum Nasional">
                                    <option value="BCA" {{ old('bank_name', $user->bank_name) == 'BCA' ? 'selected' : '' }}>BCA (Bank Central Asia)</option>
                                    <option value="MANDIRI" {{ old('bank_name', $user->bank_name) == 'MANDIRI' ? 'selected' : '' }}>Bank Mandiri</option>
                                    <option value="BNI" {{ old('bank_name', $user->bank_name) == 'BNI' ? 'selected' : '' }}>BNI (Bank Negara Indonesia)</option>
                                    <option value="BRI" {{ old('bank_name', $user->bank_name) == 'BRI' ? 'selected' : '' }}>BRI (Bank Rakyat Indonesia)</option>
                                    <option value="BTN" {{ old('bank_name', $user->bank_name) == 'BTN' ? 'selected' : '' }}>BTN (Bank Tabungan Negara)</option>
                                </optgroup>
                                <optgroup label="Bank Syariah">
                                    <option value="BSI" {{ old('bank_name', $user->bank_name) == 'BSI' ? 'selected' : '' }}>BSI (Bank Syariah Indonesia)</option>
                                    <option value="MUAMALAT" {{ old('bank_name', $user->bank_name) == 'MUAMALAT' ? 'selected' : '' }}>Bank Muamalat</option>
                                </optgroup>
                                <optgroup label="Bank Digital">
                                    <option value="JAGO" {{ old('bank_name', $user->bank_name) == 'JAGO' ? 'selected' : '' }}>Bank Jago / Artos</option>
                                    <option value="SEABANK" {{ old('bank_name', $user->bank_name) == 'SEABANK' ? 'selected' : '' }}>SeaBank Indonesia</option>
                                    <option value="NEO" {{ old('bank_name', $user->bank_name) == 'NEO' ? 'selected' : '' }}>Bank Neo Commerce</option>
                                </optgroup>
                                <option value="LAINNYA" {{ old('bank_name', $user->bank_name) == 'LAINNYA' ? 'selected' : '' }}>Lainnya...</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nomor Rekening Bank</label>
                            <div class="relative group">
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 dark:text-slate-600 group-focus-within:text-indigo-500 transition-colors">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <input type="number" name="bank_account_no" value="{{ old('bank_account_no', $user->bank_account_no) }}" class="w-full pl-11 pr-4 py-4 bg-slate-50 dark:bg-slate-950/70 border border-slate-100 dark:border-slate-800 text-slate-800 dark:text-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 focus:bg-white dark:focus:bg-slate-900 outline-none transition shadow-inner dark:shadow-none">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: KEPEGAWAIAN --}}
                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-none overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/60">
                        <h3 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-[0.2em] flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-emerald-500 dark:bg-emerald-500/20 text-white dark:text-emerald-400 dark:border dark:border-emerald-500/30 flex items-center justify-center text-[10px] shadow-lg shadow-emerald-100 dark:shadow-none"><i class="fas fa-briefcase"></i></span>
                            Data Kepegawaian & Shift
                        </h3>
                    </div>
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest mb-2 text-rose-500 dark:text-rose-400">Cabang Penempatan</label>
                            <select name="branch_id" required class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 rounded-2xl py-4 px-5 text-xs font-black uppercase text-rose-600 dark:text-rose-400 outline-none cursor-pointer">
                                <option value="">-- Pilih Cabang --</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id', $user->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Departemen</label>
                            <select name="department_id" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-2xl py-4 px-5 text-xs font-bold outline-none cursor-pointer">
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Jabatan Struktur</label>
                            <select name="position_id" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-2xl py-4 px-5 text-xs font-bold outline-none cursor-pointer">
                                @foreach($positions as $pos)
                                    <option value="{{ $pos->id }}" {{ old('position_id', $user->position_id) == $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-2">Jadwal Shift Kerja</label>
                            <select name="shift_id" required class="w-full border border-indigo-100 dark:border-indigo-500/30 bg-indigo-50/30 dark:bg-indigo-950/30 rounded-2xl py-4 px-5 text-xs font-black text-indigo-700 dark:text-indigo-300 outline-none cursor-pointer">
                                @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}" {{ old('shift_id', $user->shift_id) == $shift->id ? 'selected' : '' }}>
                                        {{ $shift->name }} ({{ substr($shift->start_time, 0, 5) }} - {{ substr($shift->end_time, 0, 5) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tanggal Bergabung</label>
                            <input type="date" name="join_date" value="{{ old('join_date', $user->join_date ? \Carbon\Carbon::parse($user->join_date)->format('Y-m-d') : '') }}" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 dark:[color-scheme:dark] rounded-2xl py-4 px-5 text-xs font-bold outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Status Karyawan</label>
                            <select name="employee_status" id="employee_status" onchange="toggleContractPeriod()" class="w-full border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-950/70 rounded-2xl py-4 px-5 text-xs font-black uppercase text-indigo-600 dark:text-indigo-400 shadow-sm dark:shadow-none outline-none cursor-pointer">
                                <option value="Permanent" {{ old('employee_status', $user->employee_status) == 'Permanent' ? 'selected' : '' }}>Tetap (Permanent)</option>
                                <option value="Contract" {{ old('employee_status', $user->employee_status) == 'Contract' ? 'selected' : '' }}>Kontrak (Contract)</option>
                                <option value="Probation" {{ old('employee_status', $user->employee_status) == 'Probation' ? 'selected' : '' }}>Masa Percobaan (Probation)</option>
                            </select>
                        </div>
                        <div id="contract_duration_container" class="{{ old('employee_status', $user->employee_status) == 'Contract' ? '' : 'hidden' }}">
                            <label class="block text-[10px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest mb-2">Durasi Kontrak (Bulan)</label>
                            <select name="contract_duration" class="w-full border border-slate-100 dark:border-amber-500/30 bg-amber-50 dark:bg-amber-950/40 rounded-2xl py-4 px-5 text-xs font-bold text-amber-700 dark:text-amber-300 outline-none cursor-pointer">
                                <option value="">Pilih Durasi</option>
                                @for ($i = 1; $i <= 24; $i++)
                                    <option value="{{ $i }}" {{ old('contract_duration', $user->contract_duration) == $i ? 'selected' : '' }}>
                                        {{ $i }} Bulan {{ $i >= 12 ? '('. floor($i/12) .' Thn '. ($i%12) .' Bln)' : '' }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Denda Telat (Rp)</label>
                            <input type="number" name="late_deduction" value="{{ old('late_deduction', $user->late_deduction ?? 5000) }}" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 rounded-2xl py-4 px-5 text-sm font-black text-rose-600 dark:text-rose-400 outline-none">
                        </div>
                    </div>
                </div>

                {{-- SECTION BARU: MANAJEMEN ASET KARYAWAN (INTEGRASI DROPDOWN MASTER) --}}
                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-none overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/60 flex justify-between items-center">
                        <h3 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-[0.2em] flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-indigo-500 dark:bg-indigo-500/20 text-white dark:text-indigo-400 dark:border dark:border-indigo-500/30 flex items-center justify-center text-[10px] shadow-lg shadow-indigo-100 dark:shadow-none"><i class="fas fa-laptop"></i></span>
                            Manajemen Aset & Inventaris
                        </h3>
                    </div>
                    <div class="p-8">
                        @php 
                            // Mengambil aset yang saat ini dipegang oleh user menggunakan optional (aman jika kosong)
                            $currentAsset = optional($user->masterAssets)->first(); 
                        @endphp

                        {{-- Tampilan jika karyawan sedang memegang aset --}}
                        @if($currentAsset)
                            <div class="bg-indigo-50 dark:bg-indigo-950/30 rounded-3xl p-6 border border-indigo-100 dark:border-indigo-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white dark:bg-slate-900 rounded-xl shadow-sm dark:shadow-none border border-transparent dark:border-slate-800 text-indigo-500 dark:text-indigo-400 flex items-center justify-center text-xl">
                                        <i class="fas fa-laptop"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-0.5">Aset Saat Ini (Ditugaskan)</p>
                                        <h4 class="text-sm font-black text-indigo-900 dark:text-indigo-200 uppercase tracking-tight">{{ $currentAsset->name }}</h4>
                                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1">SN: {{ $currentAsset->serial_number ?? 'Tidak ada' }}</p>
                                    </div>
                                </div>
                                <div class="sm:text-right">
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Diserahkan pada:</p>
                                    <p class="text-xs font-black text-slate-700 dark:text-slate-200 mt-0.5">{{ $currentAsset->assigned_date ? \Carbon\Carbon::parse($currentAsset->assigned_date)->translatedFormat('d M Y') : 'Belum tercatat' }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">
                                    {{ $currentAsset ? 'Ganti Aset / Alokasikan Aset Lain' : 'Pilih Aset Perusahaan (Ready Stock)' }}
                                </label>
                                <select name="master_asset_id" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-950/70 border border-slate-100 dark:border-slate-800 rounded-2xl text-sm font-bold text-slate-700 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 transition-all outline-none cursor-pointer">
                                    <option value="">-- Tidak Ada / Kosongkan --</option>
                                    @foreach($availableAssets ?? [] as $asset)
                                        <option value="{{ $asset->id }}">
                                            {{ $asset->name }} (SN: {{ $asset->serial_number ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="bg-amber-50 dark:bg-amber-950/30 p-4 rounded-2xl border border-amber-100 dark:border-amber-500/30">
                                <p class="text-[10px] text-amber-700 dark:text-amber-300 font-bold leading-relaxed italic">
                                    <i class="fas fa-info-circle mr-1"></i> Data aset terhubung langsung dengan menu <b>Manajemen Aset</b>. Jika Anda memilih aset baru, otomatis statusnya akan berubah menjadi <b>Assigned</b> ke karyawan ini.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SUBMIT BUTTON --}}
                <div class="flex flex-col md:flex-row items-center justify-between mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 gap-4">
                    <div class="flex items-center gap-6">
                        <label class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Status Keaktifan :</label>
                        <select name="is_active" class="bg-slate-100 dark:bg-slate-800 border border-transparent dark:border-slate-700 rounded-full px-8 py-3 text-xs font-black uppercase text-slate-600 dark:text-slate-200 focus:ring-indigo-500 transition-all outline-none cursor-pointer">
                            <option value="1" {{ old('is_active', $user->is_active) == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active', $user->is_active) == '0' ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full md:w-auto bg-gradient-to-r from-blue-800 to-blue-600 text-white font-black px-14 py-5 rounded-[2rem] shadow-2xl shadow-blue-200 dark:shadow-blue-950/50 hover:scale-105 transition-all uppercase text-[11px] tracking-[0.3em] active:scale-95">
                        Update Profil Karyawan
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- SECTION DOCUMENT CENTER --}}
    <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-xl dark:shadow-none overflow-hidden mt-12">
        <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/60 flex items-center justify-between">
            <h3 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-blue-600 dark:bg-blue-500/20 text-white dark:text-blue-400 dark:border dark:border-blue-500/30 flex items-center justify-center text-[10px] shadow-lg shadow-blue-100 dark:shadow-none"><i class="fas fa-folder-open"></i></span>
                Arsip Dokumen Digital
            </h3>
            <button type="button" onclick="openUploadModal()" class="bg-blue-600 text-white px-5 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-blue-700 transition shadow-md dark:shadow-none active:scale-95">+ Unggah Berkas</button>
        </div>
        <div class="p-8">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                @forelse($user->documents ?? [] as $doc)
                    <div class="group relative p-4 rounded-3xl border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-950/50 text-center hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all duration-300">
                        <div class="mb-2"><i class="fas fa-file-pdf text-4xl text-rose-500 group-hover:scale-110 transition-transform"></i></div>
                        <p class="text-[10px] font-black text-slate-700 dark:text-slate-200 truncate px-2 uppercase tracking-tighter">{{ $doc->type }}</p>
                        <p class="text-[8px] text-slate-400 dark:text-slate-500 font-bold truncate mt-1">{{ $doc->file_name }}</p>
                        <div class="absolute inset-0 bg-slate-900/80 rounded-3xl opacity-0 group-hover:opacity-100 flex items-center justify-center gap-2 transition-opacity duration-300">
                            <a href="{{ route('admin.documents.download', $doc->id) }}" class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-blue-600 hover:scale-110 transition shadow-lg"><i class="fas fa-download text-xs"></i></a>
                            <form action="{{ route('admin.documents.destroy', $doc->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 bg-rose-500 rounded-full flex items-center justify-center text-white hover:scale-110 transition shadow-lg" onclick="return confirm('Hapus dokumen?')"><i class="fas fa-trash-alt text-xs"></i></button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 flex flex-col items-center justify-center text-slate-300 dark:text-slate-600 border-2 border-dashed border-slate-100 dark:border-slate-800 rounded-[2rem]">
                        <i class="fas fa-cloud-upload-alt text-4xl mb-3 opacity-30"></i>
                        <p class="text-[10px] font-black uppercase tracking-widest">Belum ada berkas digital</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- MODAL UPLOAD DOKUMEN --}}
<div id="uploadModal" class="fixed inset-0 z-[9999] hidden">
    <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="closeUploadModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <form action="{{ route('admin.documents.store', $user->id) }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 w-full max-w-md rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden">
            @csrf
            <h3 class="text-lg font-black text-slate-800 dark:text-white tracking-tight mb-6 flex items-center gap-2"><i class="fas fa-file-upload text-blue-600 dark:text-blue-400"></i> Unggah Berkas Baru</h3>
            <div class="space-y-5">
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Kategori Dokumen</label>
                    <select name="type" required class="w-full bg-slate-50 dark:bg-slate-950/80 border border-slate-100 dark:border-slate-800 rounded-xl py-3.5 px-4 text-xs font-bold text-slate-700 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer">
                        <option value="KTP">Kartu Tanda Penduduk (KTP)</option>
                        <option value="NPWP">NPWP</option>
                        <option value="KK">Kartu Keluarga (KK)</option>
                        <option value="KONTRAK">Kontrak Kerja</option>
                        <option value="IJAZAH">Ijazah</option>
                        <option value="SK">Surat Keputusan (SK)</option>
                    </select>
                </div>
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Pilih Berkas (PDF/JPG/PNG)</label>
                    <input type="file" name="file" required class="w-full text-xs font-bold text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-blue-50 dark:file:bg-blue-500/20 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-blue-500/30 transition-all outline-none cursor-pointer">
                </div>
            </div>
            <div class="mt-8 flex gap-3">
                <button type="button" onclick="closeUploadModal()" class="flex-1 py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition active:scale-95">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-blue-100 dark:shadow-none hover:bg-blue-700 transition active:scale-95">Simpan Berkas</button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Mencegah browser autofill merusak background gelap pada Email & Password */
    html.dark input:-webkit-autofill,
    html.dark input:-webkit-autofill:hover,
    html.dark input:-webkit-autofill:focus,
    .dark input:-webkit-autofill,
    .dark input:-webkit-autofill:hover,
    .dark input:-webkit-autofill:focus {
        -webkit-text-fill-color: #f8fafc !important;
        -webkit-box-shadow: 0 0 0px 1000px #090d16 inset !important;
        transition: background-color 5000s ease-in-out 0s;
    }
</style>

<script>
    function toggleContractPeriod() {
        const status = document.getElementById('employee_status').value;
        const container = document.getElementById('contract_duration_container');
        if (status === 'Contract') {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    }

    function toggleAdminFields() {
        const toggle = document.getElementById('admin_toggle');
        const fields = document.getElementById('admin_fields');
        if (toggle.checked) {
            fields.classList.remove('opacity-40', 'pointer-events-none');
        } else {
            fields.classList.add('opacity-40', 'pointer-events-none');
        }
    }

    function previewPhoto(input) {
        const preview = document.getElementById('preview-image');
        const placeholder = document.getElementById('photo-placeholder');

        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function openUploadModal() {
        document.getElementById('uploadModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeUploadModal() {
        document.getElementById('uploadModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Initialize state on load
    document.addEventListener('DOMContentLoaded', function() {
        toggleContractPeriod();
        toggleAdminFields();
    });
</script>
@endsection