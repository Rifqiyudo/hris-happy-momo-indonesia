@extends('admin.layouts.master')
@section('title', 'Pengaturan Profil')

@section('content')
<div class="max-w-4xl mx-auto">
    
    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 shadow-sm border border-green-100 flex items-center gap-3 animate-fade-in-down">
            <i class="fas fa-check-circle text-lg"></i> 
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <div class="md:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center h-full">
                    <div class="relative inline-block group mb-4">
                        <img id="preview-image" 
                             src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=4f46e5&color=fff' }}" 
                             class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-xl mx-auto bg-gray-100">
                        
                        <div class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300 cursor-pointer backdrop-blur-sm" onclick="document.getElementById('avatar-input').click()">
                            <i class="fas fa-camera text-white text-2xl"></i>
                        </div>
                    </div>
                    
                    <h3 class="font-bold text-slate-800 text-lg mb-1">{{ $user->name }}</h3>
                    <p class="text-xs text-gray-400 mb-6 bg-gray-50 py-1 px-3 rounded-full inline-block">{{ $user->email }}</p>
                    
                    <input type="file" name="avatar" id="avatar-input" class="hidden" accept="image/*" onchange="previewImage(this)">
                    
                    <button type="button" onclick="document.getElementById('avatar-input').click()" class="w-full py-2.5 border-2 border-dashed border-indigo-200 rounded-xl text-sm font-bold text-indigo-600 hover:border-indigo-500 hover:bg-indigo-50 transition">
                        <i class="fas fa-upload mr-2"></i> Ganti Foto
                    </button>
                    <p class="text-[10px] text-gray-400 mt-2">Format: JPG, PNG (Max 2MB)</p>
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                    
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                        <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                            <i class="fas fa-user-cog"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-700 text-lg">Edit Biodata</h3>
                            <p class="text-xs text-gray-400">Perbarui informasi akun Anda</p>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none">
                        </div>
                        @error('name') <span class="text-xs text-red-500 mt-1 pl-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-8">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none">
                        </div>
                        @error('email') <span class="text-xs text-red-500 mt-1 pl-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="bg-slate-50 rounded-xl p-5 border border-slate-100">
                        <h4 class="font-bold text-slate-700 mb-3 flex items-center gap-2">
                            <i class="fas fa-lock text-slate-400"></i> Ganti Password
                        </h4>
                        <div class="text-xs text-slate-500 mb-4 ml-6">
                            Biarkan kolom di bawah kosong jika tidak ingin mengubah password.
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Password Baru</label>
                                <input type="password" name="password" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 transition" placeholder="******">
                                @error('password') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Ulangi Password</label>
                                <input type="password" name="password_confirmation" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 transition" placeholder="******">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-8">
                        <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-xl text-sm font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition transform active:scale-[0.98] flex items-center gap-2">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
    // Script Preview Image (Vanilla JS)
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-image').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection