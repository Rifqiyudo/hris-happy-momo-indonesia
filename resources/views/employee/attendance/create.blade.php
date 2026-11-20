@extends('employee.layouts.app')

@section('title', 'Absensi Wajah Real-Time')

@push('styles')
<style>
    /* 1. SETUP VIDEO AGAR BISA DIBOLAK-BALIK */
    video {
        /* Default: Normal (Tangan kanan di kanan) */
        transform: scaleX(1); 
        transition: transform 0.4s ease-in-out;
        object-fit: cover; /* Diubah ke cover agar memenuhi area box */
        background-color: #000;
    }

    /* 2. CLASS KHUSUS SAAT MODE CERMIN AKTIF */
    video.mirror-active {
        transform: scaleX(-1); 
    }

    /* Overlay Watermark pada layar */
    .info-overlay {
        background: linear-gradient(to top, rgba(0,0,0,0.85), transparent);
        position: absolute; bottom: 0; left: 0; right: 0;
        padding: 30px 15px 15px 15px;
        color: white; pointer-events: none;
    }

    /* Pulse effect untuk indikator GPS */
    .pulse-green { box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7); animation: pulse 1.5s infinite; }
    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(74, 222, 128, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(74, 222, 128, 0); }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col items-center pt-6 px-4 pb-24">

    {{-- HEADER --}}
    <div class="w-full max-w-md flex items-center justify-between mb-6">
        <a href="{{ route('employee.dashboard') }}" class="bg-white px-4 py-2 rounded-xl shadow-sm text-gray-600 hover:bg-gray-100 transition border border-gray-200">
            <i class="fas fa-arrow-left mr-2"></i> Dashboard
        </a>
        <div class="text-right">
            <span class="block font-bold text-gray-800 tracking-tight">Presensi Kehadiran</span>
            <span class="text-[10px] font-medium text-blue-600 uppercase tracking-widest">{{ date('d F Y') }}</span>
        </div>
    </div>

    {{-- CARD UTAMA --}}
    <div class="bg-white w-full max-w-md rounded-3xl shadow-xl border border-gray-100 p-6">
        
        <div class="text-center mb-6">
            <div class="inline-block px-4 py-1 rounded-full bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest mb-2">
                Verification System
            </div>
            <h1 class="text-xl font-black text-slate-800 uppercase tracking-tighter">
                @if($isCheckedIn ?? false) CHECK-OUT PULANG @else CHECK-IN MASUK @endif
            </h1>
        </div>

        {{-- CONTAINER VIDEO --}}
        <div class="relative w-full aspect-square bg-slate-900 rounded-[2rem] overflow-hidden shadow-2xl group mb-6 border-4 border-slate-50">
            
            {{-- VIDEO STREAM --}}
            <video id="video" autoplay playsinline muted class="absolute inset-0 w-full h-full z-10"></video>
            <canvas id="canvas" class="hidden"></canvas>

            {{-- BUTTON TOGGLE MIRROR --}}
            <button type="button" id="btn-mirror" onclick="toggleMirror()" 
                class="absolute top-4 right-4 z-30 bg-slate-800/80 text-white p-3 rounded-2xl backdrop-blur-md border border-white/20 shadow-lg active:scale-90 transition-all flex items-center gap-2">
                <i class="fas fa-arrows-alt-h"></i> 
                <span id="txt-mirror" class="text-[10px] font-black uppercase">Normal</span>
            </button>

            {{-- INFO OVERLAY --}}
            <div class="info-overlay z-20 flex justify-between items-end pb-6">
                <div class="text-left">
                    <p id="live-time" class="text-3xl font-black font-mono leading-none tracking-tighter">--:--:--</p>
                    <p id="live-date" class="text-[10px] text-gray-300 font-bold uppercase tracking-wider mt-1 italic">Waktu Server</p>
                </div>
                <div class="text-right">
                    <div class="flex items-center justify-end gap-2 mb-1">
                        <span id="gps-label" class="text-[9px] font-black uppercase tracking-widest text-rose-500">GPS Locked...</span>
                        <div id="gps-icon" class="w-2.5 h-2.5 rounded-full bg-rose-500"></div>
                    </div>
                    <p id="live-location" class="text-[10px] font-mono text-white/80">Mencari Koordinat...</p>
                </div>
            </div>

            {{-- ERROR STATE --}}
            <div id="camera-error" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-950 z-40 hidden">
                <div class="w-16 h-16 bg-slate-800 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-video-slash text-slate-500 text-2xl"></i>
                </div>
                <p class="text-white text-sm font-bold">Kamera Tidak Terdeteksi</p>
                <p class="text-slate-500 text-[10px] mt-1">Izinkan akses kamera di browser Anda.</p>
                <button type="button" onclick="location.reload()" class="mt-6 px-6 py-2 bg-blue-600 text-white text-xs font-black rounded-full uppercase tracking-widest">Muat Ulang</button>
            </div>
        </div>

        {{-- FORM PRESENSI --}}
        <form id="form-absen" action="{{ route('employee.attendance.store') }}" method="POST">
            @csrf
            <input type="hidden" name="image" id="image_data">
            <input type="hidden" name="latitude" id="latitude" value="">
            <input type="hidden" name="longitude" id="longitude" value="">

            @if($isCheckedIn ?? false)
                <button type="button" id="btn-submit" onclick="takeSnapshot()" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-black py-5 rounded-[1.5rem] shadow-xl shadow-rose-200 transition-all active:scale-95 flex justify-center items-center gap-3 uppercase tracking-widest text-sm">
                    <i class="fas fa-sign-out-alt"></i> Konfirmasi Pulang
                </button>
            @else
                <button type="button" id="btn-submit" onclick="takeSnapshot()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-5 rounded-[1.5rem] shadow-xl shadow-blue-200 transition-all active:scale-95 flex justify-center items-center gap-3 uppercase tracking-widest text-sm">
                    <i class="fas fa-fingerprint text-lg"></i> Konfirmasi Masuk
                </button>
            @endif
        </form>
    </div>

    <div class="mt-8 flex items-center gap-2 px-6 py-3 bg-slate-100 rounded-2xl border border-slate-200">
        <i class="fas fa-shield-alt text-slate-400 text-xs"></i>
        <p class="text-[9px] text-slate-500 font-medium leading-relaxed">
            Data lokasi (Geo-tagging) & foto akan diverifikasi secara otomatis oleh sistem HRIS.
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const liveTime = document.getElementById('live-time');
    const liveDate = document.getElementById('live-date');
    const liveLocation = document.getElementById('live-location');
    const gpsIcon = document.getElementById('gps-icon');
    const gpsLabel = document.getElementById('gps-label');
    const btnMirror = document.getElementById('btn-mirror');
    const txtMirror = document.getElementById('txt-mirror');
    
    let isMirrored = false;

    // --- 1. CLOCK ---
    function updateClock() {
        const now = new Date();
        liveTime.innerText = now.toLocaleTimeString('id-ID', { hour12: false });
        liveDate.innerText = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'short' });
    }
    setInterval(updateClock, 1000);
    updateClock();

    // --- 2. CAMERA SETUP ---
    async function startCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ 
                video: { 
                    facingMode: "user",
                    width: { ideal: 720 },
                    height: { ideal: 720 }
                } 
            });
            video.srcObject = stream;
        } catch (err) {
            document.getElementById('camera-error').classList.remove('hidden');
        }
    }
    startCamera();

    // --- 3. MIRROR LOGIC ---
    function toggleMirror() {
        isMirrored = !isMirrored;
        if (isMirrored) {
            video.classList.add('mirror-active');
            btnMirror.classList.replace('bg-slate-800/80', 'bg-blue-600');
            txtMirror.innerText = "Cermin";
        } else {
            video.classList.remove('mirror-active');
            btnMirror.classList.replace('bg-blue-600', 'bg-slate-800/80');
            txtMirror.innerText = "Normal";
        }
    }

    // --- 4. GEOLOCATION ---
    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(
            (pos) => {
                document.getElementById('latitude').value = pos.coords.latitude;
                document.getElementById('longitude').value = pos.coords.longitude;
                liveLocation.innerText = pos.coords.latitude.toFixed(6) + ", " + pos.coords.longitude.toFixed(6);
                
                gpsLabel.innerText = "GPS Terkunci";
                gpsLabel.classList.replace('text-rose-500', 'text-emerald-500');
                gpsIcon.classList.replace('bg-rose-500', 'bg-emerald-500');
                gpsIcon.classList.add('pulse-green');
            },
            (err) => {
                gpsLabel.innerText = "GPS Error";
                liveLocation.innerText = "Izin lokasi diperlukan!";
            },
            { enableHighAccuracy: true }
        );
    }

    // --- 5. SNAPSHOT & WATERMARK ---
    function takeSnapshot() {
        if (!document.getElementById('latitude').value) {
            Swal.fire({ icon: 'error', title: 'Lokasi Belum Siap', text: 'Tunggu hingga GPS terkunci (Indikator Hijau)' });
            return;
        }

        const ctx = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        // Draw Video with Mirroring Logic
        ctx.save();
        if (isMirrored) {
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
        }
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        ctx.restore();

        // 6. DRAW REAL-TIME WATERMARK ON IMAGE
        const h = canvas.height;
        const w = canvas.width;

        const grd = ctx.createLinearGradient(0, h - 150, 0, h);
        grd.addColorStop(0, "rgba(0,0,0,0)");
        grd.addColorStop(1, "rgba(0,0,0,0.8)");
        ctx.fillStyle = grd;
        ctx.fillRect(0, h - 150, w, 150);

        ctx.fillStyle = "white";
        ctx.textBaseline = "bottom";
        
        ctx.font = "bold " + (h * 0.08) + "px sans-serif";
        ctx.fillText(liveTime.innerText, 20, h - 50);
        
        ctx.font = "bold " + (h * 0.03) + "px sans-serif";
        ctx.fillStyle = "rgba(255,255,255,0.8)";
        const subText = liveDate.innerText.toUpperCase() + " | GPS: " + liveLocation.innerText;
        ctx.fillText(subText, 20, h - 20);

        // Convert to DataURL
        document.getElementById('image_data').value = canvas.toDataURL('image/jpeg', 0.9);

        // UI Submit
        Swal.fire({
            title: 'Memproses Absensi',
            html: 'Mohon tidak menutup aplikasi...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        document.getElementById('form-absen').submit();
    }
</script>
@endsection