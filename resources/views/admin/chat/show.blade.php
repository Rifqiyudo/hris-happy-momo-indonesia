@extends('admin.layouts.master')
@section('title', 'Percakapan dengan ' . $chat->user->name)

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-[2.5rem] shadow-sm border border-gray-100 h-[80vh] flex flex-col overflow-hidden relative">
    
    {{-- Header Chat --}}
    <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-white z-10 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="relative">
                <img src="{{ $chat->user->avatar_url }}" class="w-14 h-14 rounded-2xl object-cover border-2 border-slate-100 shadow-sm" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($chat->user->name) }}&background=EEF2FF&color=4F46E5&bold=true'">
                @if($chat->user->is_active)
                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full"></div>
                @endif
            </div>
            <div>
                <h2 class="font-black text-slate-800 text-base uppercase tracking-widest">{{ $chat->user->name }}</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-[10px] text-indigo-500 font-bold uppercase tracking-tight bg-indigo-50 px-2 py-0.5 rounded-md">
                        <i class="fas fa-briefcase mr-1"></i> {{ $chat->user->position->name ?? 'Staff' }}
                    </span>
                    <span class="text-[10px] text-emerald-600 font-bold uppercase tracking-tight bg-emerald-50 px-2 py-0.5 rounded-md">
                        <i class="fas fa-building mr-1"></i> {{ $chat->user->branch->name ?? 'Pusat' }}
                    </span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.master.user.show', $chat->user->id) }}" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition shadow-sm" title="Profil Karyawan">
                <i class="fas fa-user"></i>
            </a>
            <a href="{{ route('admin.chat.index') }}" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition shadow-sm" title="Tutup Obrolan">
                <i class="fas fa-times"></i>
            </a>
        </div>
    </div>

    {{-- Chat Box Area --}}
    <div class="flex-1 overflow-y-auto p-8 space-y-6 bg-[#f8fafc] custom-scrollbar" id="chatBox">
        <div class="text-center mb-8">
            <span class="bg-white border border-slate-200 text-slate-400 text-[10px] font-bold uppercase tracking-widest px-4 py-1.5 rounded-full shadow-sm">
                Percakapan dimulai
            </span>
        </div>

        @forelse($messages as $msg)
            <div class="flex flex-col {{ $msg->sender_id == auth()->id() ? 'items-end' : 'items-start' }} animate-fadeIn group">
                
                {{-- Bubble Pesan --}}
                <div class="relative max-w-[75%] p-4 rounded-3xl shadow-sm border {{ $msg->sender_id == auth()->id() ? 'bg-indigo-600 text-white rounded-br-sm border-indigo-700' : 'bg-white text-slate-700 border-slate-200 rounded-bl-sm' }}">
                    
                    @if($msg->message)
                        <p class="text-[13px] font-medium leading-relaxed whitespace-pre-wrap">{{ $msg->message }}</p>
                    @endif
                    
                    {{-- Lampiran File --}}
                    @if($msg->file_path)
                        @php
                            $extension = pathinfo($msg->file_path, PATHINFO_EXTENSION);
                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        @endphp
                        
                        <div class="mt-2 {{ $msg->message ? 'pt-3 border-t ' . ($msg->sender_id == auth()->id() ? 'border-indigo-400/50' : 'border-slate-100') : '' }}">
                            @if($isImage)
                                <a href="{{ asset('storage/'.$msg->file_path) }}" target="_blank" class="block group/img relative overflow-hidden rounded-xl border {{ $msg->sender_id == auth()->id() ? 'border-indigo-400/50' : 'border-slate-200' }}">
                                    <img src="{{ asset('storage/'.$msg->file_path) }}" class="max-w-xs max-h-48 object-cover transition-transform duration-300 group-hover/img:scale-105" alt="Lampiran Gambar">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center">
                                        <i class="fas fa-expand text-white text-xl"></i>
                                    </div>
                                </a>
                            @else
                                <a href="{{ asset('storage/'.$msg->file_path) }}" target="_blank" 
                                   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ $msg->sender_id == auth()->id() ? 'bg-indigo-700/50 text-white hover:bg-indigo-700' : 'bg-slate-50 text-slate-700 hover:bg-slate-100' }} transition border border-transparent hover:border-slate-200">
                                    <div class="w-10 h-10 rounded-lg {{ $msg->sender_id == auth()->id() ? 'bg-white/20' : 'bg-white shadow-sm' }} flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-file-download text-lg {{ $msg->sender_id == auth()->id() ? 'text-white' : 'text-indigo-500' }}"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <span class="text-xs font-bold truncate block w-full">{{ basename($msg->file_path) }}</span>
                                        <span class="text-[9px] uppercase tracking-wider opacity-70">Klik untuk mengunduh</span>
                                    </div>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
                
                {{-- STATUS BACA: Jam & Centang Biru/Abu-abu Interaktif --}}
                <div class="flex items-center gap-1.5 mt-1.5 {{ $msg->sender_id == auth()->id() ? 'justify-end pr-2' : 'justify-start pl-2' }}">
                    <span class="text-[10px] font-bold text-slate-400 tracking-wider">
                        {{ $msg->created_at->format('H:i') }} 
                    </span>
                    
                    @if($msg->sender_id == auth()->id()) 
                        @if($msg->is_read)
                            {{-- Status: Dibaca (Centang Dua Biru) --}}
                            <div class="flex items-center gap-1 text-blue-500" title="Dibaca">
                                <i class="fas fa-check-double text-[11px]"></i>
                                <span class="text-[9px] font-bold uppercase tracking-wider hidden group-hover:inline-block transition-all duration-300">Dibaca</span>
                            </div>
                        @else
                            {{-- Status: Terkirim (Centang Satu Abu-abu) --}}
                            <div class="flex items-center gap-1 text-slate-400" title="Terkirim">
                                <i class="fas fa-check text-[11px]"></i>
                                <span class="text-[9px] font-bold uppercase tracking-wider hidden group-hover:inline-block transition-all duration-300">Terkirim</span>
                            </div>
                        @endif
                    @endif
                </div>
                
            </div>
        @empty
            {{-- Pesan Kosong dihandle oleh blok "Percakapan dimulai" di atas --}}
        @endforelse
    </div>

    {{-- Input Area --}}
    <form action="{{ route('admin.chat.store', $chat->id) }}" method="POST" enctype="multipart/form-data" class="p-4 bg-white border-t border-slate-100" id="chatForm">
        @csrf
        
        {{-- Area Preview Lampiran (Muncul saat file dipilih) --}}
        <div id="filePreviewContainer" class="hidden mb-3 px-4">
            <div class="flex items-center justify-between bg-indigo-50 border border-indigo-100 rounded-xl p-3">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-[11px] font-black text-indigo-700 truncate block w-48 lg:w-64" id="filePreviewName">nama_file.pdf</p>
                        <p class="text-[9px] font-bold text-indigo-400 uppercase tracking-widest">File siap dikirim</p>
                    </div>
                </div>
                <button type="button" onclick="cancelUpload()" class="w-8 h-8 flex items-center justify-center text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-full transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="flex gap-3 items-end">
            {{-- Tombol Lampiran --}}
            <button type="button" onclick="document.getElementById('fileInput').click()" 
                    class="w-12 h-12 flex-shrink-0 rounded-[1.25rem] bg-slate-50 border border-slate-200 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 hover:border-indigo-200 transition-all flex items-center justify-center shadow-sm"
                    title="Lampirkan File/Foto">
                <i class="fas fa-paperclip text-lg"></i>
            </button>
            <input type="file" name="file" class="hidden" id="fileInput" onchange="showFileName(this)" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg">
            
            {{-- Input Teks --}}
            <div class="flex-1 relative">
                <textarea name="message" id="messageInput" rows="1"
                       class="w-full bg-slate-50 rounded-[1.25rem] px-5 py-3.5 text-sm text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 border border-slate-200 resize-none custom-scrollbar" 
                       placeholder="Ketik pesan..." oninput="autoResize(this)" style="min-height: 48px; max-height: 120px;"></textarea>
            </div>
            
            {{-- Tombol Kirim --}}
            <button type="submit" id="submitBtn" class="h-12 px-6 flex-shrink-0 bg-indigo-600 text-white rounded-[1.25rem] text-xs font-black uppercase tracking-widest hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-200 transition-all flex items-center gap-2">
                <span>Kirim</span>
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </form>
</div>

<style>
    /* Animasi masuk pesan */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }
    
    /* Scrollbar minimalis untuk chat box & textarea */
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb { background-color: #94a3b8; }
</style>

<script>
    // 1. Scroll ke posisi paling bawah saat halaman dimuat
    const box = document.getElementById('chatBox');
    function scrollToBottom() {
        if(box) {
            box.scrollTop = box.scrollHeight;
        }
    }
    // Delay sedikit agar gambar termuat sebelum menghitung scrollHeight
    setTimeout(scrollToBottom, 100);

    // 2. Auto-resize textarea berdasarkan isi pesan
    function autoResize(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = (textarea.scrollHeight <= 120 ? textarea.scrollHeight : 120) + 'px';
        
        // Aktifkan/nonaktifkan tombol submit jika kosong (kecuali ada file)
        checkSubmitState();
    }

    // 3. Tampilkan preview nama file yang dipilih
    const fileInput = document.getElementById('fileInput');
    const previewContainer = document.getElementById('filePreviewContainer');
    const previewName = document.getElementById('filePreviewName');
    
    function showFileName(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Validasi ukuran (contoh max 5MB)
            if(file.size > 5 * 1024 * 1024) {
                alert('Ukuran file maksimal 5MB');
                cancelUpload();
                return;
            }
            
            previewName.textContent = file.name;
            previewContainer.classList.remove('hidden');
            
            // Ubah icon di preview berdasarkan tipe file
            const iconElement = previewContainer.querySelector('.fas');
            if(file.type.startsWith('image/')) {
                iconElement.className = 'fas fa-image';
            } else if (file.type === 'application/pdf') {
                iconElement.className = 'fas fa-file-pdf text-rose-500';
            } else {
                iconElement.className = 'fas fa-file-alt';
            }
            
            document.getElementById('messageInput').focus();
            checkSubmitState();
            scrollToBottom();
        }
    }

    // 4. Batal unggah file
    function cancelUpload() {
        fileInput.value = '';
        previewContainer.classList.add('hidden');
        checkSubmitState();
    }

    // 5. Submit form dengan Enter (tanpa Shift)
    const form = document.getElementById('chatForm');
    const msgInput = document.getElementById('messageInput');
    
    msgInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if(msgInput.value.trim() !== '' || fileInput.files.length > 0) {
                form.submit();
            }
        }
    });

    // 6. Validasi tombol submit (Opsional: mematikan tombol kirim jika input kosong)
    const submitBtn = document.getElementById('submitBtn');
    function checkSubmitState() {
        if(msgInput.value.trim() === '' && fileInput.files.length === 0) {
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }
    // Panggil saat load awal
    checkSubmitState();

</script>
@endsection