@extends('admin.layouts.master')
@section('title', 'Kiosk Absensi Smart QR - PT. Happy Momo Indonesia')

@section('content')
{{-- Audio Feedback Elements --}}
<audio id="audio-success" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>
<audio id="audio-late" src="https://assets.mixkit.co/active_storage/sfx/951/951-preview.mp3" preload="auto"></audio>
<audio id="audio-error" src="https://assets.mixkit.co/active_storage/sfx/1073/1073-preview.mp3" preload="auto"></audio>

<div class="fixed inset-0 z-[9999] bg-slate-50 flex flex-col items-center justify-center p-4 overflow-hidden">
    
    {{-- Background Decoration --}}
    <div class="absolute top-0 left-0 w-full h-1 bg-indigo-600"></div>
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-indigo-100/50 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-emerald-100/50 rounded-full blur-3xl"></div>

    <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-2 gap-12 items-center relative z-10 px-4">
        
        {{-- Sisi Kiri: Smart Scanner --}}
        <div class="space-y-6">
            <div class="text-center lg:text-left">
                <div class="inline-flex w-16 h-16 bg-indigo-600 text-white rounded-2xl items-center justify-center mb-4 shadow-xl shadow-indigo-200">
                    <i class="fas fa-qrcode text-2xl"></i>
                </div>
                <h1 class="text-4xl font-black text-slate-800 tracking-tight leading-none">Smart Scan</h1>
                <p class="text-slate-400 font-bold uppercase tracking-[0.2em] text-[10px] mt-3 italic">Posisikan QR Code di Tengah Kotak</p>
            </div>

            <div id="scanner-container" class="bg-white border-[8px] border-white shadow-2xl rounded-[3.5rem] overflow-hidden relative aspect-square bg-slate-900 group transition-all duration-300">
                {{-- Area Kamera --}}
                <div id="reader" class="w-full h-full object-cover"></div>
                
                {{-- Overlay UI Premium --}}
                <div class="absolute inset-0 pointer-events-none border-[60px] border-black/20 flex items-center justify-center">
                    <div class="w-64 h-64 border-2 border-indigo-500/50 rounded-[2.5rem] relative">
                         {{-- Garis Scan Animasi --}}
                         <div class="absolute w-full h-1 bg-indigo-400 shadow-[0_0_15px_#6366f1] top-0 animate-scan-line"></div>
                         
                         {{-- Corner Frame --}}
                         <div class="absolute -top-2 -left-2 w-10 h-10 border-t-4 border-l-4 border-indigo-500 rounded-tl-xl"></div>
                         <div class="absolute -top-2 -right-2 w-10 h-10 border-t-4 border-r-4 border-indigo-500 rounded-tr-xl"></div>
                         <div class="absolute -bottom-2 -left-2 w-10 h-10 border-b-4 border-l-4 border-indigo-500 rounded-bl-xl"></div>
                         <div class="absolute -bottom-2 -right-2 w-10 h-10 border-b-4 border-r-4 border-indigo-500 rounded-br-xl"></div>
                    </div>
                </div>

                {{-- Indikator Status Scanner --}}
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 bg-black/60 backdrop-blur-md px-6 py-2 rounded-full border border-white/20">
                    <p class="text-[10px] font-black text-white uppercase tracking-widest flex items-center gap-3">
                        <span id="scanner-status-dot" class="w-2 h-2 bg-emerald-500 rounded-full animate-ping"></span>
                        <span id="scanner-status-text">AI Scanner Active - Ready</span>
                    </p>
                </div>
            </div>
            
            <div class="text-center lg:text-left">
                <button onclick="restartScanner()" class="bg-white px-6 py-2 rounded-full shadow-sm border border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-widest hover:bg-slate-50 transition-all">
                    <i class="fas fa-sync-alt mr-2 text-indigo-500"></i> Reset Camera
                </button>
            </div>
        </div>

        {{-- Sisi Kanan: Real-time Display --}}
        <div class="flex flex-col gap-8">
            {{-- Jam Digital Gede --}}
            <div class="bg-white border border-slate-100 shadow-xl rounded-[3rem] p-10 text-center">
                <div id="live-clock" class="text-7xl font-black text-slate-800 tracking-tighter mb-1 font-mono">00:00:00</div>
                <div class="text-indigo-500 font-black uppercase tracking-[0.3em] text-[12px] flex items-center justify-center gap-3">
                    <i class="fas fa-calendar-alt opacity-40 text-lg"></i>
                    <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>
            </div>

            {{-- Profile Card (Wadah Hasil Scan) --}}
            <div id="employee-info" class="bg-white border border-slate-100 shadow-xl rounded-[3rem] p-12 min-h-[420px] flex flex-col items-center justify-center text-center transition-all duration-500 relative overflow-hidden">
                
                {{-- Default View (Standby) --}}
                <div id="info-default" class="transition-opacity duration-300">
                    <div class="w-28 h-28 bg-slate-50 rounded-full flex items-center justify-center mb-8 mx-auto text-slate-200 border-4 border-dashed border-slate-100 animate-pulse">
                        <i class="fas fa-id-badge text-6xl"></i>
                    </div>
                    <p class="text-slate-400 font-black text-[11px] uppercase tracking-[0.3em]">Siap Memindai ID Card...</p>
                </div>

                {{-- Result View (Success) --}}
                <div id="info-content" class="hidden w-full animate-fade-in-up">
                    <div class="relative inline-block mb-8">
                        <img id="emp-photo" src="" class="w-44 h-44 rounded-[3.5rem] object-cover mx-auto border-4 border-white shadow-2xl bg-slate-100">
                        <div id="status-icon" class="absolute -bottom-2 -right-2 w-14 h-14 rounded-2xl flex items-center justify-center text-white shadow-lg text-2xl transition-all duration-500">
                        </div>
                    </div>
                    
                    <h2 id="emp-name" class="font-black text-3xl text-slate-800 uppercase tracking-tight px-4"></h2>
                    <p id="emp-pos" class="text-indigo-600 font-bold text-sm uppercase mt-3 tracking-[0.2em]"></p>
                    <p id="emp-nik" class="text-slate-400 font-mono text-[11px] mt-1"></p>
                    
                    <div class="mt-8">
                        <div id="scan-status" class="inline-block px-12 py-4 rounded-3xl text-[13px] font-black uppercase tracking-widest shadow-xl"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #reader video { object-fit: cover !important; image-rendering: -webkit-optimize-contrast; }
    @keyframes scan-line { 0% { top: 0%; opacity: 0; } 50% { opacity: 1; } 100% { top: 100%; opacity: 0; } }
    .animate-scan-line { animation: scan-line 2.5s infinite linear; }
    .animate-fade-in-up { animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection

@section('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // --- 1. DIGITAL CLOCK ---
    function updateClock() {
        const now = new Date();
        document.getElementById('live-clock').textContent = now.toLocaleTimeString('id-ID', { hour12: false });
    }
    setInterval(updateClock, 1000);
    updateClock();

    // --- 2. SCANNER & AUTO-FOCUS ENGINE ---
    let html5QrCode = new Html5Qrcode("reader");
    let isLocked = false;
    let cameraTrack = null;
    let autoFocusTimer = null;

    const qrConfig = { 
        fps: 30, 
        qrbox: { width: 300, height: 300 },
        aspectRatio: 1.0,
        videoConstraints: {
            facingMode: "user",
            width: { ideal: 1280 },
            height: { ideal: 1280 }
        }
    };

    const startScanner = () => {
        html5QrCode.start({ facingMode: "user" }, qrConfig, onScanSuccess)
        .then(() => {
            // Aktifkan AI Auto-Focus Loop
            const videoElem = document.querySelector('#reader video');
            if (videoElem && videoElem.srcObject) {
                cameraTrack = videoElem.srcObject.getVideoTracks()[0];
                enableAutoFocus();
            }
        })
        .catch(err => {
            document.getElementById('scanner-status-dot').className = "w-2 h-2 bg-rose-500 rounded-full";
            document.getElementById('scanner-status-text').innerText = "Camera Error";
        });
    };

    function enableAutoFocus() {
        if (!cameraTrack) return;
        const capabilities = cameraTrack.getCapabilities();
        if (capabilities.focusMode) {
            autoFocusTimer = setInterval(async () => {
                try {
                    await cameraTrack.applyConstraints({ advanced: [{ focusMode: "continuous" }] });
                } catch (e) { console.log("Refocus focus..."); }
            }, 3000);
        }
    }

    const restartScanner = () => {
        if (autoFocusTimer) clearInterval(autoFocusTimer);
        html5QrCode.stop().then(() => startScanner()).catch(() => startScanner());
    };

    // --- 3. LOGIC ABSENSI ---
    function onScanSuccess(decodedText) {
        if (isLocked) return;
        isLocked = true; 
        
        if (navigator.vibrate) navigator.vibrate(150);
        
        // Visual Feedback: Flash Box
        const container = document.getElementById('scanner-container');
        container.style.borderColor = "#6366f1";

        fetch("{{ route('admin.transaksi.attendance.process-scan') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ nik: decodedText })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateUI(data);
                playAudio(data);
                speakResult(data.user.name, data.message);
                setTimeout(resetKiosk, 5000);
            } else {
                showError(data.message);
                playAudio({ type: 'error' });
                isLocked = false;
            }
        })
        .catch(err => {
            showError("Koneksi Bermasalah");
            isLocked = false;
        });
    }

    function updateUI(data) {
        const user = data.user;
        document.getElementById('info-default').classList.add('hidden');
        document.getElementById('info-content').classList.remove('hidden');
        
        document.getElementById('emp-name').innerText = user.name;
        document.getElementById('emp-pos').innerText = user.position || 'Staff';
        document.getElementById('emp-nik').innerText = 'ID: ' + user.nik;
        document.getElementById('emp-photo').src = user.avatar_url;
        
        const statusEl = document.getElementById('scan-status');
        const iconEl = document.getElementById('status-icon');
        statusEl.innerText = data.message;
        
        const msg = data.message.toUpperCase();

        if (msg.includes("TERLAMBAT") || msg.includes("LATE")) {
            statusEl.className = "inline-block px-12 py-4 rounded-3xl text-[13px] font-black uppercase tracking-widest bg-amber-50 text-amber-600 border border-amber-100 shadow-xl shadow-amber-100/50";
            iconEl.className = "absolute -bottom-2 -right-2 w-14 h-14 rounded-2xl flex items-center justify-center text-white shadow-lg bg-amber-500 scale-110";
            iconEl.innerHTML = '<i class="fas fa-clock"></i>';
        } else if (msg.includes("PULANG") || msg.includes("OUT")) {
            statusEl.className = "inline-block px-12 py-4 rounded-3xl text-[13px] font-black uppercase tracking-widest bg-indigo-50 text-indigo-600 border border-indigo-100 shadow-xl shadow-indigo-100/50";
            iconEl.className = "absolute -bottom-2 -right-2 w-14 h-14 rounded-2xl flex items-center justify-center text-white shadow-lg bg-indigo-500 scale-110";
            iconEl.innerHTML = '<i class="fas fa-sign-out-alt"></i>';
        } else {
            statusEl.className = "inline-block px-12 py-4 rounded-3xl text-[13px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-600 border border-emerald-100 shadow-xl shadow-emerald-100/50";
            iconEl.className = "absolute -bottom-2 -right-2 w-14 h-14 rounded-2xl flex items-center justify-center text-white shadow-lg bg-emerald-500 scale-110";
            iconEl.innerHTML = '<i class="fas fa-check"></i>';
        }
    }

    // --- 4. VOICE & AUDIO FEEDBACK ---
    function speakResult(name, message) {
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
            let voiceText = "";
            const msg = message.toUpperCase();

            if (msg.includes("TERLAMBAT") || msg.includes("LATE")) {
                voiceText = `Halo ${name}. Maaf Anda terlambat hari ini. Tetap semangat ya.`;
            } else if (msg.includes("PULANG") || msg.includes("OUT")) {
                voiceText = `Kerja bagus ${name}. Hati-hati di jalan dan sampai jumpa besok.`;
            } else {
                voiceText = `Selamat pagi ${name}. Anda hadir tepat waktu. Selamat bekerja.`;
            }

            const utterance = new SpeechSynthesisUtterance(voiceText);
            utterance.lang = 'id-ID';
            utterance.rate = 1.0;
            window.speechSynthesis.speak(utterance);
        }
    }

    function playAudio(data) {
        let audioId = 'audio-success';
        const msg = data.message ? data.message.toUpperCase() : "";
        if (msg.includes("TERLAMBAT")) audioId = 'audio-late';
        if (data.type === 'error' || data.sound === 'error') audioId = 'audio-error';
        
        const audio = document.getElementById(audioId);
        if (audio) {
            audio.currentTime = 0;
            audio.play().catch(e => console.log("Audio blocked by browser"));
        }
    }

    function resetKiosk() {
        document.getElementById('info-default').classList.remove('hidden');
        document.getElementById('info-content').classList.add('hidden');
        document.getElementById('scanner-container').style.borderColor = "white";
        isLocked = false;
    }

    function showError(msg) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: msg,
            timer: 3000,
            showConfirmButton: false,
            customClass: { popup: 'rounded-[3rem]' }
        });
    }

    document.addEventListener("DOMContentLoaded", startScanner);
</script>
@endsection