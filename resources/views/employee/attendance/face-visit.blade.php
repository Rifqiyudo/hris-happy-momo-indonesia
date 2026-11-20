@extends('employee.layouts.app')

@section('title', 'Presensi Kunjungan Kerja')

@push('styles')
<style>
    /* UI Kamera & Container */
    #video-wrapper { 
        position: relative; 
        width: 100%; 
        aspect-ratio: 4/3; 
        border-radius: 2.5rem; 
        overflow: hidden; 
        background-color: #000;
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1);
        border: 4px solid #fff;
    }
    video { width: 100%; height: 100%; object-fit: cover; }
    
    /* Overlay Watermark Visual */
    .watermark-overlay {
        position: absolute; bottom: 0; left: 0; right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 100%);
        padding: 25px 20px 20px;
        color: white; pointer-events: none;
        z-index: 20;
    }

    .wm-main-time { font-family: 'Monaco', 'Consolas', monospace; font-weight: 900; line-height: 1; margin-bottom: 6px; }

    /* UI KETERANGAN LOKASI (Pengganti Maps agar Rapi) */
    .location-outer-card {
        background: #ffffff;
        padding: 1.5rem;
        border-radius: 3rem;
        border: 1px solid #f1f5f9;
        box-shadow: 0 15px 20px -5px rgb(0 0 0 / 0.05);
    }

    .location-inner-box {
        background: #f8fafc;
        padding: 1.25rem;
        border-radius: 2rem;
        border: 1px solid #f1f5f9;
        box-shadow: inset 0 2px 4px 0 rgb(0 0 0 / 0.03);
    }

    /* Pulse Indikator Sinyal GPS */
    .gps-indicator {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f1f5f9;
        padding: 6px 12px;
        border-radius: 1rem;
    }

    .pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulse-green 2s infinite;
    }

    @keyframes pulse-green {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col items-center pt-6 px-4 pb-32 text-slate-800">

    {{-- HEADER NAVIGATION --}}
    <div class="w-full max-w-md flex items-center justify-between mb-6 px-2">
        <a href="{{ route('employee.dashboard') }}" class="bg-white w-10 h-10 rounded-xl shadow-sm text-slate-400 border border-slate-100 flex items-center justify-center active:scale-90 transition-all">
            <i class="fas fa-chevron-left text-sm"></i> 
        </a>
        <div class="text-right">
            <h1 class="font-black text-slate-800 uppercase tracking-tighter text-xl leading-none">Kunjungan Kerja</h1>
            <span class="text-[8px] text-indigo-500 font-black uppercase tracking-[0.3em] block mt-1">Happy Momo Indonesia</span>
        </div>
    </div>

    <div class="w-full max-w-md space-y-6">
        
        {{-- SECTION 1: KAMERA --}}
        <div class="bg-white p-4 rounded-[3.5rem] shadow-xl shadow-slate-200 border border-white">
            <div id="video-wrapper">
                <video id="video" autoplay playsinline muted></video>
                <canvas id="canvas" class="hidden"></canvas>

                <div class="absolute top-5 right-5 z-30">
                    <button type="button" onclick="switchCamera()" class="bg-white/20 hover:bg-white/40 text-white w-12 h-12 rounded-2xl backdrop-blur-md border border-white/30 flex items-center justify-center transition active:scale-90 shadow-lg">
                        <i class="fas fa-camera-rotate text-lg"></i>
                    </button>
                </div>

                <div class="watermark-overlay">
                    <div class="flex justify-between items-end">
                        <div class="flex-1">
                            <h2 id="live-time" class="wm-main-time text-3xl">00:00:00</h2>
                            <div class="text-[10px] font-bold uppercase tracking-widest opacity-80">
                                <span>{{ date('d M Y') }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                             <div class="gps-indicator">
                                <div class="pulse-dot"></div>
                                <span id="gps-status" class="text-[9px] font-black text-slate-500 uppercase tracking-tighter">Sinyal GPS Aktif</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 2: KETERANGAN LOKASI (PENGGANTI MAPS) --}}
        <div class="location-outer-card">
            <div class="w-full flex items-center gap-3 mb-4 px-1">
                <div class="w-9 h-9 bg-indigo-50 rounded-2xl flex items-center justify-center shadow-sm text-indigo-500">
                    <i class="fas fa-location-dot text-sm"></i>
                </div>
                <span class="text-[11px] font-black text-slate-800 uppercase tracking-widest">Titik Lokasi Kunjungan</span>
            </div>
            
            <div class="location-inner-box">
                <div class="flex flex-col gap-4">
                    {{-- Detail Koordinat --}}
                    <div class="flex items-center justify-between border-b border-slate-200/50 pb-3">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Koordinat GPS</span>
                        <span id="gps-coords" class="text-[10px] font-bold text-indigo-600 font-mono">Mencari lokasi...</span>
                    </div>

                    {{-- Alamat Lengkap --}}
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter block mb-1">Alamat Terdeteksi</span>
                        <p id="visit-address" class="text-[11px] text-slate-700 font-bold leading-relaxed italic">
                            Menghubungkan ke satelit GPS...
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ACTION BUTTON --}}
        <form id="form-absen">
            @csrf
            <input type="hidden" name="image" id="image_data">
            <input type="hidden" name="address" id="address_data">
            <input type="hidden" name="latitude" id="lat_data">
            <input type="hidden" name="longitude" id="lng_data">
            <input type="hidden" name="category" value="visit">

            <button type="button" id="btn-submit" onclick="takeSnapshot()" class="w-full bg-slate-900 hover:bg-black text-white font-black py-6 rounded-[2.5rem] shadow-xl shadow-slate-200 transition-all active:scale-95 flex justify-center items-center gap-3 uppercase tracking-[0.2em] text-[11px]">
                <i class="fas fa-fingerprint text-xl text-indigo-400"></i> Konfirmasi Presensi
            </button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    let currentFacingMode = "user";
    let stream;

    // Real-time Clock
    function updateClock() {
        document.getElementById('live-time').innerText = new Date().toLocaleTimeString('id-ID', { hour12: false });
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Camera Logic
    async function startCamera() {
        if (stream) stream.getTracks().forEach(track => track.stop());
        try {
            const constraints = { video: { facingMode: currentFacingMode, width: { ideal: 1080 }, height: { ideal: 1080 } } };
            stream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = stream;
        } catch (err) {
            Swal.fire({ icon: 'error', title: 'Akses Kamera Gagal', text: 'Mohon izinkan kamera untuk absensi.' });
        }
    }

    function switchCamera() {
        currentFacingMode = currentFacingMode === "user" ? "environment" : "user";
        startCamera();
    }

    // Geolocation Tracker (Tanpa Maps)
    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(pos => {
            const { latitude, longitude } = pos.coords;
            document.getElementById('lat_data').value = latitude;
            document.getElementById('lng_data').value = longitude;
            document.getElementById('gps-coords').innerText = `${latitude.toFixed(6)}, ${longitude.toFixed(6)}`;

            // Reverse Geocoding (Nominatim API)
            fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latitude}&lon=${longitude}`)
                .then(res => res.json())
                .then(data => {
                    const addr = data.display_name;
                    document.getElementById('visit-address').innerText = addr;
                    document.getElementById('address_data').value = addr;
                });
        }, (err) => {
            console.error("GPS Error: ", err);
        }, { enableHighAccuracy: true });
    }

    // Capture Snapshot & Submit
    function takeSnapshot() {
        const address = document.getElementById('address_data').value;
        if (!address || address === "") {
            Swal.fire({ icon: 'warning', title: 'GPS Belum Siap', text: 'Tunggu hingga lokasi terdeteksi.' });
            return;
        }

        const ctx = canvas.getContext('2d');
        canvas.width = 1080; canvas.height = 1080;

        if (currentFacingMode === "user") {
            ctx.translate(canvas.width, 0); ctx.scale(-1, 1);
        }
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        ctx.setTransform(1, 0, 0, 1, 0, 0);

        // Watermark Teks
        ctx.fillStyle = "rgba(0, 0, 0, 0.5)";
        ctx.fillRect(0, canvas.height - 180, canvas.width, 180);
        ctx.fillStyle = "#ffffff";
        ctx.font = "bold 60px monospace";
        ctx.fillText(document.getElementById('live-time').innerText, 50, canvas.height - 100);
        ctx.font = "bold 30px sans-serif";
        ctx.fillText("{{ date('d F Y') }}".toUpperCase(), 50, canvas.height - 50);

        document.getElementById('image_data').value = canvas.toDataURL('image/jpeg', 0.9);

        Swal.fire({ title: 'Menyimpan Presensi...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        const formData = new FormData(document.getElementById('form-absen'));
        fetch("{{ route('employee.attendance.store-visit') }}", {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message }).then(() => {
                    window.location.href = "{{ route('employee.dashboard') }}";
                });
            } else {
                Swal.fire('Gagal', data.message, 'error');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', startCamera);
</script>
@endsection