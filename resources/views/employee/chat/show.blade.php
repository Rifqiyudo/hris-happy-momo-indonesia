@extends('employee.layouts.app')
@section('title', 'Hubungi HRD')

@section('content')
{{-- 
    WRAPPER UTAMA: FLEXBOX PENUH (ANTI-TENGGELAM)
    h-[calc(100vh-80px)] membuat area chat pas setinggi layar dikurangi tinggi Bottom Navigation Bar
--}}
<div class="max-w-md mx-auto bg-[#f8fafc] h-[calc(100vh-70px)] flex flex-col relative shadow-2xl overflow-hidden font-sans">
    
    {{-- ========================================== --}}
    {{-- 1. HEADER CHAT (TETAP DI ATAS) --}}
    {{-- ========================================== --}}
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white/95 backdrop-blur-xl z-30 shadow-sm shrink-0">
        <div class="flex items-center gap-4">
            <div class="relative">
                @if(isset($chat->hrd) && $chat->hrd->avatar)
                    <img src="{{ asset('storage/'.$chat->hrd->avatar) }}" class="w-12 h-12 rounded-[1.25rem] object-cover border-2 border-slate-100 shadow-sm">
                @else
                    <div class="w-12 h-12 rounded-[1.25rem] bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-black text-lg shadow-sm border-2 border-slate-100">
                        HR
                    </div>
                @endif
                {{-- Indikator Online --}}
                @if(isset($chat->hrd) && $chat->hrd->is_active)
                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full"></div>
                @endif
            </div>
            <div>
                <h2 class="font-black text-slate-800 text-sm uppercase tracking-widest leading-tight">{{ $chat->hrd->name ?? 'Tim HRD Happy Momo' }}</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-[9px] text-indigo-600 font-bold uppercase tracking-widest bg-indigo-50 px-2 py-0.5 rounded-md flex items-center gap-1 border border-indigo-100">
                        <i class="fas fa-briefcase text-[10px]"></i> HR Department
                    </span>
                </div>
            </div>
        </div>
        
        {{-- Tombol Close --}}
        <a href="{{ route('employee.message.index') }}" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition shadow-sm active:scale-95" title="Tutup Obrolan">
            <i class="fas fa-times text-sm"></i>
        </a>
    </div>

    {{-- ========================================== --}}
    {{-- 2. AREA PERCAKAPAN (MENGISI RUANG KOSONG) --}}
    {{-- ========================================== --}}
    <div class="flex-1 overflow-y-auto px-5 py-6 space-y-5 custom-scrollbar" id="chatBox">
        <div class="text-center mb-6">
            <span class="bg-white border border-slate-200 text-slate-400 text-[10px] font-black uppercase tracking-widest px-4 py-1.5 rounded-full shadow-sm">
                Percakapan dimulai
            </span>
        </div>

        @forelse($messages as $msg)
            <div class="flex flex-col {{ $msg->sender_id == auth()->id() ? 'items-end' : 'items-start' }} animate-fadeIn group">
                
                {{-- Bubble Pesan --}}
                <div class="relative max-w-[80%] p-4 rounded-3xl shadow-sm border text-[13px] font-medium leading-relaxed {{ $msg->sender_id == auth()->id() ? 'bg-indigo-600 text-white rounded-tr-sm border-indigo-700' : 'bg-white text-slate-700 border-slate-200 rounded-tl-sm' }}">
                    
                    @if($msg->message)
                        <p class="whitespace-pre-wrap">{{ $msg->message }}</p>
                    @endif
                    
                    {{-- Lampiran File --}}
                    @if($msg->file_path)
                        @php
                            $extension = pathinfo($msg->file_path, PATHINFO_EXTENSION);
                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        @endphp
                        
                        <div class="{{ $msg->message ? 'mt-2.5 pt-3 border-t ' . ($msg->sender_id == auth()->id() ? 'border-indigo-400/50' : 'border-slate-100') : '' }}">
                            @if($isImage)
                                <a href="{{ asset('storage/'.$msg->file_path) }}" target="_blank" class="block group/img relative overflow-hidden rounded-xl border {{ $msg->sender_id == auth()->id() ? 'border-indigo-400/50' : 'border-slate-200' }}">
                                    <img src="{{ asset('storage/'.$msg->file_path) }}" class="max-w-xs max-h-48 object-cover transition-transform duration-300 group-hover/img:scale-105" alt="Lampiran Gambar">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center">
                                        <i class="fas fa-expand text-white text-xl"></i>
                                    </div>
                                </a>
                            @else
                                <a href="{{ asset('storage/'.$msg->file_path) }}" target="_blank" 
                                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ $msg->sender_id == auth()->id() ? 'bg-indigo-700/50 text-white hover:bg-indigo-700' : 'bg-slate-50 text-slate-700 hover:bg-slate-100' }} transition border border-transparent hover:border-slate-200">
                                    <div class="w-8 h-8 rounded-lg {{ $msg->sender_id == auth()->id() ? 'bg-white/20' : 'bg-white shadow-sm' }} flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-file-download text-sm {{ $msg->sender_id == auth()->id() ? 'text-white' : 'text-indigo-500' }}"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <span class="text-[11px] font-bold truncate block w-full">{{ basename($msg->file_path) }}</span>
                                    </div>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
                
                {{-- STATUS BACA: Jam & Centang --}}
                <div class="flex items-center gap-1 mt-1.5 {{ $msg->sender_id == auth()->id() ? 'justify-end pr-2' : 'justify-start pl-2' }}">
                    <span class="text-[10px] font-bold text-slate-400 tracking-widest">
                        {{ $msg->created_at->format('H:i') }} 
                    </span>
                    
                    @if($msg->sender_id == auth()->id()) 
                        @if($msg->is_read)
                            <i class="fas fa-check-double text-[11px] text-blue-500" title="Dibaca"></i>
                        @else
                            <i class="fas fa-check text-[11px] text-slate-400" title="Terkirim"></i>
                        @endif
                    @endif
                </div>
                
            </div>
        @empty
            {{-- Initial Badge --}}
        @endforelse

        <div id="chat-bottom"></div>
    </div>

    {{-- ========================================== --}}
    {{-- 3. AREA INPUT FORM (DUDUK ALAMI DI BAWAH) --}}
    {{-- ========================================== --}}
    {{-- PERBAIKAN: Menghapus `fixed bottom-0` agar form duduk secara alami di bawah area chat --}}
    <div class="bg-white border-t border-slate-100 p-4 shrink-0 z-30 shadow-[0_-10px_40px_-15px_rgba(0,0,0,0.05)] rounded-t-3xl pb-6">
        <form action="{{ route('employee.chat.store', $chat->id) }}" method="POST" enctype="multipart/form-data" id="chatForm">
            @csrf
            
            {{-- Area Preview Lampiran --}}
            <div id="filePreviewContainer" class="hidden mb-3">
                <div class="flex items-center justify-between bg-indigo-50 border border-indigo-100 rounded-xl p-3">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-file-alt text-sm"></i>
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-[11px] font-black text-indigo-700 truncate block w-40" id="filePreviewName">nama_file.pdf</p>
                            <p class="text-[9px] font-bold text-indigo-400 uppercase tracking-widest mt-0.5">File siap dikirim</p>
                        </div>
                    </div>
                    <button type="button" onclick="cancelUpload()" class="w-8 h-8 flex items-center justify-center text-rose-400 hover:text-rose-600 bg-white rounded-full transition shadow-sm">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="flex gap-2.5 items-end">
                {{-- Tombol Lampiran --}}
                <button type="button" onclick="document.getElementById('fileInput').click()" 
                        class="w-12 h-12 flex-shrink-0 rounded-[1.25rem] bg-slate-50 border border-slate-200 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition-all flex items-center justify-center shadow-inner active:scale-95"
                        title="Lampirkan File/Foto">
                    <i class="fas fa-paperclip text-lg"></i>
                </button>
                <input type="file" name="file" class="hidden" id="fileInput" onchange="showFileName(this)" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg">
                
                {{-- Input Teks --}}
                <div class="flex-1 relative">
                    <textarea name="message" id="messageInput" rows="1"
                           class="w-full bg-slate-50 rounded-[1.25rem] px-5 py-3.5 text-sm text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 border border-slate-200 resize-none custom-scrollbar shadow-inner leading-relaxed" 
                           placeholder="Ketik pesan..." oninput="autoResize(this)" style="min-height: 48px; max-height: 120px;"></textarea>
                </div>
                
                {{-- Tombol Kirim --}}
                <button type="submit" id="submitBtn" class="w-12 h-12 flex-shrink-0 bg-indigo-600 text-white rounded-[1.25rem] hover:bg-indigo-700 shadow-lg shadow-indigo-200 flex items-center justify-center transition-transform active:scale-95">
                    <i class="fas fa-paper-plane text-base ml-[-2px]"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }
    
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb { background-color: #94a3b8; }
</style>

<script>
    const box = document.getElementById('chatBox');
    function scrollToBottom() {
        if(box) { box.scrollTop = box.scrollHeight; }
    }
    setTimeout(scrollToBottom, 100);

    function autoResize(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = (textarea.scrollHeight <= 120 ? textarea.scrollHeight : 120) + 'px';
        checkSubmitState();
    }

    const fileInput = document.getElementById('fileInput');
    const previewContainer = document.getElementById('filePreviewContainer');
    const previewName = document.getElementById('filePreviewName');
    
    function showFileName(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            if(file.size > 5 * 1024 * 1024) {
                alert('Ukuran file maksimal 5MB');
                cancelUpload();
                return;
            }
            
            previewName.textContent = file.name;
            previewContainer.classList.remove('hidden');
            
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

    function cancelUpload() {
        fileInput.value = '';
        previewContainer.classList.add('hidden');
        checkSubmitState();
    }

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

    const submitBtn = document.getElementById('submitBtn');
    function checkSubmitState() {
        if(msgInput.value.trim() === '' && fileInput.files.length === 0) {
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }
    checkSubmitState();
</script>
@endsection