@extends('admin.layouts.master')
@section('title', 'Audit Absensi - ' . $attendance->user->name)

@section('content')
<div class="max-w-7xl mx-auto pb-20">
    {{-- 1. HEADER & NAVIGASI --}}
    <div class="flex items-center justify-between mb-8 px-2">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.transaksi.attendance.index', ['date' => $attendance->date]) }}" class="w-12 h-12 bg-white border border-gray-100 rounded-2xl flex items-center justify-center text-slate-400 hover:text-indigo-600 shadow-sm transition active:scale-95 group">
                <i class="fas fa-chevron-left group-hover:-translate-x-1 transition-transform"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">Audit Kehadiran</h1>
                <p class="text-[10px] text-indigo-500 font-black uppercase tracking-[0.3em] mt-1 italic">Happy Momo HRIS System</p>
            </div>
        </div>
        
        <div class="hidden md:block text-right">
            <span class="px-5 py-2.5 rounded-2xl bg-white text-slate-500 text-[10px] font-black uppercase tracking-widest border border-slate-100 shadow-sm">
                LOG ID: #{{ $attendance->id }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- 2. KOLOM KIRI: PROFIL & BUKTI VISUAL --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Card Profil Karyawan --}}
            <div class="bg-white p-8 rounded-[3rem] shadow-xl shadow-slate-200/50 border border-white relative overflow-hidden">
                <div class="absolute top-0 right-0 p-6 opacity-10">
                    <i class="fas fa-user-shield text-6xl"></i>
                </div>
                
                <div class="text-center mb-8 relative">
                    <div class="relative inline-block mb-4">
                        <img src="{{ $attendance->user->avatar ? asset('storage/'.$attendance->user->avatar) : asset('assets/img/default-avatar.png') }}" 
                             class="w-28 h-28 rounded-[2.5rem] object-cover border-4 border-white shadow-2xl ring-1 ring-slate-100">
                        <div class="absolute -bottom-1 -right-1 w-8 h-8 {{ $attendance->user->is_active ? 'bg-emerald-500' : 'bg-slate-300' }} border-4 border-white rounded-full shadow-lg"></div>
                    </div>
                    <h3 class="font-black text-slate-800 text-xl uppercase leading-tight tracking-tighter">{{ $attendance->user->name }}</h3>
                    <p class="text-[10px] text-indigo-600 font-black uppercase tracking-[0.2em] mt-2 bg-indigo-50 inline-block px-4 py-1 rounded-full border border-indigo-100">
                        {{ $attendance->user->position->name ?? 'Staff' }}
                    </p>
                </div>

                <div class="space-y-4 bg-slate-50/50 p-6 rounded-[2rem] border border-slate-100">
                    <div class="flex justify-between items-center text-[10px]">
                        <span class="font-black text-slate-400 uppercase tracking-widest">ID Karyawan</span>
                        <span class="font-black text-slate-700">{{ $attendance->user->nik }}</span>
                    </div>
                    <div class="flex justify-between items-center text-[10px]">
                        <span class="font-black text-slate-400 uppercase tracking-widest">Penempatan Cabang</span>
                        {{-- MENAMPILKAN NAMA PT/CABANG SECARA DINAMIS --}}
                        <span class="font-black text-rose-500 uppercase tracking-tighter text-right max-w-[150px]">{{ $attendance->user->branch->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-[10px]">
                        <span class="font-black text-slate-400 uppercase tracking-widest">Departemen</span>
                        <span class="font-black text-slate-700 uppercase tracking-tighter">{{ $attendance->user->department->name ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- FOTO BUKTI MASUK --}}
            <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 group">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-6 bg-emerald-500 rounded-full"></span> Foto Masuk
                    </h4>
                    <span class="text-[9px] font-bold text-slate-400 italic">Geo-tagged</span>
                </div>
                @if($attendance->image_in)
                    <div class="relative overflow-hidden rounded-[2rem] border-4 border-slate-50 shadow-inner">
                        <img src="{{ asset('storage/' . $attendance->image_in) }}" class="w-full h-72 object-cover hover:scale-110 transition-transform duration-700 cursor-zoom-in" onclick="window.open(this.src)">
                        <div class="absolute bottom-4 left-4 right-4 bg-black/40 backdrop-blur-md p-3 rounded-2xl border border-white/20 pointer-events-none">
                            <p class="text-[8px] font-mono text-white tracking-widest uppercase"><i class="fas fa-clock mr-1 text-emerald-400"></i> {{ $attendance->clock_in }}</p>
                        </div>
                    </div>
                @else
                    <div class="w-full h-64 bg-slate-50 rounded-[2rem] flex flex-col items-center justify-center border-2 border-dashed border-slate-200 text-slate-300">
                        <i class="fas fa-image text-4xl mb-2 opacity-20"></i>
                        <p class="text-[10px] font-black uppercase tracking-widest">Tidak Ada Foto</p>
                    </div>
                @endif
            </div>

            {{-- FOTO BUKTI KELUAR --}}
            <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 group">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-6 bg-orange-500 rounded-full"></span> Foto Pulang
                    </h4>
                </div>
                @if($attendance->image_out)
                    <div class="relative overflow-hidden rounded-[2rem] border-4 border-slate-50 shadow-inner">
                        <img src="{{ asset('storage/' . $attendance->image_out) }}" class="w-full h-72 object-cover hover:scale-110 transition-transform duration-700 cursor-zoom-in" onclick="window.open(this.src)">
                        <div class="absolute bottom-4 left-4 right-4 bg-black/40 backdrop-blur-md p-3 rounded-2xl border border-white/20 pointer-events-none">
                            <p class="text-[8px] font-mono text-white tracking-widest uppercase"><i class="fas fa-clock mr-1 text-orange-400"></i> {{ $attendance->clock_out }}</p>
                        </div>
                    </div>
                @else
                    <div class="w-full h-64 bg-slate-50 rounded-[2rem] flex flex-col items-center justify-center border-2 border-dashed border-slate-200 text-slate-300">
                        <i class="fas fa-camera text-4xl mb-2 opacity-20"></i>
                        <p class="text-[10px] font-black uppercase tracking-widest">Belum Absen Pulang</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- 3. KOLOM KANAN: WAKTU & PETA LOKASI --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Info Jam & Status Keterlambatan --}}
            <div class="bg-white p-8 rounded-[3rem] shadow-xl shadow-slate-200/50 border border-white grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
                <div class="md:border-r border-slate-100 pr-4 text-center md:text-left">
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">Jam Masuk</p>
                    <h4 class="text-4xl font-black text-slate-800 tracking-tighter">{{ $attendance->clock_in ?? '--:--' }}</h4>
                    <p class="text-[10px] font-bold text-indigo-500 bg-indigo-50 inline-block px-3 py-0.5 rounded-lg mt-2">{{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('l, d M Y') }}</p>
                </div>
                <div class="md:border-r border-slate-100 pr-4 text-center md:text-left">
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">Jam Pulang</p>
                    <h4 class="text-4xl font-black text-slate-800 tracking-tighter">{{ $attendance->clock_out ?? '--:--' }}</h4>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2"><i class="fas fa-tag mr-1"></i> {{ strtoupper($attendance->category ?? 'OFFICE') }}</p>
                </div>
                <div class="text-center md:text-right">
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-3 tracking-widest">Status Presensi</p>
                    @if($attendance->is_late)
                        <div class="inline-flex flex-col items-end gap-1">
                            <span class="px-6 py-2.5 rounded-full bg-rose-50 text-rose-600 text-[10px] font-black border border-rose-100 uppercase italic shadow-lg shadow-rose-100">
                                <i class="fas fa-clock mr-1 animate-pulse"></i> Terlambat
                            </span>
                            <p class="text-[10px] text-rose-500 font-black mt-1">+{{ $attendance->late_minutes }} Menit</p>
                        </div>
                    @else
                        <span class="px-6 py-2.5 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-black border border-emerald-100 uppercase shadow-lg shadow-emerald-100">
                            <i class="fas fa-check-circle mr-1"></i> Tepat Waktu
                        </span>
                    @endif
                </div>
            </div>

            {{-- Peta Lokasi Leaflet --}}
            <div class="bg-white p-3 rounded-[3.5rem] shadow-xl shadow-slate-200/50 border border-white h-[600px] relative overflow-hidden group">
                {{-- Border inner untuk menjaga layout --}}
                <div class="w-full h-full rounded-[3rem] overflow-hidden border-4 border-slate-50 relative">
                    <div id="map" class="w-full h-full z-0"></div>
                    
                    {{-- Overlay Alamat Statis --}}
                    <div class="absolute bottom-6 left-6 right-6 z-[1000]">
                        <div class="bg-white/90 backdrop-blur-xl p-5 rounded-[2.2rem] shadow-2xl border border-white flex items-center gap-4 transition-all group-hover:scale-[1.02]">
                            <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white text-lg shadow-lg shadow-indigo-200">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Koordinat GPS Terdeteksi</p>
                                <p class="text-xs font-bold text-slate-700 leading-relaxed">{{ $attendance->address ?? 'Alamat rinci tidak tersedia' }}</p>
                                <p class="text-[9px] font-mono text-indigo-500 font-bold mt-1 tracking-tighter">LAT: {{ $attendance->latitude }} | LNG: {{ $attendance->longitude }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status GPS Fallback --}}
                @if(!$attendance->latitude || !$attendance->longitude)
                    <div class="absolute inset-0 bg-slate-100/90 backdrop-blur-md flex items-center justify-center rounded-[3rem] z-10">
                        <div class="text-center">
                            <div class="w-20 h-20 bg-slate-200 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce">
                                <i class="fas fa-satellite-dish text-slate-400 text-3xl"></i>
                            </div>
                            <p class="text-sm font-black text-slate-500 uppercase tracking-widest">Sinyal GPS Tidak Ditemukan</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* Styling khusus Leaflet agar clean */
    .leaflet-container { font-family: inherit; z-index: 1; background: #f8fafc; }
    .leaflet-popup-content-wrapper { border-radius: 1.5rem; padding: 5px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border: none; }
    .leaflet-bar { border: none !important; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1) !important; }
    .leaflet-bar a { border-radius: 12px !important; margin-bottom: 4px; border: 1px solid #f1f5f9; }
    .cursor-zoom-in { cursor: zoom-in; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lat = parseFloat("{{ $attendance->latitude }}");
        const lng = parseFloat("{{ $attendance->longitude }}");

        if (!isNaN(lat) && !isNaN(lng)) {
            // Setup Map Dasar
            var map = L.map('map', {
                scrollWheelZoom: false,
                zoomControl: true,
                dragging: true
            }).setView([lat, lng], 17);

            // Layer dari Carto (Sangat clean untuk audit HRIS)
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '© Happy Momo Indonesia'
            }).addTo(map);

            // Custom Icon Modern (Indigo Pulse)
            var customIcon = L.divIcon({
                className: 'custom-div-icon',
                html: `<div class='w-10 h-10 bg-indigo-600 rounded-2xl flex items-center justify-center text-white border-4 border-white shadow-2xl shadow-indigo-400 animate-bounce'>
                        <i class='fas fa-user-check text-xs'></i>
                       </div>`,
                iconSize: [40, 40],
                iconAnchor: [20, 20]
            });

            var marker = L.marker([lat, lng], { icon: customIcon }).addTo(map);
            
            // Popup Info
            marker.bindPopup(`
                <div class="text-center p-2">
                    <p class="font-black text-slate-800 uppercase text-[10px] tracking-widest mb-1">{{ $attendance->user->name }}</p>
                    <p class="text-[9px] text-slate-400 uppercase italic">Titik Presensi Terkunci</p>
                </div>
            `).openPopup();

            // SOLUSI UTAMA: Paksa redraw setelah container stabil
            
            setTimeout(() => { 
                map.invalidateSize(true); 
            }, 600);
        }
    });
</script>
@endpush