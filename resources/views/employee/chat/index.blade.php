@extends('employee.layouts.app')
@section('title', 'Pesan Internal')

@section('content')
<div class="max-w-4xl mx-auto pb-10 px-4 md:px-0">
    {{-- Main Container --}}
    <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col overflow-hidden h-[85vh]">
        
        {{-- Header App --}}
        <div class="px-6 py-5 border-b border-slate-100 bg-white z-10 flex items-center justify-between sticky top-0">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-[1.25rem] flex items-center justify-center text-xl shadow-inner">
                    <i class="fas fa-headset"></i>
                </div>
                <div>
                    <h2 class="font-black text-slate-800 text-lg tracking-tight">Pusat Bantuan HRD</h2>
                    <p class="text-xs text-slate-400 font-medium">Saluran komunikasi internal & enkripsi</p>
                </div>
            </div>
            
            {{-- Indikator HRD Online Dinamis --}}
            <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-full {{ isset($hrdOnline) && $hrdOnline ? 'bg-emerald-50 border border-emerald-100' : 'bg-slate-50 border border-slate-100' }}">
                @if(isset($hrdOnline) && $hrdOnline)
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">HRD Online</span>
                @else
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-slate-400"></span>
                    </span>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">HRD Offline</span>
                @endif
            </div>
        </div>

        {{-- Search Bar (Sudah dibungkus Form agar berfungsi) --}}
        <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100">
            <form action="{{ route('employee.chat.index') ?? '#' }}" method="GET" class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari riwayat percakapan..." 
                       class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-2xl text-sm font-medium outline-none focus:ring-2 focus:ring-indigo-500 transition-shadow shadow-sm cursor-text">
            </form>
        </div>

        {{-- Chat List Area --}}
        <div class="flex-1 overflow-y-auto custom-scrollbar bg-slate-50/30">
            @forelse($chats as $chat)
                <a href="{{ route('employee.chat.show', $chat->id) }}" 
                   class="flex items-center p-5 border-b border-slate-100 hover:bg-white transition-all duration-200 group relative">
                    
                    {{-- Efek Garis Samping saat Hover --}}
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-600 rounded-r-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    
                    {{-- Avatar HRD --}}
                    <div class="relative flex-shrink-0 mr-4">
                        @if(isset($chat->hrd) && $chat->hrd->avatar)
                            <img src="{{ asset('storage/'.$chat->hrd->avatar) }}" 
                                 class="w-14 h-14 rounded-[1.25rem] object-cover border-2 border-white shadow-sm group-hover:shadow-md transition-shadow">
                        @else
                            <div class="w-14 h-14 rounded-[1.25rem] bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-black text-lg shadow-sm border-2 border-white group-hover:shadow-md transition-shadow">
                                HR
                            </div>
                        @endif
                        
                        {{-- Indikator Lingkaran Status Avatar (Dinamis dari akun HRD) --}}
                        @if(isset($chat->hrd) && $chat->hrd->is_active)
                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full"></div>
                        @else
                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-slate-400 border-2 border-white rounded-full"></div>
                        @endif
                    </div>

                    {{-- Isi Chat Preview --}}
                    <div class="flex-1 min-w-0 py-1">
                        <div class="flex justify-between items-baseline mb-1">
                            <p class="font-black text-sm text-slate-800 truncate group-hover:text-indigo-600 transition-colors">
                                {{ $chat->hrd->name ?? 'Tim HRD Happy Momo' }}
                            </p>
                            <span class="text-[10px] font-bold {{ $chat->unread_count > 0 ? 'text-indigo-600' : 'text-slate-400' }} ml-2 flex-shrink-0">
                                {{ $chat->lastMessage ? $chat->lastMessage->created_at->format('H:i') : '' }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <p class="text-xs truncate w-4/5 {{ $chat->unread_count > 0 ? 'text-slate-800 font-bold' : 'text-slate-500 font-medium' }}">
                                @if($chat->lastMessage)
                                    @if($chat->lastMessage->sender_id == auth()->id())
                                        <i class="fas fa-check{{ $chat->lastMessage->is_read ? '-double text-indigo-500' : ' text-slate-400' }} mr-1 text-[10px]"></i> 
                                    @endif
                                    
                                    @if($chat->lastMessage->file_path)
                                        <i class="fas fa-paperclip text-slate-400 mr-1"></i> Mengirim lampiran
                                    @else
                                        {{ $chat->lastMessage->message }}
                                    @endif
                                @else
                                    <span class="italic text-slate-400">Ketuk untuk mulai berkonsultasi...</span>
                                @endif
                            </p>

                            {{-- Notifikasi Badge Pesan Belum Dibaca --}}
                            @if($chat->unread_count > 0)
                                <span class="bg-indigo-600 text-white text-[10px] font-black min-w-[20px] h-5 flex items-center justify-center rounded-full px-1.5 shadow-md shadow-indigo-200">
                                    {{ $chat->unread_count }}
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                {{-- Empty State Modern --}}
                <div class="flex flex-col items-center justify-center h-full text-center p-8 opacity-70">
                    <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mb-6 shadow-sm border border-slate-100 relative">
                        <i class="fas fa-comments text-indigo-200 text-4xl"></i>
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-amber-100 text-amber-500 rounded-full flex items-center justify-center border-4 border-white">
                            <i class="fas fa-star text-xs"></i>
                        </div>
                    </div>
                    <h3 class="text-slate-700 text-sm font-black uppercase tracking-widest mb-2">Kotak Pesan Kosong</h3>
                    <p class="text-slate-400 text-xs max-w-xs leading-relaxed">
                        Anda belum memiliki riwayat percakapan. Silakan hubungi HRD jika Anda memiliki pertanyaan atau kendala.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    /* Styling Scrollbar Khusus untuk Area Chat */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb { background-color: #94a3b8; }
</style>
@endsection