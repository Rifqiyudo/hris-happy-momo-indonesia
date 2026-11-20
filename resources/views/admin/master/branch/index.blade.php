@extends('admin.layouts.master')
@section('title', 'Manajemen Kantor & Cabang')

@section('content')

{{-- 1. LOAD ASSETS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

<style>
    #map {
        height: 380px;
        width: 100%;
        border-radius: 16px;
        border: 2px solid #f1f5f9;
        box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05);
        z-index: 1;
    }
    .leaflet-control-geocoder { 
        z-index: 1000 !important; 
        border-radius: 12px !important; 
        border: none !important; 
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1) !important; 
    }
    .leaflet-control-geocoder-form input {
        font-size: 12px !important;
        padding: 10px !important;
        border-radius: 8px !important;
    }
    .line-clamp-2 { 
        display: -webkit-box; 
        -webkit-line-clamp: 2; 
        -webkit-box-orient: vertical; 
        line-clamp: 2;
        overflow: hidden; 
    }
    .animate-gps { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .5; } }
</style>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- KOLOM KIRI: DAFTAR CABANG --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white p-6 rounded-4xl shadow-sm border border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Data Cabang</h1>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-1">Total Aktif: <span class="text-indigo-600">{{ $branches->total() }} Lokasi</span></p>
            </div>
            <form action="{{ route('admin.master.branch.index') }}" method="GET" class="relative group">
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="w-full md:w-72 pl-12 pr-5 py-3 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-600 focus:ring-2 focus:ring-indigo-500 transition-all" 
                    placeholder="Cari ID atau Nama...">
                <i class="fas fa-search absolute left-4 top-3.5 text-slate-300 group-focus-within:text-indigo-500 transition-colors"></i>
            </form>
        </div>

        <div class="bg-white rounded-4xl shadow-sm border border-slate-100 overflow-hidden text-nowrap">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100 w-20 text-center">ID</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">Nama Cabang</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">Alamat</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($branches as $branch)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4 text-xs font-bold text-slate-400 text-center">#{{ $branch->id }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-black text-slate-700 uppercase tracking-tight">{{ $branch->name }}</p>
                                        <p class="text-[9px] text-emerald-500 font-bold uppercase">Radius: {{ $branch->radius }}m</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-normal">
                                <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed max-w-xs">
                                    {{ $branch->address ?? 'Alamat belum diatur.' }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-2">
                                    <button onclick='editBranch({!! $branch->toJson() !!})' class="flex items-center justify-center w-8 h-8 rounded-xl bg-amber-50 text-amber-500 hover:bg-amber-500 hover:text-white transition-all shadow-sm">
                                        <i class="fas fa-pencil-alt text-[10px]"></i>
                                    </button>
                                    <button onclick="confirmDelete('{{ $branch->id }}')" class="flex items-center justify-center w-8 h-8 rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-all shadow-sm">
                                        <i class="fas fa-trash-alt text-[10px]"></i>
                                    </button>
                                </div>
                                <form id="del-{{ $branch->id }}" action="{{ route('admin.master.branch.destroy', $branch->id) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-20 text-center text-slate-400 text-[10px] font-black uppercase tracking-widest">Data tidak ditemukan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($branches->hasPages())
            <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100">
                {{ $branches->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- KOLOM KANAN: EDITOR LOKASI --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-4xl shadow-xl shadow-slate-200/50 border border-slate-50 sticky top-6 overflow-hidden">
            <div class="bg-slate-900 px-8 py-6 flex justify-between items-center">
                <h3 class="font-black text-white uppercase tracking-widest text-xs flex items-center gap-3" id="formTitle">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Tambah Kantor
                </h3>
                <button type="button" onclick="getLocation()" id="btnLocation" class="w-9 h-9 bg-white/10 text-white rounded-2xl flex items-center justify-center hover:bg-indigo-500 transition-all group" title="Gunakan Lokasi Saya Saat Ini">
                    <i class="fas fa-location-crosshairs text-sm group-hover:scale-110"></i>
                </button>
            </div>

            <div class="p-8">
                @if ($errors->any())
                    <div class="bg-rose-50 text-rose-500 p-4 rounded-2xl mb-4 text-[10px] font-bold uppercase tracking-widest">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form id="branchForm" action="{{ route('admin.master.branch.store') }}" method="POST">
                    @csrf
                    <div id="methodField"></div>

                    <div class="space-y-4">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ml-2">Nama Identitas Cabang</label>
                            <input type="text" name="name" id="name" required class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-xs font-bold focus:ring-2 focus:ring-indigo-600" placeholder="Pusat / Site Sadang">
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ml-2">Kode Cabang</label>
                            <input type="text" name="code" id="code" required class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-xs font-bold focus:ring-2 focus:ring-indigo-600" placeholder="Contoh: HO-SBY">
                        </div>
                        
                        {{-- KOTAK PETA INTERAKTIF (DILENGKAPI PENCARIAN LOKASI / GEOCODER) --}}
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block ml-2">Titik Koordinat & Peta</label>
                            <div id="map"></div>
                            <p class="text-[9px] text-slate-400 ml-2 italic">Gunakan kolom cari di pojok kiri atas peta atau klik/geser pin merah.</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100">
                                <p class="text-[8px] font-black text-slate-400 uppercase mb-1">Latitude</p>
                                <input type="text" name="latitude" id="lat" readonly class="w-full bg-transparent border-none p-0 text-[10px] font-mono font-bold text-slate-700 outline-none">
                            </div>
                            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100">
                                <p class="text-[8px] font-black text-slate-400 uppercase mb-1">Longitude</p>
                                <input type="text" name="longitude" id="lng" readonly class="w-full bg-transparent border-none p-0 text-[10px] font-mono font-bold text-slate-700 outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-2 block ml-2">Radius (Meter)</label>
                            <input type="number" name="radius" id="radius" value="100" oninput="updateCircle()" class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-xs font-bold focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block ml-2">Alamat Lengkap</label>
                            <textarea name="address" id="address" rows="3" class="w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-xs font-bold focus:ring-2 focus:ring-indigo-600" placeholder="Alamat fisik cabang..."></textarea>
                        </div>

                        <div class="flex flex-col gap-3 pt-4">
                            <button type="submit" id="btnSubmit" class="w-full bg-indigo-600 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] shadow-xl shadow-indigo-200 hover:bg-indigo-700 active:scale-95 transition-all">Simpan Cabang</button>
                            <button type="button" id="btnCancel" onclick="resetForm()" class="hidden w-full text-slate-400 py-3 text-[9px] font-black uppercase tracking-widest">Batalkan Edit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let defaultLat = "{{ $branches->first()->latitude ?? '-7.2511576' }}";
    let defaultLng = "{{ $branches->first()->longitude ?? '112.6816946' }}";
    
    let map = L.map('map', { 
        zoomControl: false, 
        fadeAnimation: true, 
        markerZoomAnimation: true 
    }).setView([parseFloat(defaultLat), parseFloat(defaultLng)], 13);
    
    L.control.zoom({ position: 'bottomright' }).addTo(map);

    // Menggunakan Esri World Street Map (Tampilan peta bersih & modern mirip Google Maps)
    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri',
        maxZoom: 18
    }).addTo(map);

    let marker, circle;
    let redIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
    });

    // Fitur Kotak Pencarian Alamat di dalam Peta (Geocoder Search)
    let geocoder = L.Control.geocoder({
        defaultMarkGeocode: false,
        placeholder: 'Cari Nama Tempat / Alamat...',
        position: 'topleft',
        suggestMinLength: 3,
        suggestTimeout: 500
    })
    .on('markgeocode', function(e) {
        let latlng = e.geocode.center;
        updateMarkerAndCircle(latlng.lat, latlng.lng);
        map.setView(latlng, 17);
        document.getElementById('address').value = e.geocode.name;
    })
    .addTo(map);

    map.on('click', function(e) {
        updateMarkerAndCircle(e.latlng.lat, e.latlng.lng);
        reverseGeocode(e.latlng.lat, e.latlng.lng);
    });

    // Ambil Lokasi GPS Perangkat secara Akurat
    function getLocation() {
        const btn = document.getElementById('btnLocation');
        if (navigator.geolocation) {
            btn.classList.add('bg-indigo-500', 'animate-gps');
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    updateMarkerAndCircle(lat, lng);
                    map.setView([lat, lng], 18);
                    reverseGeocode(lat, lng);
                    btn.classList.remove('bg-indigo-500', 'animate-gps');
                    Swal.fire({ icon: 'success', title: 'GPS Berhasil', text: 'Lokasi kantor terkunci dengan akurat.', timer: 1500, showConfirmButton: false });
                },
                (error) => {
                    btn.classList.remove('bg-indigo-500', 'animate-gps');
                    Swal.fire({ icon: 'error', title: 'GPS Gagal', text: 'Pastikan izin lokasi pada browser Anda aktif.' });
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        }
    }

    function updateMarkerAndCircle(lat, lng) {
        if (marker) map.removeLayer(marker);
        if (circle) map.removeLayer(circle);

        marker = L.marker([lat, lng], {icon: redIcon, draggable: true}).addTo(map);
        let radiusValue = document.getElementById('radius').value || 100;
        circle = L.circle([lat, lng], {
            color: '#10b981', weight: 2, fillColor: '#10b981', fillOpacity: 0.2, radius: parseInt(radiusValue)
        }).addTo(map);

        document.getElementById('lat').value = parseFloat(lat).toFixed(8);
        document.getElementById('lng').value = parseFloat(lng).toFixed(8);

        marker.on('dragend', function() {
            let pos = marker.getLatLng();
            updateMarkerAndCircle(pos.lat, pos.lng);
            reverseGeocode(pos.lat, pos.lng);
        });
    }

    function updateCircle() { if(circle) circle.setRadius(document.getElementById('radius').value || 0); }

    function reverseGeocode(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, {
            headers: { 'Accept-Language': 'id' }
        })
        .then(res => res.json())
        .then(data => { if(data.display_name) document.getElementById('address').value = data.display_name; })
        .catch(err => console.warn("Layanan alamat sibuk."));
    }

    function editBranch(data) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        document.getElementById('formTitle').innerHTML = '<span class="w-2 h-2 rounded-full bg-amber-400"></span> Mode Edit Cabang';
        
        document.getElementById('branchForm').action = "/admin/master/branch/" + data.id;
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        
        document.getElementById('name').value = data.name;
        document.getElementById('code').value = data.code;
        document.getElementById('address').value = data.address;
        document.getElementById('radius').value = data.radius;
        document.getElementById('lat').value = data.latitude;
        document.getElementById('lng').value = data.longitude;

        let btnSub = document.getElementById('btnSubmit');
        btnSub.innerText = 'Update Cabang';
        btnSub.className = "w-full bg-amber-500 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] shadow-xl shadow-amber-200 hover:bg-amber-600 transition-all";
        document.getElementById('btnCancel').classList.remove('hidden');

        if(data.latitude && data.longitude) {
            setTimeout(() => {
                map.invalidateSize();
                map.setView([parseFloat(data.latitude), parseFloat(data.longitude)], 17);
                updateMarkerAndCircle(parseFloat(data.latitude), parseFloat(data.longitude));
            }, 500);
        }
    }

    function resetForm() {
        document.getElementById('formTitle').innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Tambah Kantor Baru';
        document.getElementById('branchForm').reset();
        document.getElementById('branchForm.action').action = "{{ route('admin.master.branch.store') }}";
        document.getElementById('methodField').innerHTML = '';
        if (marker) map.removeLayer(marker);
        if (circle) map.removeLayer(circle);
        map.setView([parseFloat(defaultLat), parseFloat(defaultLng)], 13);
        let btnSub = document.getElementById('btnSubmit');
        btnSub.innerText = 'Simpan Cabang';
        btnSub.className = "w-full bg-indigo-600 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] shadow-xl shadow-indigo-200 hover:bg-indigo-700 transition-all";
        document.getElementById('btnCancel').classList.add('hidden');
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Cabang?', text: "Data tidak bisa dikembalikan!", icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#e11d48', confirmButtonText: 'Ya, Hapus'
        }).then((result) => { if (result.isConfirmed) document.getElementById('del-' + id).submit(); });
    }
</script>
@endsection