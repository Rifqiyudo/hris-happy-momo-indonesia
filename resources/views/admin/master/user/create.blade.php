@extends('admin.layouts.master')
@section('title', 'Registrasi Karyawan - Happy Momo')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 pb-24">
    {{-- Header Page --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">Tambah Karyawan Baru</h1>
            <p class="text-slate-400 dark:text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Sistem Informasi SDM PT. Happy Momo Indonesia</p>
        </div>
        <a href="{{ route('admin.master.user.index') }}" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-500 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition shadow-sm dark:shadow-none">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke List
        </a>
    </div>

    <form action="{{ route('admin.master.user.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- KOLOM KIRI: AVATAR & AKUN --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Card Foto (Bersih Tanpa Broken Thumbnail) --}}
                <div class="bg-white dark:bg-slate-900 p-6 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-none text-center relative overflow-hidden group">
                    <div class="absolute top-0 inset-x-0 h-1.5 bg-indigo-600"></div>
                    <div class="mb-6 mt-4 relative inline-block">
                        {{-- Bingkai Pas Foto 3x4 --}}
                        <label for="avatar-input" class="w-40 h-52 rounded-[2rem] border-2 border-dashed border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/70 hover:border-indigo-500 dark:hover:border-indigo-500 mx-auto shadow-inner flex flex-col items-center justify-center overflow-hidden cursor-pointer transition-all relative">
                            {{-- Placeholder Normal (Tampil Sebelum Foto Dipilih) --}}
                            <div id="photo-placeholder" class="flex flex-col items-center justify-center p-4 text-center">
                                <div class="w-12 h-12 rounded-2xl bg-slate-200/70 dark:bg-slate-800 text-slate-400 dark:text-slate-500 flex items-center justify-center mb-2.5 group-hover:text-indigo-500 transition-colors">
                                    <i class="fas fa-user text-xl"></i>
                                </div>
                                <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pilih Foto</span>
                                <span class="text-[8px] font-bold text-slate-400 dark:text-slate-600 uppercase tracking-widest mt-0.5">JPG / PNG</span>
                            </div>

                            {{-- Gambar Preview (Disembunyikan Sampai Ada File Dipilih) --}}
                            <img id="preview-image" src="" alt="Preview Pas Foto"
                                 class="hidden w-full h-full object-cover transition-transform group-hover:scale-105 duration-500">
                        </label>

                        <label for="avatar-input" class="absolute -bottom-2 -right-2 w-10 h-10 bg-indigo-600 text-white rounded-full flex items-center justify-center cursor-pointer border-4 border-white dark:border-slate-900 hover:bg-indigo-700 transition shadow-lg dark:shadow-indigo-950/50">
                            <i class="fas fa-camera text-xs"></i>
                            <input id="avatar-input" type="file" name="avatar" accept="image/*" class="hidden" onchange="previewPhoto(this)">
                        </label>
                    </div>
                    <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tighter">Pas Foto 3x4</h3>
                    <p class="text-[9px] text-slate-400 dark:text-slate-500 font-bold mt-1 uppercase tracking-widest leading-relaxed">Resize Image ( 205 x 273 )</p>
                </div>

                {{-- Card Kredensial Login --}}
                <div class="bg-slate-900 dark:bg-slate-900 border border-slate-800 p-8 rounded-[2.5rem] shadow-xl dark:shadow-none text-white relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>
                    
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em]">Akses Sistem</h4>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="admin_toggle" name="is_admin" class="sr-only peer" onchange="toggleAdminFields()">
                            <div class="w-10 h-5 bg-slate-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>

                    <div id="admin_fields" class="space-y-5 opacity-40 pointer-events-none transition-all duration-300">
                        <div>
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-wider mb-2">Level Otoritas</label>
                            <select name="role" class="w-full bg-slate-800 dark:bg-slate-950/80 border border-slate-700/80 rounded-xl text-xs font-bold text-white py-3 px-4 outline-none focus:ring-1 focus:ring-indigo-500">
                                <option value="employee">Staff Biasa</option>
                                <option value="pic">Supervisor / PIC</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-wider mb-2">Email Perusahaan</label>
                            <input type="email" name="email" autocomplete="off" class="w-full bg-slate-800 dark:bg-slate-950/80 border border-slate-700/80 rounded-xl text-xs font-bold text-white placeholder-slate-500 py-3 px-4 outline-none focus:ring-1 focus:ring-indigo-500" placeholder="user@happymomo.id">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-wider mb-2">Password</label>
                            <input type="password" name="password" autocomplete="new-password" class="w-full bg-slate-800 dark:bg-slate-950/80 border border-slate-700/80 rounded-xl text-xs font-bold text-white placeholder-slate-500 py-3 px-4 outline-none focus:ring-1 focus:ring-indigo-500" placeholder="••••••••">
                        </div>
                    </div>
                    <p id="toggle_note" class="text-[8px] text-slate-400 font-bold mt-5 italic uppercase text-center tracking-widest">Aktifkan toggle untuk akses login</p>
                </div>
            </div>

            {{-- KOLOM KANAN: FORM DETAIL --}}
            <div class="lg:col-span-3 space-y-8">
                
                {{-- SECTION 1: BIODATA & IDENTITAS --}}
                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-none">
                    <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-900/60 rounded-t-[2.5rem]">
                        <h3 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-[0.2em] flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-indigo-600 dark:bg-indigo-500/20 text-white dark:text-indigo-400 dark:border dark:border-indigo-500/30 flex items-center justify-center text-[10px] shadow-lg shadow-indigo-100 dark:shadow-none"><i class="fas fa-user"></i></span>
                            Informasi Personal & Identitas
                        </h3>
                    </div>
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-6">
                        <div class="lg:col-span-3">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nama Lengkap (Sesuai KTP) <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" required class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600 rounded-2xl py-4 px-5 text-sm font-bold focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500 transition-all uppercase outline-none" placeholder="Masukkan Nama Lengkap">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">ID Karyawan <span class="text-rose-500">*</span></label>
                            <input type="text" name="nik" required class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600 rounded-2xl py-4 px-5 text-sm font-black focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500 transition-all outline-none" placeholder="1812xxx">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nomor KTP (16 Digit)</label>
                            <input type="number" name="ktp_no" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-2xl py-4 px-5 text-sm font-bold focus:bg-white dark:focus:bg-slate-900 transition-all outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">NPWP (Optional)</label>
                            <input type="text" name="npwp_no" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-2xl py-4 px-5 text-sm font-bold focus:bg-white dark:focus:bg-slate-900 transition-all outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tempat Lahir</label>
                            <input type="text" name="birth_place" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-2xl py-4 px-5 text-xs font-bold focus:bg-white dark:focus:bg-slate-900 transition-all outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tanggal Lahir</label>
                            <input type="date" name="birth_date" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 dark:[color-scheme:dark] rounded-2xl py-4 px-5 text-xs font-bold focus:bg-white dark:focus:bg-slate-900 transition-all outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Jenis Kelamin</label>
                            <select name="sex" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-2xl py-4 px-5 text-xs font-bold outline-none">
                                <option value="F">Perempuan</option>
                                <option value="M">Laki-Laki</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Agama</label>
                            <select name="religion" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-2xl py-4 px-5 text-xs font-bold outline-none">
                                <option>Islam</option><option>Kristen</option><option>Katolik</option><option>Hindu</option><option>Budha</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pendidikan Terakhir</label>
                            <select name="last_education" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-2xl py-4 px-5 text-[11px] font-bold outline-none">
                                <option value="SMA">SMA / SMK Sederajat</option>
                                <option value="D3">Diploma III (D3)</option>
                                <option value="D4">Sarjana Terapan (D4)</option>
                                <option value="S1">Sarjana (S1)</option>
                                <option value="S2">Magister (S2)</option>
                                <option value="S3">Doktor (S3)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                                <i class="fas fa-graduation-cap"></i> Bidang Jurusan <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="educational_major" required 
                                   class="w-full border border-indigo-100 dark:border-indigo-500/30 bg-indigo-50/30 dark:bg-indigo-950/30 text-slate-800 dark:text-indigo-100 placeholder-slate-400 dark:placeholder-slate-500 rounded-2xl py-4 px-5 text-xs font-bold focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500 transition-all uppercase outline-none" 
                                   placeholder="Contoh: Akuntansi / Teknik Mesin">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Status Pernikahan</label>
                            <select name="marital_status" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-2xl py-4 px-5 text-xs font-bold outline-none">
                                <option value="Single">Lajang (S)</option>
                                <option value="Married">Menikah (M)</option>
                                <option value="Divorced">Cerai</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Jumlah Anak</label>
                            <input type="number" name="children_count" value="0" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-2xl py-4 px-5 text-xs font-bold focus:bg-white dark:focus:bg-slate-900 transition-all outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">No. WhatsApp</label>
                            <input type="number" name="phone" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-2xl py-4 px-5 text-sm font-bold focus:bg-white dark:focus:bg-slate-900 transition-all outline-none">
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: PEKERJAAN & JABATAN --}}
                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-none">
                    <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-900/60 rounded-t-[2.5rem]">
                        <h3 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-[0.2em] flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-emerald-500 dark:bg-emerald-500/20 text-white dark:text-emerald-400 dark:border dark:border-emerald-500/30 flex items-center justify-center text-[10px] shadow-lg shadow-emerald-100 dark:shadow-none"><i class="fas fa-briefcase"></i></span>
                            Data Kepegawaian & Penempatan
                        </h3>
                    </div>
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="lg:col-span-1">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Cabang Penempatan <span class="text-rose-500">*</span></label>
                            <select name="branch_id" required class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-2xl py-4 px-5 text-xs font-bold outline-none">
                                <option value="">-- Pilih Cabang --</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Departemen</label>
                            <select name="department_id" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-2xl py-4 px-5 text-xs font-bold outline-none">
                                <option value="">-- Pilih Departemen --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Jabatan</label>
                            <select name="position_id" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-2xl py-4 px-5 text-xs font-bold outline-none">
                                <option value="">-- Pilih Jabatan --</option>
                                @foreach($positions as $pos)
                                    <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Jadwal Shift <span class="text-rose-500">*</span></label>
                            <select name="shift_id" required class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 rounded-2xl py-4 px-5 text-xs font-black text-indigo-600 dark:text-indigo-400 outline-none">
                                <option value="">-- Pilih Shift --</option>
                                @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}">{{ $shift->name }} ({{ substr($shift->start_time, 0, 5) }} - {{ substr($shift->end_time, 0, 5) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tanggal Bergabung <span class="text-rose-500">*</span></label>
                            <input type="date" name="join_date" required class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 dark:[color-scheme:dark] rounded-2xl py-4 px-5 text-xs font-bold outline-none">
                        </div>
                        
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Status Karyawan <span class="text-rose-500">*</span></label>
                            <select name="employee_status" id="employee_status" required onchange="toggleContractPeriod()" class="w-full border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-950/70 rounded-2xl py-4 px-5 text-xs font-black uppercase text-indigo-600 dark:text-indigo-400 shadow-sm dark:shadow-none outline-none">
                                <option value="Permanent">Tetap (Permanent)</option>
                                <option value="Contract">Kontrak (Contract)</option>
                                <option value="Probation">Masa Percobaan (Probation)</option>
                            </select>
                        </div>

                        <div id="contract_duration_container" class="hidden">
                            <label class="block text-[10px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest mb-2">Durasi Kontrak (1-24 Bln)</label>
                            <select name="contract_duration" class="w-full border border-slate-100 dark:border-amber-500/30 bg-amber-50 dark:bg-amber-950/40 rounded-2xl py-4 px-5 text-xs font-bold text-amber-700 dark:text-amber-300 outline-none">
                                <option value="">-- Pilih Durasi --</option>
                                @for ($i = 1; $i <= 24; $i++)
                                    <option value="{{ $i }}">{{ $i }} Bulan {{ $i >= 12 ? '('. floor($i/12) .' Thn '. ($i%12) .' Bln)' : '' }}</option>
                                @endfor
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Denda Telat (Rp)</label>
                            <input type="number" name="late_deduction" value="5000" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 rounded-2xl py-4 px-5 text-sm font-black text-rose-600 dark:text-rose-400 outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Toleransi (Menit)</label>
                            <input type="number" name="late_tolerance" value="15" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 rounded-2xl py-4 px-5 text-sm font-black text-amber-600 dark:text-amber-400 outline-none">
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: DATA KELUARGA --}}
                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-none">
                    <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-900/60 rounded-t-[2.5rem]">
                        <h3 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-[0.2em] flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-rose-500 dark:bg-rose-500/20 text-white dark:text-rose-400 dark:border dark:border-rose-500/30 flex items-center justify-center text-[10px] shadow-lg shadow-rose-100 dark:shadow-none"><i class="fas fa-users"></i></span>
                            Data Keluarga & Kontak Darurat
                        </h3>
                    </div>
                    <div class="p-8 space-y-8">
                        <div>
                             <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                <i class="fas fa-user-friends text-rose-500"></i> Nama Pasangan / Orang Tua
                            </label>
                            <input type="text" name="family_name" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-xl py-3.5 px-5 text-xs font-bold focus:bg-white dark:focus:bg-slate-900 transition-all uppercase outline-none">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                    <i class="fas fa-phone-alt text-emerald-500"></i> Kontak Darurat (Nama & Hubungan)
                                </label>
                                <textarea name="emergency_contact_name" rows="2" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600 rounded-2xl py-4 px-5 text-xs font-bold focus:bg-white dark:focus:bg-slate-900 transition-all outline-none" placeholder="Contoh: Ibu Kandung - Siti Aminah"></textarea>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                    <i class="fas fa-mobile-alt text-emerald-500"></i> Nomor Kontak Darurat
                                </label>
                                <textarea name="emergency_contact_phone" rows="2" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600 rounded-2xl py-4 px-5 text-xs font-bold focus:bg-white dark:focus:bg-slate-900 transition-all outline-none" placeholder="Contoh: 08123xxxx"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 4: PAYROLL & BPJS --}}
                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-none overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-900/60 rounded-t-[2.5rem]">
                        <h3 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-[0.2em] flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-amber-500 dark:bg-amber-500/20 text-white dark:text-amber-400 dark:border dark:border-amber-500/30 flex items-center justify-center text-[10px] shadow-lg shadow-amber-100 dark:shadow-none"><i class="fas fa-university"></i></span>
                            Informasi Finansial & BPJS
                        </h3>
                    </div>
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <h4 class="text-[10px] font-black text-slate-800 dark:text-slate-200 uppercase border-l-4 border-amber-500 pl-3 tracking-widest">Rekening Bank</h4>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-[9px] font-black text-slate-400 uppercase mb-2">Nama Bank Lengkap</label>
                                    <select name="bank_name" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-xl py-3 px-4 text-xs font-bold outline-none focus:ring-1 focus:ring-amber-500">
                                        <option value="">-- Pilih Bank --</option>
                                        <optgroup label="Bank Umum Nasional">
                                            <option value="BCA">BCA (Bank Central Asia)</option>
                                            <option value="MANDIRI">Bank Mandiri</option>
                                            <option value="BNI">BNI (Bank Negara Indonesia)</option>
                                            <option value="BRI">BRI (Bank Rakyat Indonesia)</option>
                                            <option value="BTN">BTN (Bank Tabungan Negara)</option>
                                            <option value="BSI">BSI (Bank Syariah Indonesia)</option>
                                        </optgroup>
                                        <optgroup label="Bank Digital">
                                            <option value="JAGO">Bank Jago / Artos</option>
                                            <option value="SEABANK">SeaBank Indonesia</option>
                                            <option value="NEO">Bank Neo Commerce</option>
                                        </optgroup>
                                        <option value="LAINNYA">Lainnya...</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-slate-400 uppercase mb-2">No. Rekening</label>
                                    <input type="number" name="bank_account_no" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600 rounded-xl py-3 px-4 text-xs font-black tracking-widest outline-none" placeholder="8830xxxx">
                                </div>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <h4 class="text-[10px] font-black text-slate-800 dark:text-slate-200 uppercase border-l-4 border-indigo-500 pl-3 tracking-widest">Jaminan Sosial (BPJS)</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[9px] font-black text-slate-400 uppercase mb-2">No. BPJS Kes</label>
                                    <input type="number" name="bpjs_kesehatan_no" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-xl py-3 px-4 text-[10px] font-bold outline-none">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-slate-400 uppercase mb-2">No. BPJS TK</label>
                                    <input type="number" name="bpjs_ketenagakerjaan_no" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-xl py-3 px-4 text-[10px] font-bold outline-none">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 5: ALAMAT --}}
                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-none p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-rose-500"></i> Alamat Sesuai KTP
                        </label>
                        <textarea name="address" rows="3" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-2xl py-4 px-5 text-xs font-bold focus:bg-white dark:focus:bg-slate-900 transition-all placeholder:text-slate-300 dark:placeholder:text-slate-600 uppercase outline-none" placeholder="Masukkan alamat lengkap..."></textarea>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <i class="fas fa-home text-indigo-500"></i> Alamat Domisili Sekarang
                        </label>
                        <textarea name="domicile" rows="3" class="w-full border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 text-slate-800 dark:text-slate-100 rounded-2xl py-4 px-5 text-xs font-bold focus:bg-white dark:focus:bg-slate-900 transition-all placeholder:text-slate-300 dark:placeholder:text-slate-600 uppercase outline-none" placeholder="Masukkan alamat domisili..."></textarea>
                    </div>
                </div>

                {{-- SECTION BARU: MANAJEMEN ASET KARYAWAN --}}
                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-none overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-900/60 rounded-t-[2.5rem]">
                        <h3 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-[0.2em] flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-indigo-500 dark:bg-indigo-500/20 text-white dark:text-indigo-400 dark:border dark:border-indigo-500/30 flex items-center justify-center text-[10px] shadow-lg shadow-indigo-100 dark:shadow-none"><i class="fas fa-laptop"></i></span>
                            Manajemen Aset & Inventaris
                        </h3>
                    </div>
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Aset / Inventaris</label>
                            <input type="text" name="asset_name" placeholder="Contoh: Laptop Macbook Pro" 
                                   class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-950/70 border border-slate-100 dark:border-slate-800 rounded-2xl text-sm font-bold text-slate-700 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500 transition-all outline-none">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Serial Number / Kode Aset</label>
                            <input type="text" name="asset_serial_number" placeholder="Contoh: SN-2026-XXXX" 
                                   class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-950/70 border border-slate-100 dark:border-slate-800 rounded-2xl text-sm font-bold text-slate-700 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500 transition-all outline-none">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kategori Aset</label>
                            <select name="asset_category" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-950/70 border border-slate-100 dark:border-slate-800 rounded-2xl text-sm font-bold text-slate-700 dark:text-slate-100 outline-none">
                                <option value="electronics">Electronics</option>
                                <option value="vehicle">Vehicle</option>
                                <option value="furniture">Furniture</option>
                                <option value="others">Others</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Harga Beli Aset (Rp)</label>
                            <input type="number" name="asset_purchase_price" placeholder="0" 
                                   class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-950/70 border border-slate-100 dark:border-slate-800 rounded-2xl text-sm font-bold text-slate-700 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500 transition-all outline-none">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Tanggal Penyerahan</label>
                            <input type="date" name="asset_handover_date" 
                                   class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-950/70 border border-slate-100 dark:border-slate-800 rounded-2xl text-sm font-bold text-slate-700 dark:text-slate-100 dark:[color-scheme:dark] focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500 transition-all outline-none">
                        </div>
                    </div>
                </div>

                {{-- FOOTER: ACTION --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-6">
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-bold italic">Periksa kembali NIK dan Email sebelum menyimpan.</p>
                    <div class="flex items-center gap-4">
                        <button type="reset" onclick="resetPhotoPreview()" class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest hover:text-slate-600 dark:hover:text-slate-300 transition">Reset Form</button>
                        <button type="submit" class="bg-slate-900 dark:bg-indigo-600 text-white px-10 py-5 rounded-[2rem] font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-slate-200 dark:shadow-indigo-950/50 hover:bg-black dark:hover:bg-indigo-500 transform hover:-translate-y-1 transition-all">
                            Simpan Data Karyawan <i class="fas fa-save ml-2 text-indigo-400 dark:text-white"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
        const note = document.getElementById('toggle_note');
        if (toggle.checked) {
            fields.classList.remove('opacity-40', 'pointer-events-none');
            fields.classList.add('opacity-100');
            note.innerText = "Akses login aktif";
            note.classList.replace('text-slate-400', 'text-indigo-400');
        } else {
            fields.classList.add('opacity-40', 'pointer-events-none');
            fields.classList.remove('opacity-100');
            note.innerText = "Aktifkan toggle untuk akses login";
            note.classList.replace('text-indigo-400', 'text-slate-400');
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

    function resetPhotoPreview() {
        const preview = document.getElementById('preview-image');
        const placeholder = document.getElementById('photo-placeholder');
        if (preview) {
            preview.src = '';
            preview.classList.add('hidden');
        }
        if (placeholder) {
            placeholder.classList.remove('hidden');
        }
    }
</script>
@endsection