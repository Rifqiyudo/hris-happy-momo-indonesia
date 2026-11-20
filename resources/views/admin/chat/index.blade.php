@extends('admin.layouts.master')
@section('title', 'Pesan Internal Karyawan')

@section('content')
<div class="h-[85vh] flex gap-6 pb-6 px-4 max-w-7xl mx-auto">
    {{-- SIDEBAR: DAFTAR KARYAWAN (KOTAK MASUK) --}}
    <div class="w-full lg:w-1/3 bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col overflow-hidden relative z-10">
        
        {{-- Header Sidebar --}}
        <div class="p-6 bg-white border-b border-slate-100/80 z-20">
            <div class="flex justify-between items-center mb-5">
                <h3 class="font-black text-slate-800 text-sm tracking-widest uppercase flex items-center gap-2">
                    <i class="fas fa-inbox text-indigo-500 text-lg"></i> Pesan
                </h3>
                <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-xl border border-indigo-100">{{ $chats->count() }} Aktif</span>
            </div>
            
            {{-- Tombol Pemicu Modal Pencarian (Gaya Modern) --}}
            <button onclick="openSearchModal()" class="w-full py-3.5 bg-slate-50 text-slate-500 rounded-2xl font-bold text-xs flex items-center justify-center gap-2 border border-slate-200 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all duration-300 group shadow-sm">
                <i class="fas fa-search group-hover:rotate-90 transition-transform duration-300"></i> Cari Karyawan Baru
            </button>
        </div>
        
        {{-- List Chat --}}
        <div class="flex-1 overflow-y-auto custom-scrollbar bg-slate-50/30">
            @forelse($chats as $chat)
                <a href="{{ route('admin.chat.show', $chat->id) }}" 
                   class="flex items-center p-4 border-b border-slate-100 hover:bg-white transition-all duration-200 group relative {{ request()->routeIs('admin.chat.show', $chat->id) ? 'bg-white' : '' }}">
                    
                    {{-- Indikator Aktif --}}
                    @if(request()->routeIs('admin.chat.show', $chat->id))
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-600 rounded-r-full"></div>
                    @endif
                    
                    {{-- Avatar --}}
                    <div class="relative flex-shrink-0 mr-4">
                        <img src="{{ $chat->user->avatar_url }}" 
                             class="w-14 h-14 rounded-[1.25rem] object-cover border-2 border-white shadow-sm group-hover:shadow-md transition-shadow"
                             onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($chat->user->name) }}&background=EEF2FF&color=4F46E5&bold=true'">
                        @if($chat->user->is_active)
                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full"></div>
                        @endif
                    </div>
                    
                    <div class="flex-1 min-w-0 py-1">
                        <div class="flex justify-between items-baseline mb-1">
                            {{-- NAMA: Hitam tebal jika ada pesan baru --}}
                            <p class="text-sm truncate transition-colors {{ $chat->unread_count > 0 ? 'font-black text-slate-900' : 'font-bold text-slate-700 group-hover:text-indigo-600' }}">
                                {{ $chat->user->name }}
                            </p>
                            
                            {{-- JAM: Merah menyala jika ada pesan baru --}}
                            <span class="text-[9px] font-black {{ $chat->unread_count > 0 ? 'text-rose-500' : 'text-slate-400 font-bold' }} ml-2 flex-shrink-0">
                                {{ $chat->updated_at ? $chat->updated_at->format('H:i') : '' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            {{-- TEKS PREVIEW: Penanganan Logika Teks & Lampiran Secara Ketat --}}
                            <p class="text-xs truncate w-4/5 {{ $chat->unread_count > 0 ? 'text-slate-800 font-bold' : 'text-slate-500 font-medium' }}">
                                @if($chat->lastMessage)
                                    @if(!empty(trim($chat->lastMessage->message)))
                                        {{ Str::limit(strip_tags($chat->lastMessage->message), 35) }}
                                    @elseif(!empty($chat->lastMessage->file_path))
                                        <i class="fas fa-paperclip {{ $chat->unread_count > 0 ? 'text-slate-700' : 'text-slate-400' }} mr-1"></i> Mengirim lampiran
                                    @else
                                        <i class="fas fa-envelope text-slate-400 mr-1"></i> Pesan baru diterima
                                    @endif
                                @else
                                    Mulai percakapan baru...
                                @endif
                            </p>
                            
                            {{-- BADGE NOTIFIKASI: Merah & Berkedip --}}
                            @if($chat->unread_count > 0)
                                <span class="bg-rose-500 text-white text-[10px] font-black min-w-[22px] h-[22px] flex items-center justify-center rounded-full px-1.5 shadow-lg shadow-rose-200 animate-pulse">
                                    {{ $chat->unread_count }}
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center p-10 flex flex-col items-center justify-center h-full opacity-60">
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-4 shadow-sm border border-slate-100">
                        <i class="fas fa-comments text-slate-300 text-3xl"></i>
                    </div>
                    <p class="text-slate-500 text-xs font-black uppercase tracking-widest">Kosong</p>
                    <p class="text-slate-400 text-[10px] mt-1">Belum ada percakapan internal.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- MAIN AREA: PLACEHOLDER (KOSONG) --}}
    <div class="hidden lg:flex flex-1 bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 flex-col items-center justify-center text-center p-10 relative overflow-hidden">
        {{-- Background Pattern --}}
        <div class="absolute inset-0 opacity-[0.02]" style="background-image: radial-gradient(#4f46e5 1px, transparent 1px); background-size: 20px 20px;"></div>
        
        <div class="relative z-10 flex flex-col items-center">
            <div class="w-32 h-32 bg-indigo-50 rounded-full flex items-center justify-center mb-6 shadow-inner relative">
                <i class="fas fa-paper-plane text-indigo-500 text-5xl ml-2 mt-2"></i>
                <div class="absolute top-2 right-2 w-6 h-6 bg-amber-400 rounded-full border-4 border-white"></div>
            </div>
            <h2 class="font-black text-slate-800 text-2xl tracking-tight mb-2">Happy Momo Messenger</h2>
            <p class="text-slate-500 text-sm max-w-sm leading-relaxed">
                Platform komunikasi internal yang aman dan terenkripsi. Silakan pilih kontak di panel kiri untuk memulai obrolan.
            </p>
            <div class="mt-8 flex items-center gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest bg-slate-50 px-4 py-2 rounded-full border border-slate-100">
                <i class="fas fa-lock text-emerald-500"></i> End-to-End Encrypted
            </div>
        </div>
    </div>
</div>

{{-- MODAL PENCARIAN KARYAWAN (MODERN) --}}
<div id="searchModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-opacity opacity-0 pointer-events-none" style="transition: opacity 0.3s ease;">
    <div class="bg-white rounded-[2rem] w-full max-w-md shadow-2xl transform scale-95 transition-transform duration-300 overflow-hidden" id="modalContent">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-black text-slate-800 uppercase text-xs tracking-widest flex items-center gap-2">
                    <i class="fas fa-users text-indigo-500"></i> Direktori Karyawan
                </h3>
                <button onclick="closeModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-200 text-slate-500 hover:bg-rose-500 hover:text-white transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <div class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                <input type="text" id="searchInput" placeholder="Cari nama atau NIK karyawan..." 
                       class="w-full pl-11 pr-4 py-3.5 bg-white border border-slate-200 rounded-2xl text-sm font-medium outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-shadow shadow-sm">
            </div>
        </div>
        
        <div id="results" class="max-h-[60vh] overflow-y-auto p-4 space-y-2 bg-white">
            <div class="text-center py-10 opacity-50">
                <i class="fas fa-keyboard text-3xl mb-3 text-slate-300"></i>
                <p class="text-xs font-bold text-slate-500">Ketik minimal 3 huruf untuk mencari.</p>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb { background-color: #94a3b8; }
</style>
@endsection

@section('scripts')
<script>
    const modal = document.getElementById('searchModal');
    const modalContent = document.getElementById('modalContent');
    const searchInput = document.getElementById('searchInput');

    function openSearchModal() { 
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
            searchInput.focus();
        }, 10);
    }

    function closeModal() { 
        modal.classList.add('opacity-0', 'pointer-events-none');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            searchInput.value = '';
            document.getElementById('results').innerHTML = `
                <div class="text-center py-10 opacity-50">
                    <i class="fas fa-keyboard text-3xl mb-3 text-slate-300"></i>
                    <p class="text-xs font-bold text-slate-500">Ketik minimal 3 huruf untuk mencari.</p>
                </div>`;
        }, 300);
    }

    modal.addEventListener('click', function(e) {
        if(e.target === this) closeModal();
    });

    searchInput.addEventListener('input', function(e) {
        let q = e.target.value;
        const resultsDiv = document.getElementById('results');
        
        if(q.length > 2) {
            resultsDiv.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-indigo-500 text-2xl"></i></div>';
            
            fetch(`/admin/chat/search?q=${q}`)
                .then(res => res.json())
                .then(data => {
                    if (data.length > 0) {
                        let html = data.map(u => `
                            <a href="/admin/chat/start/${u.id}" class="flex items-center p-3 bg-slate-50 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-200 rounded-2xl transition-all group">
                                <img src="${u.avatar_url}" class="w-12 h-12 rounded-xl mr-4 object-cover shadow-sm group-hover:shadow" 
                                     onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(u.name)}&background=EEF2FF&color=4F46E5'">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-black text-slate-800 group-hover:text-indigo-600 truncate">${u.name}</p>
                                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-tight truncate mt-0.5">
                                        <i class="fas fa-briefcase text-slate-300 mr-1"></i> ${u.position} 
                                        <span class="mx-1 text-slate-300">•</span> 
                                        ${u.branch}
                                    </p>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-slate-300 group-hover:text-indigo-500 group-hover:shadow-sm transition-all">
                                    <i class="fas fa-paper-plane text-xs"></i>
                                </div>
                            </a>`).join('');
                        resultsDiv.innerHTML = html;
                    } else {
                        resultsDiv.innerHTML = `
                            <div class="text-center py-10 opacity-50">
                                <i class="fas fa-search-minus text-3xl mb-3 text-rose-300"></i>
                                <p class="text-xs font-bold text-slate-500">Tidak ada karyawan yang cocok.</p>
                            </div>`;
                    }
                })
                .catch(err => {
                    resultsDiv.innerHTML = '<p class="text-xs text-center text-rose-500 p-4">Terjadi kesalahan jaringan.</p>';
                });
        } else {
            resultsDiv.innerHTML = `
                <div class="text-center py-10 opacity-50">
                    <i class="fas fa-keyboard text-3xl mb-3 text-slate-300"></i>
                    <p class="text-xs font-bold text-slate-500">Ketik minimal 3 huruf untuk mencari.</p>
                </div>`;
        }
    });
</script>
@endsection