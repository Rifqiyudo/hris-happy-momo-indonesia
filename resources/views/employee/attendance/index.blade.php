@extends('employee.layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /* Animasi Laser Scanning Face ID */
        .scan-line {
            width: 100%;
            height: 2px;
            background: #00ff00;
            box-shadow: 0 0 10px #00ff00;
            position: absolute;
            top: 0;
            animation: scan 3s infinite linear;
            opacity: 0.6;
        }
        @keyframes scan {
            0% { top: 0%; }
            50% { top: 100%; }
            100% { top: 0%; }
        }
        /* Kotak Wajah */
        .face-frame {
            border: 2px solid rgba(255, 255, 255, 0.5);
            border-radius: 20px;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.6); /* Gelapkan area luar */
        }
    </style>
@endpush

@section('content')
<div class="bg-gray-900 h-screen flex flex-col relative overflow-hidden">
    
    <div class="absolute top-0 left-0 right-0 z-30 p-4 flex justify-between items-center text-white">
        <a href="{{ route('employee.dashboard') }}" class="bg-black/30 p-2 rounded-full backdrop-blur-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="font-bold text-sm tracking-wide">FACE RECOGNITION</h1>
        <div class="w-8"></div>
    </div>

    <div class="relative flex-1 bg-black flex items-center justify-center overflow-hidden">
        <video id="video" autoplay playsinline class="absolute inset-0 w-full h-full object-cover z-0"></video>
        <canvas id="canvas" class="hidden"></canvas>
        
        <div class="relative z-10 w-64 h-80 face-frame flex flex-col justify-between p-4 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-green-500 rounded-tl-xl"></div>
            <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-green-500 rounded-tr-xl"></div>
            <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-green-500 rounded-bl-xl"></div>
            <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-green-500 rounded-br-xl"></div>

            <div class="scan-line"></div>

            <p class="text-white text-center text-xs mt-2 font-mono opacity-80 bg-black/50 rounded px-2 py-1 mx-auto">
                Posisikan wajah di dalam kotak
            </p>
        </div>

        <div id="loading-overlay" class="absolute inset-0 z-20 bg-black flex flex-col items-center justify-center text-white transition-opacity duration-500">
            <div class="w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mb-4"></div>
            <p class="animate-pulse font-mono">Mencari Koordinat GPS...</p>
        </div>
    </div>

    <div class="bg-white rounded-t-3xl p-6 relative z-30 -mt-6">
        
        <div class="mb-4">
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-bold text-gray-500 uppercase">Lokasi Anda</span>
                <span id="distance-info" class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded">Menghitung jarak...</span>
            </div>
            <div id="map" class="w-full h-32 rounded-xl border border-gray-200 z-0"></div>
            <input type="hidden" id="latitude">
            <input type="hidden" id="longitude">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <button onclick="takeSnapshot('in')" id="btn-in" disabled
                class="bg-emerald-500 text-white py-3 rounded-xl font-bold shadow-lg shadow-emerald-200 opacity-50 transition active:scale-95 flex items-center justify-center gap-2">
                <i class="fas fa-camera"></i> ABSEN MASUK
            </button>
            <button onclick="takeSnapshot('out')" id="btn-out" disabled
                class="bg-rose-500 text-white py-3 rounded-xl font-bold shadow-lg shadow-rose-200 opacity-50 transition active:scale-95 flex items-center justify-center gap-2">
                <i class="fas fa-sign-out-alt"></i> PULANG
            </button>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // --- KONFIGURASI KANTOR (GANTI DISINI) ---
    // Koordinat Margomulyo Permai (Contoh)
    const OFFICE_LAT = -7.245842;
    const OFFICE_LNG = 112.678456;
    const RADIUS_KM = 0.5; // Radius 500 meter

    let map, userMarker, officeCircle;

    // 1. SETUP KAMERA
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');

    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } })
            .then(stream => video.srcObject = stream)
            .catch(err => Swal.fire('Error Kamera', 'Izin kamera ditolak!', 'error'));
    }

    // 2. SETUP PETA (LEAFLET)
    function initMap(lat, lng) {
        // Inisialisasi Peta
        map = L.map('map').setView([lat, lng], 15);

        // Pakai Tile Layer Gratis (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Marker Posisi User
        const userIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/markers/marker-icon-2x-blue.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
        userMarker = L.marker([lat, lng], {icon: userIcon}).addTo(map).bindPopup("Posisi Anda").openPopup();

        // Lingkaran Radius Kantor (Zona Hijau)
        officeCircle = L.circle([OFFICE_LAT, OFFICE_LNG], {
            color: 'green',
            fillColor: '#22c55e',
            fillOpacity: 0.2,
            radius: RADIUS_KM * 1000 // Konversi KM ke Meter
        }).addTo(map);

        // Marker Kantor
        const officeIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/markers/marker-icon-2x-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41]
        });
        L.marker([OFFICE_LAT, OFFICE_LNG], {icon: officeIcon}).addTo(map).bindPopup("Kantor Happy Momo");
    }

    // 3. GET LOCATION
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(successLoc, errorLoc, {
            enableHighAccuracy: true,
            timeout: 10000
        });
    }

    function successLoc(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;

        // Simpan ke input hidden
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;

        // Hitung Jarak
        const distance = calculateDistance(lat, lng, OFFICE_LAT, OFFICE_LNG);
        const distMeter = Math.round(distance * 1000);

        // Update UI
        document.getElementById('loading-overlay').classList.add('opacity-0', 'pointer-events-none');
        
        // Cek apakah dalam jangkauan?
        const btnIn = document.getElementById('btn-in');
        const btnOut = document.getElementById('btn-out');
        const distInfo = document.getElementById('distance-info');

        if (distance <= RADIUS_KM) {
            distInfo.innerHTML = `<i class="fas fa-check-circle"></i> Dalam Jangkauan (${distMeter}m)`;
            distInfo.classList.replace('text-blue-600', 'text-green-600');
            distInfo.classList.replace('bg-blue-50', 'bg-green-50');
            
            // Aktifkan Tombol
            btnIn.disabled = false; btnIn.classList.remove('opacity-50');
            btnOut.disabled = false; btnOut.classList.remove('opacity-50');
        } else {
            distInfo.innerHTML = `<i class="fas fa-times-circle"></i> Terlalu Jauh (${distMeter}m)`;
            distInfo.classList.replace('text-blue-600', 'text-red-600');
            distInfo.classList.replace('bg-blue-50', 'bg-red-50');
            
            Swal.fire({
                icon: 'warning',
                title: 'Diluar Lokasi!',
                text: `Anda berjarak ${distMeter} meter dari kantor. Maksimal ${RADIUS_KM*1000} meter.`,
                confirmButtonColor: '#3085d6',
            });
        }

        // Tampilkan Peta
        initMap(lat, lng);
    }

    function errorLoc(err) {
        Swal.fire('GPS Error', 'Gagal mendapatkan lokasi. Pastikan GPS aktif!', 'error');
    }

    // --- UTILS ---
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // Radius bumi KM
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    function takeSnapshot(type) {
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0);
        
        const photo = canvas.toDataURL('image/jpeg', 0.8);
        const lat = document.getElementById('latitude').value;
        const lng = document.getElementById('longitude').value;

        // Efek Flash
        const flash = document.createElement('div');
        flash.className = 'absolute inset-0 bg-white z-50 transition-opacity duration-200';
        document.body.appendChild(flash);
        setTimeout(() => { flash.classList.add('opacity-0'); setTimeout(()=>flash.remove(), 200); }, 50);

        // Kirim
        Swal.fire({
            title: 'Memproses Wajah...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        fetch("{{ route('employee.attendance.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ photo, latitude: lat, longitude: lng, type })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                Swal.fire('Berhasil!', data.message, 'success').then(() => {
                    window.location.href = "{{ route('employee.dashboard') }}";
                });
            } else {
                Swal.fire('Gagal!', data.message, 'error');
            }
        })
        .catch(err => Swal.fire('Error', 'Terjadi kesalahan sistem', 'error'));
    }
</script>
@endpush