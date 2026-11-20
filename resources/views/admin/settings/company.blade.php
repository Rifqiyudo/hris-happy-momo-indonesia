@extends('admin.layouts.master')
@section('title', 'Konfigurasi Perusahaan')

@section('content')
<div class="space-y-6 animate-fadeIn">
    
    {{-- FLASH MESSAGE (SUCCESS) --}}
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 px-6 py-4 rounded-2xl flex items-center gap-3 shadow-sm">
        <i class="fas fa-check-circle text-xl"></i>
        <div>
            <h4 class="font-bold text-sm">Berhasil!</h4>
            <p class="text-xs">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    {{-- FLASH MESSAGE (ERROR) --}}
    @if(session('error'))
    <div class="bg-rose-50 border border-rose-200 text-rose-600 px-6 py-4 rounded-2xl flex items-center gap-3 shadow-sm">
        <i class="fas fa-exclamation-triangle text-xl"></i>
        <div>
            <h4 class="font-bold text-sm">Gagal!</h4>
            <p class="text-xs">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    {{-- HEADER --}}
    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="font-black text-slate-800 text-xl tracking-tight">Konfigurasi Perusahaan</h2>
            <p class="text-xs text-slate-500 mt-1">Kelola identitas perusahaan, informasi legal, zona waktu, dan jam operasional standar.</p>
        </div>
    </div>

    {{-- FORM KONFIGURASI (Hanya menggunakan POST, tanpa PUT) --}}
    <form action="{{ route('admin.settings.company.update') ?? '#' }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- KOLOM KIRI: Identitas & Kontak --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- IDENTITAS DASAR --}}
                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                    <h3 class="font-black text-slate-800 text-base uppercase tracking-widest mb-6 flex items-center gap-2">
                        <i class="fas fa-building text-indigo-500"></i> Identitas & Kontak
                    </h3>
                    
                    <div class="space-y-5">
                        {{-- Nama Perusahaan --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nama Perusahaan <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <i class="fas fa-signature absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                                <input type="text" name="company_name" value="{{ old('company_name', $settings['company_name'] ?? '') }}" required class="w-full pl-11 pr-4 py-3 bg-slate-50 border @error('company_name') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all" placeholder="PT. Nama Perusahaan Anda">
                            </div>
                            @error('company_name') <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {{-- Email Perusahaan --}}
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Email Resmi <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                                    <input type="email" name="company_email" value="{{ old('company_email', $settings['company_email'] ?? '') }}" required class="w-full pl-11 pr-4 py-3 bg-slate-50 border @error('company_email') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all" placeholder="info@perusahaan.co.id">
                                </div>
                                @error('company_email') <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Telepon --}}
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nomor Telepon</label>
                                <div class="relative">
                                    <i class="fas fa-phone-alt absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                                    <input type="tel" name="company_phone" value="{{ old('company_phone', $settings['company_phone'] ?? '') }}" class="w-full pl-11 pr-4 py-3 bg-slate-50 border @error('company_phone') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all" placeholder="(031) 1234567">
                                </div>
                                @error('company_phone') <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p> @enderror
                            </div>
                            
                            {{-- Website --}}
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Website</label>
                                <div class="relative">
                                    <i class="fas fa-globe absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                                    <input type="url" name="company_website" value="{{ old('company_website', $settings['company_website'] ?? '') }}" class="w-full pl-11 pr-4 py-3 bg-slate-50 border @error('company_website') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all" placeholder="https://www.perusahaan.com">
                                </div>
                                @error('company_website') <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- NPWP / Legalitas --}}
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">NPWP / NIB</label>
                                <div class="relative">
                                    <i class="fas fa-file-invoice absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                                    <input type="text" name="company_npwp" value="{{ old('company_npwp', $settings['company_npwp'] ?? '') }}" class="w-full pl-11 pr-4 py-3 bg-slate-50 border @error('company_npwp') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all" placeholder="00.000.000.0-000.000">
                                </div>
                                @error('company_npwp') <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Alamat --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Alamat Lengkap</label>
                            <textarea name="company_address" rows="3" class="w-full p-4 bg-slate-50 border @error('company_address') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all resize-none" placeholder="Masukkan alamat lengkap kantor pusat...">{{ old('company_address', $settings['company_address'] ?? '') }}</textarea>
                            @error('company_address') <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: Logo & Operasional --}}
            <div class="space-y-6">
                
                {{-- Logo Upload --}}
                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm text-center">
                    <h3 class="font-black text-slate-800 text-base uppercase tracking-widest mb-6">
                        Logo Perusahaan
                    </h3>
                    
                    <div class="relative w-32 h-32 mx-auto mb-4 group cursor-pointer" onclick="document.getElementById('logoInput').click()">
                        <div id="logoPreviewWrapper" class="w-full h-full rounded-[1.5rem] border-2 {{ isset($settings['company_logo']) && $settings['company_logo'] ? 'border-solid border-indigo-100' : 'border-dashed border-slate-300' }} flex items-center justify-center bg-slate-50 overflow-hidden relative z-10 transition-colors group-hover:border-indigo-400">
                            
                            @if(isset($settings['company_logo']) && $settings['company_logo'])
                                <img id="logoPreview" src="{{ asset('storage/' . $settings['company_logo']) }}" class="w-full h-full object-contain p-2">
                                <div id="logoPlaceholder" class="text-slate-400 flex-col items-center hidden">
                            @else
                                <img id="logoPreview" src="#" class="w-full h-full object-contain p-2 hidden">
                                <div id="logoPlaceholder" class="text-slate-400 flex flex-col items-center">
                            @endif
                                    <i class="fas fa-cloud-upload-alt text-3xl mb-1"></i>
                                    <span class="text-[10px] font-black uppercase tracking-widest">Upload</span>
                                </div>
                        </div>
                        <div class="absolute inset-0 bg-indigo-600/10 rounded-[1.5rem] opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center z-20">
                            <i class="fas fa-pen text-indigo-600 text-xl drop-shadow-md"></i>
                        </div>
                    </div>
                    <input type="file" name="company_logo" id="logoInput" class="hidden" accept="image/jpeg,image/png,image/svg+xml" onchange="previewImage(this)">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-2">Format: JPG, PNG, SVG (Max 2MB)</p>
                    @error('company_logo') <p class="text-[10px] text-rose-500 font-bold mt-2">{{ $message }}</p> @enderror
                </div>

                {{-- Konfigurasi Sistem & Operasional --}}
                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                    <h3 class="font-black text-slate-800 text-base uppercase tracking-widest mb-6 flex items-center gap-2">
                        <i class="fas fa-cogs text-indigo-500"></i> Sistem & Waktu
                    </h3>
                    
                    <div class="space-y-5">
                        
                        {{-- Currency / Mata Uang --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Mata Uang Default</label>
                            <div class="relative">
                                <select name="currency" class="w-full pl-4 pr-10 py-3 bg-slate-50 border @error('currency') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white appearance-none cursor-pointer">
                                    <option value="IDR" {{ (old('currency', $settings['currency'] ?? 'IDR') == 'IDR') ? 'selected' : '' }}>Rupiah (IDR)</option>
                                    <option value="USD" {{ (old('currency', $settings['currency'] ?? '') == 'USD') ? 'selected' : '' }}>US Dollar (USD)</option>
                                </select>
                                <i class="fas fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                            </div>
                            @error('currency') <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Timezone --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Zona Waktu Default</label>
                            <div class="relative">
                                <select name="timezone" class="w-full pl-4 pr-10 py-3 bg-slate-50 border @error('timezone') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white appearance-none cursor-pointer">
                                    <option value="Asia/Jakarta" {{ (old('timezone', $settings['timezone'] ?? 'Asia/Jakarta') == 'Asia/Jakarta') ? 'selected' : '' }}>WIB (Asia/Jakarta)</option>
                                    <option value="Asia/Makassar" {{ (old('timezone', $settings['timezone'] ?? '') == 'Asia/Makassar') ? 'selected' : '' }}>WITA (Asia/Makassar)</option>
                                    <option value="Asia/Jayapura" {{ (old('timezone', $settings['timezone'] ?? '') == 'Asia/Jayapura') ? 'selected' : '' }}>WIT (Asia/Jayapura)</option>
                                </select>
                                <i class="fas fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                            </div>
                            @error('timezone') <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Jam Operasional --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Jam Masuk</label>
                                <input type="time" name="work_start_time" value="{{ old('work_start_time', $settings['work_start_time'] ?? '08:00') }}" class="w-full px-4 py-3 bg-slate-50 border @error('work_start_time') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-center cursor-pointer">
                                @error('work_start_time') <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Jam Pulang</label>
                                <input type="time" name="work_end_time" value="{{ old('work_end_time', $settings['work_end_time'] ?? '17:00') }}" class="w-full px-4 py-3 bg-slate-50 border @error('work_end_time') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white text-center cursor-pointer">
                                @error('work_end_time') <p class="text-[10px] text-rose-500 font-bold mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- BUTTON SUBMIT --}}
        <div class="mt-8 flex justify-end">
            <button type="submit" onclick="this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> Menyimpan...'; this.classList.add('opacity-80', 'cursor-not-allowed');" class="bg-indigo-600 text-white px-8 py-3.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-200 transition-all flex items-center gap-2">
                <i class="fas fa-save"></i> Simpan Konfigurasi
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // Script untuk preview gambar logo secara instan sebelum di-upload
    function previewImage(input) {
        const preview = document.getElementById('logoPreview');
        const placeholder = document.getElementById('logoPlaceholder');
        const wrapper = document.getElementById('logoPreviewWrapper');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                wrapper.classList.remove('border-dashed', 'border-slate-300');
                wrapper.classList.add('border-solid', 'border-indigo-100');
                if(placeholder) {
                    placeholder.classList.remove('flex');
                    placeholder.classList.add('hidden');
                }
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection