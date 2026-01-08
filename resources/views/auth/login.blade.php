<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HRIS Happy Momo Indonesia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        
        /* 1. Animasi Background Gradient Bergerak */
        .bg-animate-gradient {
            background: linear-gradient(-45deg, #f8fafc, #fff7ed, #fed7aa, #f1f5f9);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* 2. Animasi Floating Blobs (Bola Mengambang) */
        .blob {
            position: absolute;
            filter: blur(40px);
            z-index: 0;
            opacity: 0.6;
            animation: float 10s infinite ease-in-out;
        }
        .blob-1 { top: -10%; left: -10%; width: 300px; height: 300px; background: #fdba74; animation-delay: 0s; }
        .blob-2 { bottom: -10%; right: -10%; width: 350px; height: 350px; background: #fed7aa; animation-delay: 2s; }
        .blob-3 { top: 40%; left: 40%; width: 200px; height: 200px; background: #e2e8f0; animation-delay: 4s; opacity: 0.4; }

        @keyframes float {
            0% { transform: translateY(0px) scale(1); }
            33% { transform: translateY(-30px) scale(1.1); }
            66% { transform: translateY(10px) scale(0.9); }
            100% { transform: translateY(0px) scale(1); }
        }

        /* Shadow Glow Orange */
        .shadow-orange-glow { box-shadow: 0 10px 30px -5px rgba(249, 115, 22, 0.3); }
    </style>
</head>
<body class="bg-animate-gradient h-screen flex justify-center items-center relative overflow-hidden">

    <div class="blob blob-1 rounded-full"></div>
    <div class="blob blob-2 rounded-full"></div>
    <div class="blob blob-3 rounded-full"></div>

    <div class="bg-white/90 backdrop-blur-sm p-10 rounded-2xl shadow-2xl w-full max-w-md border-t-4 border-orange-500 relative z-10 transform transition-all hover:scale-[1.01] duration-500">
        
        <div class="text-center mb-8">
            <div class="mb-6 flex justify-center">
                <img src="{{ asset('logo.png') }}" alt="Logo Happy Momo" class="h-16 object-contain drop-shadow-md animate-[bounce_3s_infinite]">
            </div>

            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">HRIS SYSTEM</h1>
            <p class="text-slate-500 text-sm mt-1 font-medium">PT. Happy Momo Indonesia</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6 text-sm shadow-sm animate-[pulse_1s_ease-in-out]">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6 text-sm shadow-sm animate-[pulse_1s_ease-in-out]">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST" class="space-y-5">
            @csrf
            <div class="group">
                <label class="block text-slate-700 text-xs font-bold uppercase tracking-wide mb-2 group-focus-within:text-orange-600 transition-colors">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-orange-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <input type="email" name="email" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all text-slate-800 placeholder-slate-400 text-sm shadow-inner" placeholder="user@happymomo.com" required>
                </div>
            </div>
            
            <div class="group">
                <label class="block text-slate-700 text-xs font-bold uppercase tracking-wide mb-2 group-focus-within:text-orange-600 transition-colors">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-orange-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input type="password" name="password" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all text-slate-800 placeholder-slate-400 text-sm shadow-inner" placeholder="••••••••" required>
                </div>
            </div>
            
            <div class="pt-4">
                <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-amber-600 text-white font-bold py-3 px-4 rounded-lg hover:from-orange-600 hover:to-amber-700 shadow-orange-glow transition-all duration-300 transform hover:-translate-y-1 active:scale-95 flex justify-center items-center space-x-2">
                    <span>MASUK SISTEM</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('register') }}" class="text-xs text-slate-500 hover:text-orange-600 transition-colors">
                Karyawan baru? <span class="font-bold underline">Daftar disini</span>
            </a>
        </div>
        
        <div class="mt-6 pt-6 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-400">
                &copy; {{ date('Y') }} PT. Happy Momo Indonesia.<br>All rights reserved.
            </p>
        </div>
    </div>

</body>
</html>