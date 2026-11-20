<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    {{-- Viewport Optimasi: Mencegah zoom otomatis di iPhone saat fokus input --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#4f46e5">
    
    <title>@yield('title', 'Employee HRIS') - PT Happy Momo Indonesia</title>
    
    {{-- Favicon --}}
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    {{-- Load CSS & JS via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Font Awesome & Google Fonts (Inter) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">

    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            -webkit-tap-highlight-color: transparent; 
            overscroll-behavior-y: contain;
        }
        
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Animasi Transisi Halaman */
        .page-fade { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Loading Overlay Style */
        #loader {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 3px;
            background: #e2e8f0; z-index: 9999;
        }
        #loader-bar {
            width: 0%; height: 100%;
            background: #4f46e5;
            transition: width 0.3s ease;
        }

        @media print {
            body { background-color: white !important; }
            nav, .bottom-nav, .btn-float { display: none !important; } 
            main { 
                box-shadow: none !important; 
                max-width: 100% !important; 
                background-color: white !important;
                margin: 0 !important; padding: 0 !important;
            }
        }
    </style>
    @yield('styles')
</head>
<body class="bg-gray-100 text-slate-800 antialiased pb-24 print:pb-0 print:bg-white"> 

    {{-- Progress Bar saat pindah halaman --}}
    <div id="loader"><div id="loader-bar"></div></div>

    {{-- FLASH DATA UNTUK SWEETALERT & ERROR VALIDATION --}}
    <div id="flash-data" 
         data-success="{{ session('success') }}" 
         data-error="{{ session('error') }}" 
         data-info="{{ session('info') }}"
         data-any-error="{{ $errors->any() ? $errors->first() : '' }}">
    </div>

    {{-- KONTEN UTAMA --}}
    <main class="relative max-w-md mx-auto bg-gray-50 shadow-2xl min-h-screen page-fade print:max-w-none print:shadow-none print:bg-white">
        @yield('content')
    </main>

    {{-- NAVIGASI BAWAH --}}
    <nav class="bottom-nav fixed bottom-0 left-0 w-full z-50 bg-white/80 backdrop-blur-lg border-t border-gray-100 shadow-[0_-5px_15px_rgba(0,0,0,0.05)] print:hidden">
        <div class="max-w-md mx-auto flex justify-around items-center px-2 py-3">
            
            <a href="{{ route('employee.dashboard') }}" 
               class="flex flex-col items-center gap-1 transition-all duration-300 w-16 {{ request()->routeIs('employee.dashboard') ? 'text-indigo-600' : 'text-gray-400' }}">
                <i class="fas fa-home text-xl"></i>
                <span class="text-[10px] font-bold">Beranda</span>
            </a>

            <a href="{{ route('employee.history.index') }}" 
               class="flex flex-col items-center gap-1 transition-all duration-300 w-16 {{ request()->routeIs('employee.history.*') ? 'text-indigo-600' : 'text-gray-400' }}">
                <i class="fas fa-file-invoice-dollar text-xl"></i>
                <span class="text-[10px] font-bold">Riwayat</span>
            </a>

            <div class="relative -top-7">
                <a href="{{ route('employee.attendance.create') }}" 
                   class="w-14 h-14 bg-gradient-to-tr from-indigo-600 to-indigo-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-200 transform active:scale-90 transition-all border-4 border-white">
                    <i class="fas fa-fingerprint text-2xl"></i>
                </a>
            </div>

            <a href="{{ route('employee.reimbursement.index') }}" 
               class="flex flex-col items-center gap-1 transition-all duration-300 w-16 {{ request()->routeIs('employee.reimbursement.*') ? 'text-indigo-600' : 'text-gray-400' }}">
                <i class="fas fa-receipt text-xl"></i>
                <span class="text-[10px] font-bold">Klaim</span>
            </a>

            <a href="{{ route('employee.profile.index') }}" 
               class="flex flex-col items-center gap-1 transition-all duration-300 w-16 {{ request()->routeIs('employee.profile.*') ? 'text-indigo-600' : 'text-gray-400' }}">
                <i class="fas fa-user-circle text-xl"></i>
                <span class="text-[10px] font-bold">Profil</span>
            </a>

        </div>
    </nav>

    {{-- SCRIPTS GLOBAL --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const flashData = document.getElementById('flash-data');
            const successMsg = flashData.getAttribute('data-success');
            const errorMsg = flashData.getAttribute('data-error');
            const infoMsg = flashData.getAttribute('data-info');
            const anyError = flashData.getAttribute('data-any-error');

            const Toast = Swal.mixin({
                toast: true,
                position: 'top',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });

            if (successMsg) Toast.fire({ icon: 'success', title: successMsg });
            
            // Tampilkan error dari session ATAU dari validasi form laravel ($errors)
            if (errorMsg || anyError) {
                Swal.fire({ 
                    icon: 'error', 
                    title: 'Terjadi Kesalahan', 
                    text: errorMsg || anyError, 
                    confirmButtonColor: '#4f46e5',
                    customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl px-10' }
                });
            }

            if (infoMsg) {
                Swal.fire({ 
                    icon: 'info', title: 'Informasi', text: infoMsg, 
                    confirmButtonColor: '#4f46e5', customClass: { popup: 'rounded-3xl' }
                });
            }

            // Haptic feedback
            if ('vibrate' in navigator) {
                document.querySelectorAll('.bottom-nav a').forEach(btn => {
                    btn.addEventListener('click', () => navigator.vibrate(10));
                });
            }

            // Loading Bar Logic
            window.onbeforeunload = function() {
                document.getElementById('loader').style.display = 'block';
                document.getElementById('loader-bar').style.width = '70%';
            };
        });
    </script>
    
    @yield('scripts')
</body>
</html>