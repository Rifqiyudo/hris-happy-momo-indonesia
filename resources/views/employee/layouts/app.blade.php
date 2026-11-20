<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" id="themeColorMeta" content="#4f46e5">
    <meta name="color-scheme" content="light dark">
    
    {{-- PWA Meta Tags --}}
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    
    <title>@yield('title', 'Employee HRIS') - PT Happy Momo Indonesia</title>

    {{-- [BARU] Anti-FOUC Theme Loader: Membaca Mode Terang / Malam sebelum halaman dirender --}}
    <script>
        (function () {
            const savedTheme = localStorage.getItem('employee_theme') || localStorage.getItem('theme') || 'light';
            const isDark = savedTheme === 'dark';
            if (isDark) {
                document.documentElement.classList.add('dark');
                document.documentElement.setAttribute('data-theme', 'dark');
                const metaTheme = document.getElementById('themeColorMeta');
                if (metaTheme) metaTheme.setAttribute('content', '#020617');
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.setAttribute('data-theme', 'light');
                const metaTheme = document.getElementById('themeColorMeta');
                if (metaTheme) metaTheme.setAttribute('content', '#4f46e5');
            }
        })();
    </script>
    
    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Font Awesome & Google Fonts (Inter) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; -webkit-tap-highlight-color: transparent; overscroll-behavior-y: contain; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        .page-fade { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

        /* Loading Overlay Elegan */
        #loader-overlay { display: none; position: fixed; inset: 0; background: rgba(255,255,255,0.9); z-index: 9999; flex-direction: column; align-items: center; justify-content: center; backdrop-filter: blur(4px); }
        .spinner { width: 40px; height: 40px; border: 4px solid #e2e8f0; border-top: 4px solid #4f46e5; border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        /* [BARU] Fix Konflik Font Awesome (.fas) vs Tailwind (.hidden) pada Ikon Switcher */
        .theme-hidden { display: none !important; }

        /* ========================================================================= */
        /* [BARU] UNIVERSAL LIGHT MODE GUARD (Menetralkan Paksaan OS Dark Mode)      */
        /* ========================================================================= */
        html:not(.dark) { color-scheme: light; }
        html:not(.dark) body { background-color: #f3f4f6 !important; color: #1e293b !important; }
        html:not(.dark) main,
        html:not(.dark) .dark\:bg-slate-950 { background-color: #f8fafc !important; }
        html:not(.dark) .dark\:bg-slate-900 { background-color: #ffffff !important; }
        html:not(.dark) .dark\:bg-slate-900\/95 { background-color: rgba(255, 255, 255, 0.92) !important; }
        html:not(.dark) .dark\:bg-slate-800 { background-color: #f1f5f9 !important; }
        html:not(.dark) .dark\:border-slate-800,
        html:not(.dark) .dark\:border-slate-900 { border-color: #f1f5f9 !important; }
        html:not(.dark) .dark\:border-slate-950 { border-color: #ffffff !important; }
        html:not(.dark) .dark\:text-slate-100,
        html:not(.dark) .dark\:text-slate-200 { color: #1e293b !important; }
        html:not(.dark) .dark\:text-slate-300 { color: #475569 !important; }
        html:not(.dark) .dark\:text-white:not(.bg-indigo-600):not(.bg-indigo-500):not(.bg-rose-500):not(.bg-emerald-500):not([class*="bg-gradient-"]) {
            color: #1e293b !important;
        }
        html:not(.dark) [class*="bg-gradient-"] .dark\:text-white,
        html:not(.dark) .bg-indigo-600 .dark\:text-white,
        html:not(.dark) .bg-rose-500 .dark\:text-white {
            color: #ffffff !important;
        }
        html:not(.dark) a.dark\:bg-slate-950\/90 {
            background-color: #ffffff !important;
            color: #4f46e5 !important;
        }

        /* ========================================================================= */
        /* [BARU] UNIVERSAL DARK MODE ENGINE UNTUK SELURUH HALAMAN PORTAL KARYAWAN   */
        /* ========================================================================= */
        html.dark { color-scheme: dark; }
        html.dark body { background-color: #000000 !important; color: #f1f5f9 !important; }
        html.dark main { background-color: #020617 !important; color: #f1f5f9 !important; border-color: #0f172a !important; }

        /* Adaptasi otomatis permukaan kartu & latar terang di semua sub-halaman */
        html.dark .bg-white:not([class*="bg-gradient-"]),
        html.dark .bg-white\/90,
        html.dark .bg-white\/95 {
            background-color: #0f172a !important;
        }
        html.dark .bg-gray-50,
        html.dark .bg-slate-50,
        html.dark .bg-slate-50\/50,
        html.dark .bg-gray-100,
        html.dark .bg-slate-100 {
            background-color: #020617 !important;
        }
        html.dark .bg-slate-200 {
            background-color: #1e293b !important;
        }

        /* Adaptasi border halus di semua halaman */
        html.dark .border-white,
        html.dark .border-gray-50,
        html.dark .border-gray-100,
        html.dark .border-gray-200,
        html.dark .border-slate-50,
        html.dark .border-slate-100,
        html.dark .border-slate-200 {
            border-color: #1e293b !important;
        }

        /* Adaptasi tipografi gelap ke terang di semua sub-halaman */
        html.dark .text-slate-900,
        html.dark .text-slate-800,
        html.dark .text-gray-900,
        html.dark .text-gray-800 {
            color: #f8fafc !important;
        }
        html.dark .text-slate-700,
        html.dark .text-gray-700 {
            color: #e2e8f0 !important;
        }
        html.dark .text-slate-600,
        html.dark .text-gray-600 {
            color: #cbd5e1 !important;
        }
        html.dark .text-slate-500,
        html.dark .text-gray-500 {
            color: #94a3b8 !important;
        }

        /* Adaptasi form input, select, dan textarea di sub-halaman (Izin, Lembur, Klaim, Profil) */
        html.dark input:not([type="checkbox"]):not([type="radio"]):not([type="file"]),
        html.dark select,
        html.dark textarea {
            background-color: #020617 !important;
            border-color: #1e293b !important;
            color: #f8fafc !important;
        }
        html.dark input::placeholder,
        html.dark textarea::placeholder {
            color: #64748b !important;
        }

        /* Hilangkan pendaran bayangan terang di mode gelap */
        html.dark .shadow-indigo-100,
        html.dark .shadow-indigo-200,
        html.dark .shadow-slate-200 {
            --tw-shadow-color: rgba(30, 27, 75, 0.7) !important;
        }

        /* Loading Overlay & SweetAlert2 Dark Mode */
        html.dark #loader-overlay {
            background: rgba(2, 6, 23, 0.88);
        }
        html.dark .spinner {
            border-color: #1e293b;
            border-top-color: #6366f1;
        }
        html.dark .swal2-popup {
            background: #0f172a !important;
            color: #f1f5f9 !important;
            border: 1px solid #1e293b !important;
            border-radius: 1.5rem !important;
        }
        html.dark .swal2-title {
            color: #ffffff !important;
        }
        html.dark .swal2-html-container {
            color: #94a3b8 !important;
        }
        html.dark .swal2-timer-progress-bar {
            background: #6366f1 !important;
        }

        @media print {
            body { background-color: white !important; color: #1e293b !important; }
            nav, .bottom-nav { display: none !important; } 
            main { box-shadow: none !important; max-width: 100% !important; background-color: white !important; margin: 0 !important; padding: 0 !important; }
        }
    </style>
    @yield('styles')
</head>
<body class="bg-gray-100 dark:bg-black text-slate-800 dark:text-slate-100 antialiased pb-24 print:pb-0 print:bg-white transition-colors duration-300"> 

    {{-- Loading Overlay --}}
    <div id="loader-overlay"><div class="spinner"></div><p class="mt-4 text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest">Memproses...</p></div>

    <div id="flash-data" 
         data-success="{{ session('success') }}" 
         data-error="{{ session('error') }}" 
         data-info="{{ session('info') }}"
         data-any-error="{{ $errors->any() ? $errors->first() : '' }}">
    </div>

    <main class="relative max-w-md mx-auto bg-gray-50 dark:bg-slate-950 shadow-2xl dark:shadow-none dark:border-x dark:border-slate-900 min-h-screen page-fade print:max-w-none print:shadow-none print:bg-white transition-colors duration-300">
        @yield('content')
    </main>

    <nav class="bottom-nav fixed bottom-0 left-0 w-full z-50 bg-white/90 dark:bg-slate-900/95 backdrop-blur-lg border-t border-gray-100 dark:border-slate-800 shadow-[0_-5px_15px_rgba(0,0,0,0.05)] dark:shadow-none print:hidden transition-colors duration-300">
        <div class="max-w-md mx-auto flex justify-around items-center px-2 py-3">
            <a href="{{ route('employee.dashboard') }}" class="flex flex-col items-center gap-1 w-16 transition-colors {{ request()->routeIs('employee.dashboard') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-slate-500 hover:text-indigo-500 dark:hover:text-slate-300' }}">
                <i class="fas fa-home text-xl"></i><span class="text-[10px] font-bold">Beranda</span>
            </a>
            <a href="{{ route('employee.chat.index') }}" class="flex flex-col items-center gap-1 w-16 transition-colors {{ request()->routeIs('employee.chat.*') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-slate-500 hover:text-indigo-500 dark:hover:text-slate-300' }}">
                <i class="fas fa-comment-dots text-xl"></i><span class="text-[10px] font-bold">Pesan</span>
            </a>
            <div class="relative -top-7">
                <a href="{{ route('employee.attendance.create') }}" class="w-14 h-14 bg-gradient-to-tr from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-purple-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-200 dark:shadow-indigo-950/90 active:scale-90 transition-all border-4 border-white dark:border-slate-950">
                    <i class="fas fa-fingerprint text-2xl"></i>
                </a>
            </div>
            <a href="{{ route('employee.history.index') }}" class="flex flex-col items-center gap-1 w-16 transition-colors {{ request()->routeIs('employee.history.*') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-slate-500 hover:text-indigo-500 dark:hover:text-slate-300' }}">
                <i class="fas fa-file-invoice-dollar text-xl"></i><span class="text-[10px] font-bold">Riwayat</span>
            </a>
            <a href="{{ route('employee.profile.index') }}" class="flex flex-col items-center gap-1 w-16 transition-colors {{ request()->routeIs('employee.profile.*') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-slate-500 hover:text-indigo-500 dark:hover:text-slate-300' }}">
                <i class="fas fa-user-circle text-xl"></i><span class="text-[10px] font-bold">Profil</span>
            </a>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // [BARU] Kontroler Global Mode Terang & Mode Malam Portal Karyawan
        window.syncEmployeeThemeUI = function () {
            const isDark = document.documentElement.classList.contains('dark');
            document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');

            const metaTheme = document.getElementById('themeColorMeta');
            if (metaTheme) {
                metaTheme.setAttribute('content', isDark ? '#020617' : '#4f46e5');
            }

            // Tampilkan Matahari saat Dark Mode (untuk balik ke Terang), tampilkan Bulan saat Light Mode
            document.querySelectorAll('.theme-icon-sun').forEach(el => {
                el.classList.toggle('theme-hidden', !isDark);
                el.style.display = isDark ? 'inline-block' : 'none';
            });
            document.querySelectorAll('.theme-icon-moon').forEach(el => {
                el.classList.toggle('theme-hidden', isDark);
                el.style.display = isDark ? 'none' : 'inline-block';
            });
        };

        window.toggleEmployeeDarkMode = function (event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            const html = document.documentElement;
            const willBeDark = !html.classList.contains('dark');
            html.classList.toggle('dark', willBeDark);
            localStorage.setItem('employee_theme', willBeDark ? 'dark' : 'light');
            localStorage.setItem('theme', willBeDark ? 'dark' : 'light');
            window.syncEmployeeThemeUI();
        };

        // Registrasi Service Worker untuk PWA
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(console.error);
        }

        // [BARU] Reset Loader Overlay jika pengguna menekan tombol Back (bfcache)
        window.addEventListener('pageshow', function () {
            const loader = document.getElementById('loader-overlay');
            if (loader) loader.style.display = 'none';
            window.syncEmployeeThemeUI();
        });

        document.addEventListener('DOMContentLoaded', function() {
            // [BARU] Sinkronisasi ikon tema segera saat DOM siap
            window.syncEmployeeThemeUI();

            // Loading Overlay saat form submit
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', () => {
                    document.getElementById('loader-overlay').style.display = 'flex';
                });
            });

            // Flash Data Handling
            const flashData = document.getElementById('flash-data');
            const successMsg = flashData.getAttribute('data-success');
            const errorMsg = flashData.getAttribute('data-error');
            const anyError = flashData.getAttribute('data-any-error');

            const Toast = Swal.mixin({ toast: true, position: 'top', showConfirmButton: false, timer: 3000, timerProgressBar: true });

            if (successMsg) Toast.fire({ icon: 'success', title: successMsg });
            if (errorMsg || anyError) {
                Swal.fire({ icon: 'error', title: 'Perhatian!', text: errorMsg || anyError, confirmButtonColor: '#4f46e5' });
            }

            if ('vibrate' in navigator) {
                document.querySelectorAll('.bottom-nav a').forEach(btn => {
                    btn.addEventListener('click', () => navigator.vibrate(10));
                });
            }
        });
    </script>
    @yield('scripts')
</body>
</html>