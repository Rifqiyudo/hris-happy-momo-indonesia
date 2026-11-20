@extends('employee.layouts.app')

@section('content')
<div class="fixed inset-0 bg-white z-50 flex flex-col overflow-hidden font-sans">
    
    {{-- Top Bar --}}
    <div class="p-5 flex justify-between items-center border-b bg-white z-10">
        <a href="{{ route('employee.dashboard') }}" class="text-slate-800 w-10 h-10 flex items-center justify-center bg-slate-50 rounded-xl">
            <i class="fas fa-chevron-left"></i>
        </a>
        <h1 class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-800">Presensi Lokasi</h1>
        <div class="w-10"></div>
    </div>

    {{-- SECTION 1: MAP & RADIUS VALIDATION --}}
    <div id="section-map" class="relative flex-1 bg-slate-100 flex flex-col">
        <div id="map" class="flex-1 z-0"></div>
        
        <div class="p-8 bg-white rounded-t-[3rem] shadow-[0_-15px_50px_rgba(0,0,0,0.08)] border-t z-10">
            <div class="flex items-center gap-5 mb-8">
                <div id="status-icon" class="w-14 h-14 rounded-[1.2rem] bg-slate-100 text-slate-400 flex items-center justify-center transition-all duration-500">
                    <i class="fas fa-location-arrow text-xl"></i>
                </div>
                <div class="flex-1">
                    <p id="status-text" class="text-[10px] font-black uppercase tracking-[0.15em] text-slate-400">Menghubungkan Satelit...</p>
                    <p id="location-name" class="text-xs font-bold text-slate-700 mt-1 leading-tight">Mencari titik presisi GPS anda...</p>
                </div>
            </div>

            {{-- Fields tambahan jika di luar radius --}}
            <div id="visit-fields" class="hidden mb-6 space-y-4 animate-fadeIn">
                <div class="relative">
                    <i class="fas fa-building absolute left-5 top-5 text-slate-400 text-xs"></i>
                    <input type="text" name="address_name" id="address_name" placeholder="Nama Lokasi / Instansi Klien" 
                        class="w-full bg-slate-50 border-none rounded-2xl py-4 pl-12 pr-6 text-xs font-bold focus:ring-2 focus:ring-amber-500 transition-all">
                </div>
                <div class="relative">
                    <i class="fas fa-sticky-note absolute left-5 top-5 text-slate-400 text-xs"></i>
                    <textarea id="notes" placeholder="Tujuan atau hasil kunjungan singkat..." 
                        class="w-full bg-slate-50 border-none rounded-2xl py-4 pl-12 pr-6 text-xs font-bold focus:ring-2 focus:ring-amber-500 h-24 transition-all"></textarea>
                </div>
            </div>

            <button onclick="checkAndProceed()" id="btn-next" disabled 
                class="w-full py-5 rounded-[1.5rem] bg-slate-200 text-slate-400 font-black text-[11px] tracking-[0.2em] uppercase transition-all shadow-lg active:scale-95">
                Validasi Lokasi...
            </button>
        </div>
    </div>

    {{-- SECTION 2: CAMERA FACE SCAN --}}
    <div id="section-camera" class="hidden fixed inset-0 bg-black z-[100] flex flex-col items-center justify-center">
        <video id="video" autoplay playsinline class="w-full h-full object-cover"></video>
        <canvas id="canvas" class="hidden"></canvas>
        
        {{-- Frame Overlay --}}
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
            <div class="w-64 h-80 border-2 border-white/20 rounded-[3.5rem] shadow-[0_0_0_9999px_rgba(0,0,0,0.85)] relative">
                <div class="absolute top-0 w-full text-center -translate-y-14">
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] text-indigo-400">Face Recognition</span>
                </div>
                <div class="absolute w-full h-1 bg-gradient-to-r from-transparent via-indigo-500 to-transparent shadow-[0_0_20px_#6366f1] top-0 animate-scan"></div>
                
                {{-- Corner Accents --}}
                <div class="absolute -top-1 -left-1 w-8 h-8 border-t-4 border-l-4 border-indigo-500 rounded-tl-2xl"></div>
                <div class="absolute -top-1 -right-1 w-8 h-8 border-t-4 border-r-4 border-indigo-500 rounded-tr-2xl"></div>
            </div>
        </div>

        <div class="absolute bottom-16 px-10 w-full flex flex-col gap-4">
            <button onclick="capture()" class="w-full bg-white text-slate-900 py-6 rounded-3xl font-black uppercase text-[11px] tracking-[0.2em] shadow-2xl active:scale-95 transition-all">
                <i class="fas fa-camera-retro mr-2"></i> Ambil Foto & Absen
            </button>
            <button onclick="backToMap()" class="w-full text-white/40 py-2 text-[10px] font-black uppercase tracking-[0.3em] hover:text-white transition-colors">
                <i class="fas fa-times mr-2"></i> Batalkan
            </button>
        </div>
    </div>

    {{-- HIDDEN FORM --}}
    <form id="form-absen" action="{{ route('employee.attendance.store') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="latitude" id="in-lat">
        <input type="hidden" name="longitude" id="in-lng">
        <input type="hidden" name="image" id="in-img">
        <input type="hidden" name="category" id="in-cat" value="office">
        <input type="hidden" name="address_name" id="in-address">
        <input type="hidden" name="notes" id="in-notes">
    </form>
</div>

<style>
    @keyframes scan { 0% { top: 0%; } 100% { top: 100%; } }
    .animate-scan { animation: scan 3s infinite linear; }
    .animate-fadeIn { animation: fadeIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .leaflet-container { font-family: inherit; }
</style>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const OFFICE_LAT = {{ $user->branch->latitude ?? -7.2504 }};
    const OFFICE_LNG = {{ $user->branch->longitude ?? 112.7688 }};
    const RADIUS_M = {{ $user->branch->radius ?? 100 }};
    
    let map, userMarker, officeCircle;
    let isWithinRadius = false;

    // 1. Inisialisasi Peta
    function initMap() {
        map = L.map('map', { zoomControl: false }).setView([OFFICE_LAT, OFFICE_LNG], 17);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        officeCircle = L.circle([OFFICE_LAT, OFFICE_LNG], {
            color: '#6366f1', weight: 2, fillColor: '#6366f1', fillOpacity: 0.1, radius: RADIUS_M
        }).addTo(map);

        trackUser();
    }

    // 2. Pantau Lokasi User
    function trackUser() {
        if (!navigator.geolocation) {
            Swal.fire('Error', 'Browser tidak mendukung GPS', 'error');
            return;
        }

        navigator.geolocation.watchPosition((pos) => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            const dist = calculateDistance(lat, lng, OFFICE_LAT, OFFICE_LNG) * 1000;

            document.getElementById('in-lat').value = lat;
            document.getElementById('in-lng').value = lng;

            if (userMarker) {
                userMarker.setLatLng([lat, lng]);
            } else {
                const userIcon = L.divIcon({
                    className: 'custom-div-icon',
                    html: "<div style='background-color:#4f46e5; height:12px; width:12px; border:3px solid white; border-radius:50%; box-shadow:0 0 10px rgba(0,0,0,0.3);'></div>",
                    iconSize: [12, 12], iconAnchor: [6, 6]
                });
                userMarker = L.marker([lat, lng], {icon: userIcon}).addTo(map);
            }
            
            map.panTo([lat, lng]);
            updateUI(dist);
        }, (err) => {
            Swal.fire('GPS Lemah', 'Pastikan GPS Aktif & Berikan Izin Lokasi', 'warning');
        }, { enableHighAccuracy: true, timeout: 15000 });
    }

    function updateUI(distance) {
        const icon = document.getElementById('status-icon');
        const text = document.getElementById('status-text');
        const locName = document.getElementById('location-name');
        const btn = document.getElementById('btn-next');
        const visitFields = document.getElementById('visit-fields');

        btn.disabled = false;

        if (distance <= RADIUS_M) {
            isWithinRadius = true;
            icon.className = "w-14 h-14 rounded-[1.2rem] bg-emerald-50 text-emerald-600 flex items-center justify-center";
            text.innerText = "Radius Kantor";
            text.className = "text-[10px] font-black uppercase tracking-[0.15em] text-emerald-600";
            locName.innerText = "Posisi aman, silakan mulai verifikasi wajah.";
            btn.innerText = "Mulai Verifikasi Wajah";
            btn.className = "w-full py-5 rounded-[1.5rem] bg-indigo-600 text-white font-black text-[11px] tracking-[0.2em] uppercase shadow-indigo-200 shadow-lg active:scale-95";
            visitFields.classList.add('hidden');
            document.getElementById('in-cat').value = 'office';
        } else {
            isWithinRadius = false;
            icon.className = "w-14 h-14 rounded-[1.2rem] bg-amber-50 text-amber-600 flex items-center justify-center";
            text.innerText = "Luar Jangkauan";
            text.className = "text-[10px] font-black uppercase tracking-[0.15em] text-amber-600";
            locName.innerText = `Jarak anda ${Math.round(distance)}m dari titik kantor.`;
            btn.innerText = "Lanjut Absen Kunjungan";
            btn.className = "w-full py-5 rounded-[1.5rem] bg-amber-500 text-white font-black text-[11px] tracking-[0.2em] uppercase shadow-amber-200 shadow-lg active:scale-95";
            visitFields.classList.remove('hidden');
            document.getElementById('in-cat').value = 'visit';
        }
    }

    // 3. Alur Kamera
    async function checkAndProceed() {
        if (!isWithinRadius) {
            const addr = document.getElementById('address_name').value;
            if (!addr) return Swal.fire('Data Kurang', 'Sebutkan lokasi kunjungan anda!', 'warning');
            document.getElementById('in-address').value = addr;
            document.getElementById('in-notes').value = document.getElementById('notes').value;
        }

        document.getElementById('section-map').classList.add('hidden');
        document.getElementById('section-camera').classList.remove('hidden');
        
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: "user", width: { ideal: 1280 }, height: { ideal: 720 } } 
            });
            document.getElementById('video').srcObject = stream;
        } catch (e) {
            Swal.fire('Akses Kamera Ditolak', 'Sistem membutuhkan kamera untuk Face ID.', 'error');
            backToMap();
        }
    }

    function backToMap() {
        document.getElementById('section-map').classList.remove('hidden');
        document.getElementById('section-camera').classList.add('hidden');
        const stream = document.getElementById('video').srcObject;
        if (stream) stream.getTracks().forEach(track => track.stop());
    }

    function capture() {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        // Mirror Effect
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0);
        ctx.setTransform(1, 0, 0, 1, 0, 0);

        // Watermark Data
        ctx.fillStyle = "white";
        ctx.font = "bold 16px sans-serif";
        const timestamp = new Date().toLocaleString('id-ID');
        ctx.shadowColor = "black"; ctx.shadowBlur = 7;
        ctx.fillText(timestamp, 20, canvas.height - 40);
        ctx.font = "12px sans-serif";
        ctx.fillText("GPS: " + document.getElementById('in-lat').value + ", " + document.getElementById('in-lng').value, 20, canvas.height - 20);
        
        document.getElementById('in-img').value = canvas.toDataURL('image/jpeg', 0.85);
        
        Swal.fire({ 
            title: 'Verifikasi...', 
            text: 'Mengirim data ke server PT. Happy Momo',
            allowOutsideClick: false, 
            didOpen: () => Swal.showLoading() 
        });
        
        document.getElementById('form-absen').submit();
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // km
        const dLat = (lat2-lat1) * Math.PI/180;
        const dLon = (lon2-lon1) * Math.PI/180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(lat1*Math.PI/180) * Math.cos(lat2*Math.PI/180) * Math.sin(dLon/2) * Math.sin(dLon/2);
        return R * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)));
    }

    window.onload = initMap;
</script>
@endpush