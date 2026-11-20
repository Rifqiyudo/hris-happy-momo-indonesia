<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>HRIS - PT. Happy Momo Indonesia</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        primary: '#4f46e5', // Indigo 600
                        secondary: '#8b5cf6', // Violet 500
                    }
                }
            }
        }
    </script>

    <style>
        .blob {
            position: absolute;
            filter: blur(40px);
            z-index: -1;
            opacity: 0.3;
            animation: move 12s infinite alternate ease-in-out;
        }
        @keyframes move {
            from { transform: translate(0, 0) scale(1); }
            to { transform: translate(30px, -30px) scale(1.05); }
        }
        /* Styling khusus untuk membatasi ukuran gambar di mobile */
        .hero-img-wrapper {
            position: relative;
            width: 100%;
            max-width: 380px; /* Batas ukuran di HP */
            margin: 0 auto;
        }
        @media (min-width: 1024px) {
            .hero-img-wrapper { max-width: none; }
        }
    </style>
</head>
<body class="antialiased bg-slate-50/50 text-slate-600 relative overflow-x-hidden selection:bg-indigo-500 selection:text-white">

    {{-- DEKORASI BACKGROUND MODERN CORPORATE --}}
    <div class="blob bg-indigo-200/60 w-[300px] md:w-[500px] h-[300px] md:h-[500px] rounded-full top-0 left-0 mix-blend-multiply"></div>
    <div class="blob bg-blue-200/60 w-[300px] md:w-[500px] h-[300px] md:h-[500px] rounded-full top-0 right-0 mix-blend-multiply" style="animation-delay: 2s;"></div>

    {{-- NAVBAR (Responsive & Glassmorphism) --}}
    <nav class="fixed w-full z-50 transition-all duration-300 bg-white/85 backdrop-blur-lg border-b border-slate-200/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 sm:h-20">
                {{-- Logo --}}
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-indigo-600 to-blue-700 rounded-lg sm:rounded-xl flex items-center justify-center text-white shadow-md shadow-indigo-200 shrink-0">
                        <i class="fas fa-building sm:text-lg"></i>
                    </div>
                    <div>
                        <h1 class="font-extrabold text-sm sm:text-xl text-slate-900 leading-tight tracking-tight">PT. Happy Momo Indonesia</h1>
                        <p class="text-[9px] sm:text-[10px] text-indigo-600 font-black tracking-widest uppercase mt-0.5">Enterprise HR System</p>
                    </div>
                </div>

                {{-- Menu Desktop (Sembunyi di Mobile) --}}
                <div class="hidden md:flex items-center gap-8">
                    <a href="#home" class="text-sm font-bold text-slate-600 hover:text-indigo-600 transition-colors">Beranda</a>
                    <a href="#features" class="text-sm font-bold text-slate-600 hover:text-indigo-600 transition-colors">Modul Sistem</a>
                    <a href="#about" class="text-sm font-bold text-slate-600 hover:text-indigo-600 transition-colors">Tentang Kami</a>
                </div>

                {{-- Auth Buttons (Responsive) --}}
                <div class="flex items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/admin/dashboard') }}" class="px-4 sm:px-6 py-2 sm:py-2.5 bg-slate-900 text-white rounded-lg sm:rounded-full text-xs sm:text-sm font-bold hover:bg-indigo-600 transition-colors shadow-md">
                                <i class="fas fa-columns mr-1.5 sm:mr-2"></i> Dasbor Utama
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-4 sm:px-5 py-2 sm:py-2.5 text-indigo-700 font-extrabold text-xs sm:text-sm hover:bg-indigo-50 rounded-lg sm:rounded-full transition-colors">Login Masuk</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-5 py-2.5 bg-indigo-600 text-white rounded-full text-sm font-bold hover:bg-indigo-700 transition-colors shadow-md shadow-indigo-200 hidden md:inline-block">
                                    Registrasi Pegawai
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    {{-- HERO SECTION (Corporate Style & Fully Responsive) --}}
    <section id="home" class="pt-24 sm:pt-32 pb-16 sm:pb-20 lg:pt-40 lg:pb-32 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-12 items-center">
                
                {{-- Text Content --}}
                <div class="text-center lg:text-left z-10 order-2 lg:order-1 mt-6 lg:mt-0">
                    <div class="inline-flex items-center gap-2 py-1.5 px-3 sm:px-4 rounded-full bg-slate-100 border border-slate-200 mb-4 sm:mb-6">
                        <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                        <span class="text-[10px] sm:text-xs font-bold text-slate-700 uppercase tracking-widest">Sistem SDM Terintegrasi 2026</span>
                    </div>
                    
                    {{-- Responsif Teks: text-3xl (HP) -> 5xl (Tablet) -> 6xl (Laptop) --}}
                    <h1 class="text-3xl sm:text-5xl lg:text-[3.5rem] font-black text-slate-900 leading-[1.1] sm:leading-tight mb-4 sm:mb-6 tracking-tight">
                        Standardisasi Kinerja & <br class="hidden lg:block">
                        <span class="text-indigo-600">Manajemen Pegawai</span> Modern.
                    </h1>
                    
                    <p class="text-sm sm:text-lg text-slate-500 mb-8 leading-relaxed max-w-xl mx-auto lg:mx-0 font-medium">
                        Solusi <i>Human Resource Information System</i> (HRIS) korporasi untuk PT. Happy Momo Indonesia. Otomatisasi absensi presisi tinggi, kalkulasi payroll instan, dan kontrol data terpusat.
                    </p>
                    
                    {{-- TOMBOL AKSI (Responsive) --}}
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center lg:justify-start items-center w-full sm:w-auto">
                        @auth
                            <a href="{{ url('/admin/dashboard') }}" class="w-full sm:w-auto px-8 py-3.5 sm:py-4 bg-indigo-600 text-white rounded-xl sm:rounded-full font-bold text-sm sm:text-base hover:bg-slate-900 transition-colors shadow-lg shadow-indigo-200/50 text-center">
                                Akses Modul Operasional
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3.5 sm:py-4 bg-indigo-600 text-white rounded-xl sm:rounded-full font-bold text-sm sm:text-base hover:bg-slate-900 transition-colors shadow-lg shadow-indigo-200/50 text-center flex items-center justify-center gap-2">
                                <i class="fas fa-shield-alt"></i> Portal Karyawan
                            </a>
                        @endauth
                    </div>
                </div>

                {{-- Image Illustration (Responsive Custom Wrapper) --}}
                <div class="order-1 lg:order-2 z-10 hero-img-wrapper">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-tr from-slate-200 to-indigo-100 rounded-2xl sm:rounded-[2.5rem] transform rotate-3 scale-105 z-0"></div>
                        <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Corporate Meeting" class="relative z-10 rounded-2xl sm:rounded-[2.5rem] shadow-2xl object-cover h-[250px] sm:h-[400px] lg:h-[480px] w-full border border-white/50">
                        
                        {{-- Floating Card 1 (Responsive Scale) --}}
                        <div class="absolute -top-4 -left-2 sm:top-8 sm:-left-8 bg-white p-3 sm:p-4 rounded-xl sm:rounded-2xl shadow-xl z-20 flex items-center gap-2 sm:gap-3 scale-90 sm:scale-100 origin-top-left border border-slate-100">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                <i class="fas fa-file-invoice-dollar text-sm sm:text-base"></i>
                            </div>
                            <div>
                                <p class="text-[9px] sm:text-[10px] text-slate-400 font-extrabold uppercase">Distribusi Payroll</p>
                                <p class="text-xs sm:text-sm font-black text-slate-800">Tervalidasi & Selesai</p>
                            </div>
                        </div>

                        {{-- Floating Card 2 (Responsive Scale) --}}
                        <div class="absolute -bottom-4 -right-2 sm:bottom-12 sm:-right-8 bg-white p-3 sm:p-4 rounded-xl sm:rounded-2xl shadow-xl z-20 flex items-center gap-2 sm:gap-3 scale-90 sm:scale-100 origin-bottom-right border border-slate-100">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <i class="fas fa-fingerprint text-sm sm:text-base"></i>
                            </div>
                            <div>
                                <p class="text-[9px] sm:text-[10px] text-slate-400 font-extrabold uppercase">Kehadiran Operasional</p>
                                <p class="text-xs sm:text-sm font-black text-slate-800">98% Data Sinkron</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- STATS SECTION (Corporate Minimalist) --}}
    <section class="bg-white border-y border-slate-200/60 py-8 sm:py-12 relative z-20 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 sm:gap-8 text-center divide-x-0 md:divide-x divide-slate-100">
                <div class="px-2">
                    <h3 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight">200<span class="text-indigo-600">+</span></h3>
                    <p class="text-[10px] sm:text-xs text-slate-500 font-extrabold uppercase tracking-widest mt-1 sm:mt-2">Total Karyawan</p>
                </div>
                <div class="px-2">
                    <h3 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight">100<span class="text-indigo-600">%</span></h3>
                    <p class="text-[10px] sm:text-xs text-slate-500 font-extrabold uppercase tracking-widest mt-1 sm:mt-2">Akurasi Perhitungan</p>
                </div>
                <div class="px-2">
                    <h3 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight">24<span class="text-indigo-600">/7</span></h3>
                    <p class="text-[10px] sm:text-xs text-slate-500 font-extrabold uppercase tracking-widest mt-1 sm:mt-2">Akses Sistem Aktif</p>
                </div>
                <div class="px-2">
                    <h3 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight">0<span class="text-indigo-600">.0</span></h3>
                    <p class="text-[10px] sm:text-xs text-slate-500 font-extrabold uppercase tracking-widest mt-1 sm:mt-2">Margin Error Data</p>
                </div>
            </div>
        </div>
    </section>

    {{-- FEATURES SECTION (Enterprise Layout) --}}
    <section id="features" class="py-16 sm:py-24 bg-slate-50 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16">
                <span class="text-indigo-600 font-black uppercase tracking-widest text-[10px] sm:text-xs">Modul Utama Sistem</span>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-slate-900 mt-2 tracking-tight">Infrastruktur HR Terpusat</h2>
                <p class="text-sm sm:text-base text-slate-500 mt-3 sm:mt-4">Optimalisasi pengawasan operasional dan administrasi SDM melalui digitalisasi penuh tanpa birokrasi kertas.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
                {{-- Feature 1 --}}
                <div class="bg-white p-6 sm:p-8 rounded-2xl sm:rounded-[2rem] shadow-sm border border-slate-200/60 hover:shadow-xl hover:-translate-y-1 transition duration-300">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-indigo-50 border border-indigo-100 rounded-xl sm:rounded-2xl flex items-center justify-center text-indigo-600 mb-5 sm:mb-6">
                        <i class="fas fa-location-crosshairs text-xl sm:text-2xl"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 mb-2 sm:mb-3">Absensi Geolocation</h3>
                    <p class="text-slate-500 text-xs sm:text-sm leading-relaxed">
                        Pencatatan jam kerja presisi dengan verifikasi titik koordinat GPS dan validasi biometrik/foto wajah karyawan di area tugas.
                    </p>
                </div>
                {{-- Feature 2 --}}
                <div class="bg-white p-6 sm:p-8 rounded-2xl sm:rounded-[2rem] shadow-sm border border-slate-200/60 hover:shadow-xl hover:-translate-y-1 transition duration-300">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-indigo-50 border border-indigo-100 rounded-xl sm:rounded-2xl flex items-center justify-center text-indigo-600 mb-5 sm:mb-6">
                        <i class="fas fa-calculator text-xl sm:text-2xl"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 mb-2 sm:mb-3">Kalkulasi Payroll</h3>
                    <p class="text-slate-500 text-xs sm:text-sm leading-relaxed">
                        Sistem penggajian cerdas. Terintegrasi langsung dengan rekap absensi, perhitungan jam lembur, tunjangan struktural, dan potongan pajak.
                    </p>
                </div>
                {{-- Feature 3 --}}
                <div class="bg-white p-6 sm:p-8 rounded-2xl sm:rounded-[2rem] shadow-sm border border-slate-200/60 hover:shadow-xl hover:-translate-y-1 transition duration-300">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-indigo-50 border border-indigo-100 rounded-xl sm:rounded-2xl flex items-center justify-center text-indigo-600 mb-5 sm:mb-6">
                        <i class="fas fa-folder-tree text-xl sm:text-2xl"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 mb-2 sm:mb-3">Workflow Perizinan</h3>
                    <p class="text-slate-500 text-xs sm:text-sm leading-relaxed">
                        Otomatisasi rantai birokrasi pengajuan cuti, sakit, dan lembur operasional dengan hirarki persetujuan manajerial digital.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- SECTION TENTANG KAMI (Professional Layout) --}}
    <section id="about" class="py-16 sm:py-24 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">
                
                {{-- Kolom Gambar (Kiri) --}}
                <div class="relative w-full max-w-sm sm:max-w-md lg:max-w-none mx-auto lg:mx-0">
                    <div class="relative rounded-2xl sm:rounded-[2rem] overflow-hidden shadow-2xl border border-slate-100">
                        <img src="https://images.unsplash.com/photo-1542744173-8e7e53415bb0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="Corporate Office" 
                             class="w-full h-auto aspect-square lg:aspect-auto lg:h-[450px] object-cover">
                    </div>
                    
                    {{-- Badge Corporate --}}
                    <div class="absolute -bottom-4 -right-4 sm:-bottom-6 sm:-right-6 bg-slate-900 text-white p-4 sm:p-6 rounded-xl sm:rounded-2xl shadow-xl shadow-slate-900/30">
                        <span class="block text-xl sm:text-2xl font-black text-indigo-400">100%</span>
                        <span class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-slate-300">Kepatuhan<br>Regulasi SDM</span>
                    </div>
                </div>

                {{-- Kolom Teks (Kanan) --}}
                <div>
                    <span class="text-indigo-600 font-black uppercase tracking-widest text-[10px] sm:text-xs flex items-center gap-2 mb-3">
                        <span class="w-6 h-0.5 bg-indigo-600"></span> Identitas Korporasi
                    </span>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-slate-900 mb-4 sm:mb-6 leading-tight tracking-tight">
                        Mewujudkan Ekosistem <span class="text-indigo-600">Kerja Profesional</span> Berbasis Data.
                    </h2>
                    <p class="text-sm sm:text-base text-slate-500 mb-6 leading-relaxed text-justify sm:text-left">
                        Sebagai tulang punggung administrasi <strong>PT. Happy Momo Indonesia</strong>, platform ini mendigitalkan seluruh interaksi operasional. Kami berkomitmen menyajikan transparansi data untuk memastikan setiap pegawai mendapatkan haknya dengan tepat, akurat, dan efisien.
                    </p>

                    <div class="space-y-4 mb-8">
                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center shrink-0 mt-1 border border-slate-200">
                                <i class="fas fa-shield-alt text-sm sm:text-base"></i>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-sm sm:text-base text-slate-900">Keamanan Data Sentral</h4>
                                <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">Infrastruktur basis data tersentralisasi dengan hak akses berjenjang yang melindungi privasi finansial tiap karyawan.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FOOTER (Clean Corporate) --}}
    <footer class="bg-slate-900 border-t border-slate-800 pt-16 pb-8 text-slate-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 md:gap-12 mb-12">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 bg-indigo-600 rounded flex items-center justify-center text-white">
                            <i class="fas fa-building text-sm"></i>
                        </div>
                        <h4 class="font-bold text-lg text-white tracking-tight">PT. Happy Momo Indonesia</h4>
                    </div>
                    <p class="text-slate-400 text-xs sm:text-sm leading-relaxed max-w-sm">
                        Enterprise Human Resource Information System terintegrasi. Dirancang untuk kontrol manajerial dan efisiensi operasional pegawai.
                    </p>
                </div>
                
                <div>
                    <h5 class="font-bold text-white mb-4 text-sm tracking-wide">Navigasi Cepat</h5>
                    <ul class="space-y-3 text-xs sm:text-sm text-slate-400">
                        <li><a href="#home" class="hover:text-indigo-400 transition-colors"><i class="fas fa-chevron-right text-[8px] mr-2"></i>Beranda Portal</a></li>
                        <li><a href="#features" class="hover:text-indigo-400 transition-colors"><i class="fas fa-chevron-right text-[8px] mr-2"></i>Modul Sistem</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-indigo-400 transition-colors"><i class="fas fa-chevron-right text-[8px] mr-2"></i>Login Karyawan</a></li>
                    </ul>
                </div>
                
                <div>
                    <h5 class="font-bold text-white mb-4 text-sm tracking-wide">Informasi Kontak</h5>
                    <ul class="space-y-3 text-xs sm:text-sm text-slate-400">
                        <li class="flex items-start gap-3"><i class="fas fa-map-marker-alt text-indigo-500 mt-1"></i> <span class="leading-relaxed">Margomulyo Permai DD-III No.19, Surabaya</span></li>
                        <li class="flex items-center gap-3"><i class="fas fa-envelope text-indigo-500"></i> happymomoindonesia@gmail.com</li>
                        <li class="flex items-center gap-3"><i class="fas fa-phone text-indigo-500"></i> +62 813-3337-0421</li>
                    </ul>
                </div>
            </div>

            {{-- CREDIT & PORTFOLIO SECTION --}}
            <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-6">
                <p class="text-[10px] sm:text-xs text-slate-500 font-semibold uppercase tracking-widest text-center md:text-left">
                    &copy; {{ date('Y') }} PT. Happy Momo Indonesia. Hak Cipta Dilindungi.
                </p>
                
                {{-- BADGE DEVELOPER --}}
                <div class="inline-flex items-center gap-3 sm:gap-4 bg-slate-800/50 px-4 sm:px-6 py-2.5 sm:py-3 rounded-xl border border-slate-700/50">
                    <span class="text-[8px] sm:text-[9px] font-black text-slate-500 uppercase tracking-widest border-r border-slate-700 pr-3 sm:pr-4">System Builder</span>
                    <a href="https://www.linkedin.com/in/rifqi-yudo-dewantoro-527b4b253" target="_blank" class="flex items-center gap-2 sm:gap-3 group">
                        <div class="text-left">
                            <p class="text-[10px] sm:text-xs font-black text-slate-300 uppercase leading-none group-hover:text-indigo-400 transition-colors">Rifqi Yudo Dewantoro</p>
                            <p class="text-[8px] sm:text-[9px] text-slate-500 font-bold tracking-tight mt-0.5">Software Developer</p>
                        </div>
                        <div class="w-6 h-6 sm:w-8 sm:h-8 rounded bg-slate-900 flex items-center justify-center text-slate-400 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <i class="fab fa-linkedin-in text-xs sm:text-sm"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>