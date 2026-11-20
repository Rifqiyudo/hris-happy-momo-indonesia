@extends('employee.layouts.app')

@section('title', 'Sunting Profil - Happy Momo')

@section('content')

{{-- 1. HEADER MINIMALIS --}}
<div class="relative bg-white pb-6 pt-10 border-b border-slate-100">
    <div class="flex items-center gap-4 px-6 mb-4">
        <a href="{{ route('employee.profile.index') }}" class="w-10 h-10 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-600 active:scale-90 transition border border-slate-100">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <h1 class="text-xl font-black text-slate-800 tracking-tight">Sunting Profil</h1>
    </div>
</div>

<div class="px-6 py-10 pb-24 bg-slate-50/50">
    <div class="bg-white rounded-[2.5rem] shadow-sm p-8 border border-slate-100 relative">
        
        <form action="{{ route('employee.profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
            @csrf
            @method('PUT')

            {{-- 2. FOTO PROFIL DENGAN PREVIEW --}}
            <div class="flex flex-col items-center mb-10">
                <div class="relative group">
                    <div class="w-28 h-28 rounded-4xl border-4 border-white shadow-xl overflow-hidden bg-slate-50 ring-1 ring-indigo-100">
                        <img id="avatar_preview" 
                             src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=5D5FEF&color=fff' }}" 
                             alt="Avatar" 
                             class="w-full h-full object-cover">
                    </div>
                    
                    {{-- Tombol Kamera --}}
                    <label for="avatar_input" class="absolute -bottom-2 -right-2 w-10 h-10 bg-indigo-600 rounded-2xl flex items-center justify-center text-white border-4 border-white cursor-pointer shadow-lg hover:bg-indigo-700 transition active:scale-90">
                        <i class="fas fa-camera text-sm"></i>
                    </label>
                    <input type="file" name="avatar" id="avatar_input" class="hidden" accept="image/*" onchange="previewImage(this)">
                </div>
                <p class="text-[9px] font-black text-slate-300 mt-4 uppercase tracking-[0.2em]">Ketuk ikon untuk ubah foto</p>
            </div>

            {{-- 3. FORM INPUT --}}
            <div class="space-y-6 mb-10">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 ml-1 tracking-widest">Nama Lengkap</label>
                    <div class="relative">
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:outline-none focus:border-indigo-500 focus:bg-white transition-all shadow-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 ml-1 tracking-widest">No. WhatsApp</label>
                    <div class="relative">
                        <input type="number" name="phone" value="{{ old('phone', $user->phone) }}" required
                            class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:outline-none focus:border-indigo-500 focus:bg-white transition-all shadow-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 ml-1 tracking-widest">Ganti Password (Opsional)</label>
                    <div class="relative">
                        <input type="password" name="password" placeholder="Kosongkan jika tidak diganti"
                            class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:outline-none focus:border-indigo-500 focus:bg-white transition-all shadow-sm">
                    </div>
                </div>
            </div>

            {{-- TOMBOL SIMPAN --}}
            <button type="submit" class="w-full py-5 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-3xl shadow-lg shadow-indigo-100 transition transform active:scale-95 flex items-center justify-center gap-3">
                <i class="fas fa-save"></i>
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar_preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const hasError = "{{ $errors->any() ? 'true' : 'false' }}";
        const firstError = "{{ $errors->first() }}";
        const successMessage = "{{ session('success') }}";

        if (hasError === 'true') {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menyimpan',
                text: firstError,
                customClass: { popup: 'rounded-4xl' }
            });
        }

        if (successMessage) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: successMessage,
                timer: 2000,
                showConfirmButton: false,
                customClass: { popup: 'rounded-4xl' }
            });
        }
    });
</script>
@endpush

@endsection