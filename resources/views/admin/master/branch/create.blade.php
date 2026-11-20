@extends('admin.layouts.master')
@section('title', 'Tambah Cabang Baru')

{{-- Tambahkan CSS Leaflet --}}
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 400px; width: 100%; border-radius: 1.5rem; }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto pb-20">
    {{-- Breadcrumb --}}
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest text-slate-400">
            <li><a href="{{ route('admin.branch.index') }}" class="hover:text-indigo-600 transition">Cabang</a></li>
            <li><i class="fas fa-chevron-right text-[8px]"></i></li>
            <li class="text-slate-600">Tambah Baru</li>
        </ol>
    </nav>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-2xl overflow-hidden">
        <div class="p-10 border-b border-slate-50 bg-slate-50/30">
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Form Cabang</h1>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Tentukan titik lokasi di peta untuk mengaktifkan Auto-Radius</p>
        </div>

        <form action="{{ route('admin.branch.store') }}" method="POST" class="p-10">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                
                {{-- KOLOM KIRI: INPUT DATA --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Nama Cabang</label>
                        <input type="text" name="name" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-4 px-6 text-xs font-bold focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none" placeholder="PT. Happy Momo Indonesia">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Kode Cabang</label>
                        <input type="text" name="code" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-4 px-6 text-xs font-bold focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none" placeholder="BR-001">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-indigo-600 ml-1">Radius Absensi (Meter)</label>
                        <input type="number" id="radius" name="radius" value="100" min="10" required 
                            class="w-full bg-indigo-50 border border-indigo-100 rounded-2xl py-4 px-6 text-xs font-black text-indigo-700 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Latitude</label>
                        <input type="text" id="latitude" name="latitude" required readonly 
                            class="w-full bg-slate-100 border border-slate-200 rounded-2xl py-4 px-6 text-xs font-bold text-slate-500 cursor-not-allowed outline-none">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Longitude</label>
                        <input type="text" id="longitude" name="longitude" required readonly 
                            class="w-full bg-slate-100 border border-slate-200 rounded-2xl py-4 px-6 text-xs font-bold text-slate-500 cursor-not-allowed outline-none">
                    </div>
                </div>

                {{-- KOLOM KANAN: PETA INTERAKTIF --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1 flex items-center gap-2">
                            <i class="fas fa-map-marked-alt text-indigo-500"></i> Pilih Lokasi Kantor di Peta
                        </label>
                        <div id="map" class="shadow-inner border-4 border-slate-50"></div>
                        <p class="text-[9px] font-bold text-rose-500 uppercase tracking-tight mt-2 italic">* Klik pada peta untuk memindahkan posisi kantor</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Alamat Lengkap Kantor Cabang</label>
                        <textarea name="address" rows="3" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-4 px-6 text-xs font-bold focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none" placeholder="Jl. Boulevard No. 10..."></textarea>
                    </div>
                </div>
            </div>

            <div class="mt-12 flex items-center justify-between pt-8 border-t border-slate-50">
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" value="1" checked class="w-5 h-5 rounded-lg border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                    <label class="text-xs font-black uppercase tracking-widest text-slate-600 cursor-pointer">Status Cabang Aktif</label>
                </div>
                
                <div class="flex gap-4">
                    <a href="{{ route('admin.branch.index') }}" class="px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition">Batal</a>
                    <button type="submit" class="px-10 py-4 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition active:scale-95">Simpan Cabang</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // 1. Inisialisasi Peta (Default Jakarta)
    var map = L.map('map').setView([-6.2000, 106.8166], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    var marker;
    var circle;

    // 2. Fungsi Update Radius Visual
    function updateRadius() {
        var lat = document.getElementById('latitude').value;
        var lng = document.getElementById('longitude').value;
        var rad = document.getElementById('radius').value;

        if (lat && lng) {
            if (circle) map.removeLayer(circle);
            circle = L.circle([lat, lng], {
                color: '#4f46e5',      // Indigo 600
                fillColor: '#818cf8',  // Indigo 400
                fillOpacity: 0.2,
                radius: parseInt(rad)
            }).addTo(map);
        }
    }

    // 3. Event Klik Peta
    map.on('click', function(e) {
        var lat = e.latlng.lat;
        var lng = e.latlng.lng;

        // Set value ke input
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;

        // Update Marker
        if (marker) map.removeLayer(marker);
        marker = L.marker([lat, lng]).addTo(map);

        updateRadius();
        
        // Animasi Zoom ke titik yang diklik
        map.panTo(e.latlng);
    });

    // 4. Event Input Radius (Live Update Circle)
    document.getElementById('radius').addEventListener('input', updateRadius);
</script>
@endpush