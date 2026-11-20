@extends('employee.layouts.app')

@section('title', 'Detail Riwayat Absensi')

@section('content')
<div class="p-6 space-y-6 pb-24 max-w-md mx-auto">
    
    {{-- 1. HEADER & BACK BUTTON --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('employee.history.index') }}" class="w-10 h-10 bg-white rounded-2xl flex items-center justify-center shadow-sm text-slate-400 active:scale-90 transition">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-black text-slate-800 tracking-tight leading-tight">Detail Presensi</h1>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    {{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('l, d F Y') }}
                </p>
            </div>
        </div>

        {{-- TOMBOL DOWNLOAD PDF --}}
        <a href="{{ route('employee.history.pdf', $attendance->id) }}" class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center shadow-sm active:scale-90 transition">
            <i class="fas fa-file-pdf"></i>
        </a>
    </div>

    {{-- 2. RINGKASAN WAKTU & STATUS --}}
    <div class="bg-white p-6 rounded-4xl shadow-xl shadow-slate-200/50 border border-slate-50 relative overflow-hidden">
        {{-- Status Badge --}}
        <div class="absolute top-6 right-6">
            @if($attendance->is_late)
                <span class="px-3 py-1 bg-rose-50 text-rose-500 text-[9px] font-black rounded-lg border border-rose-100 uppercase tracking-tighter">Terlambat</span>
            @else
                <span class="px-3 py-1 bg-emerald-50 text-emerald-500 text-[9px] font-black rounded-lg border border-emerald-100 uppercase tracking-tighter">Tepat Waktu</span>
            @endif
        </div>

        <div class="grid grid-cols-2 gap-6 mt-4">
            <div class="space-y-1">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Jam Masuk</p>
                <p class="text-2xl font-black text-slate-800">{{ $attendance->clock_in }}</p>
                <p class="text-[9px] text-slate-400 font-medium italic">{{ $attendance->category ?? 'Office' }}</p>
            </div>
            <div class="space-y-1 border-l border-slate-100 pl-6">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Jam Pulang</p>
                <p class="text-2xl font-black text-slate-800">{{ $attendance->clock_out ?? '--:--' }}</p>
                @if($attendance->clock_out)
                    <p class="text-[9px] text-emerald-500 font-bold uppercase italic">Selesai</p>
                @else
                    <p class="text-[9px] text-slate-300 font-bold uppercase italic tracking-tighter">Belum Absen</p>
                @endif
            </div>
        </div>
    </div>

    {{-- 3. BUKTI FOTO (MASUK & PULANG) --}}
    <div class="grid grid-cols-2 gap-4">
        {{-- Foto Masuk --}}
        <div class="space-y-3">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Foto Masuk</h3>
            <div class="aspect-3/4 bg-slate-100 rounded-4xl overflow-hidden border-2 border-white shadow-sm relative group">
                @if($attendance->image_in)
                    <img src="{{ asset('storage/'.$attendance->image_in) }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center text-slate-300">
                        <i class="fas fa-image text-3xl mb-2"></i>
                        <span class="text-[8px] font-black uppercase">Tanpa Foto</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Foto Pulang --}}
        <div class="space-y-3">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Foto Pulang</h3>
            <div class="aspect-3/4 bg-slate-100 rounded-4xl overflow-hidden border-2 border-white shadow-sm relative group">
                @if($attendance->image_out)
                    <img src="{{ asset('storage/'.$attendance->image_out) }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center text-slate-300">
                        <i class="fas fa-image text-3xl mb-2"></i>
                        <span class="text-[8px] font-black uppercase">Belum Pulang</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- 4. PETA LOKASI PRESENSI --}}
    <div class="space-y-3">
        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Lokasi Terdeteksi</h3>
        <div class="bg-white p-2 rounded-4xl shadow-sm border border-slate-50 overflow-hidden relative">
            <div id="map" class="w-full h-56 rounded-4xl z-0 border border-slate-100 shadow-inner"></div>
            
            {{-- Info Alamat Overlay --}}
            <div class="absolute bottom-6 left-6 right-6 bg-white/90 backdrop-blur-md p-4 rounded-3xl border border-white shadow-lg z-1000">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-500 shrink-0">
                        <i class="fas fa-map-marker-alt text-xs"></i>
                    </div>
                    <div>
                        <p class="text-[8px] font-black text-slate-400 uppercase">Titik Koordinat</p>
                        <p class="text-[10px] font-bold text-slate-700 leading-tight line-clamp-2">
                            {{ $attendance->latitude }}, {{ $attendance->longitude }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 5. CATATAN KETERLAMBATAN --}}
    @if($attendance->is_late)
    <div class="bg-rose-50 border border-rose-100 rounded-3xl p-5 flex items-center gap-4">
        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-rose-500 shadow-sm shrink-0">
            <i class="fas fa-clock text-sm"></i>
        </div>
        <div>
            <h4 class="text-xs font-black text-rose-900 uppercase tracking-tight">Informasi Keterlambatan</h4>
            <p class="text-[10px] text-rose-600 font-medium leading-relaxed italic">
                Anda terlambat selama <strong>{{ $attendance->late_minutes }} menit</strong> pada absensi masuk.
            </p>
        </div>
    </div>
    @endif

    {{-- 6. TOMBOL DOWNLOAD UTAMA --}}
    <div class="pt-4">
        <a href="{{ route('employee.history.pdf', $attendance->id) }}" class="w-full bg-slate-900 text-white font-black py-4 rounded-4xl shadow-2xl shadow-slate-200 active:scale-95 transition flex items-center justify-center gap-3">
            <i class="fas fa-file-pdf"></i> 
            <span class="text-[10px] font-black uppercase tracking-widest">Download Bukti PDF</span>
        </a>
    </div>

</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Perbaikan: Bungkus variabel Blade dengan tanda kutip untuk keamanan JS
        const lat = "{{ $attendance->latitude }}";
        const lng = "{{ $attendance->longitude }}";
        
        if (lat && lng) {
            var map = L.map('map', {
                zoomControl: false, 
                attributionControl: false
            }).setView([lat, lng], 16);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

            var customIcon = L.divIcon({
                className: 'custom-div-icon',
                html: "<div style='background-color:#6366f1; width:20px; height:20px; border-radius:50%; border:3px solid white; box-shadow:0 0 15px rgba(99,102,241,0.5)'></div>",
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });

            L.marker([lat, lng], {icon: customIcon}).addTo(map);
        }
    });
</script>

<style>
    .leaflet-container { font-family: inherit; }
    .z-1000 { z-index: 1000 !important; }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush