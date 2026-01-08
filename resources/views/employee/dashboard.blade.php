<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Portal Karyawan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        body { font-family: 'Poppins', sans-serif; }
        /* Hide Scrollbar */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-slate-50 pb-28">

    <div class="relative bg-gradient-to-br from-blue-600 to-indigo-700 pb-16 pt-8 px-6 rounded-b-[40px] shadow-xl overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-white opacity-5 rounded-full -mr-10 -mt-10"></div>
        <div class="absolute bottom-0 left-0 w-20 h-20 bg-white opacity-5 rounded-full -ml-5 -mb-5"></div>

        <div class="flex justify-between items-center relative z-10">
            <div>
                <p class="text-blue-100 text-sm font-medium">Selamat Pagi,</p>
                <h1 class="text-2xl font-bold text-white mt-1">{{ explode(' ', Auth::user()->name)[0] }} 👋</h1>
            </div>
            <div class="relative">
                <img src="{{ Auth::user()->avatar_url }}" class="w-12 h-12 rounded-full border-2 border-white/50 shadow-md">
                <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 border-2 border-white rounded-full"></span>
            </div>
        </div>

        <div class="mt-8 bg-white/10 backdrop-blur-md border border-white/20 p-5 rounded-2xl text-white relative overflow-hidden">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-blue-100 uppercase tracking-wider mb-1">Jadwal Shift Hari Ini</p>
                    <div class="flex items-baseline">
                         @if($todayShift && !$todayShift->is_holiday)
                            <span class="text-3xl font-bold font-mono">{{ \Carbon\Carbon::parse($todayShift->clock_in)->format('H:i') }}</span>
                            <span class="mx-2 opacity-60">-</span>
                            <span class="text-xl opacity-90 font-mono">{{ \Carbon\Carbon::parse($todayShift->clock_out)->format('H:i') }}</span>
                        @else
                             <span class="text-2xl font-bold text-red-100">LIBUR</span>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd') }}</p>
                    <p class="text-xs text-blue-100">{{ \Carbon\Carbon::now()->format('d M Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 -mt-8 relative z-20">
        
        <div class="bg-white rounded-3xl shadow-xl p-6 mb-6">
            <h3 class="text-slate-800 font-bold mb-4 text-sm">Status Kehadiran</h3>
            <div class="grid grid-cols-2 gap-4">
                <button class="group relative overflow-hidden bg-emerald-50 hover:bg-emerald-500 transition-all duration-300 rounded-2xl p-5 text-center border border-emerald-100 hover:border-emerald-500 hover:shadow-lg hover:shadow-emerald-200">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-emerald-500 mx-auto mb-3 shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fas fa-camera text-xl"></i>
                    </div>
                    <span class="block font-bold text-emerald-700 group-hover:text-white">Absen Masuk</span>
                    <span class="text-xs text-emerald-400 group-hover:text-emerald-100">Tap untuk foto</span>
                </button>

                <button class="group relative overflow-hidden bg-rose-50 hover:bg-rose-500 transition-all duration-300 rounded-2xl p-5 text-center border border-rose-100 hover:border-rose-500 hover:shadow-lg hover:shadow-rose-200 opacity-60"> <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-rose-500 mx-auto mb-3 shadow-sm">
                        <i class="fas fa-sign-out-alt text-xl"></i>
                    </div>
                    <span class="block font-bold text-rose-700 group-hover:text-white">Absen Pulang</span>
                    <span class="text-xs text-rose-400 group-hover:text-rose-100">Selesaikan shift</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <a href="#" class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex flex-col items-center justify-center active:scale-95 transition-transform">
                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mb-2">
                    <i class="fas fa-history"></i>
                </div>
                <span class="text-xs font-semibold text-slate-600">Riwayat</span>
            </a>
            <a href="#" class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex flex-col items-center justify-center active:scale-95 transition-transform">
                <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mb-2">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <span class="text-xs font-semibold text-slate-600">Slip Gaji</span>
            </a>
            <a href="#" class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex flex-col items-center justify-center active:scale-95 transition-transform">
                <div class="w-10 h-10 bg-teal-50 text-teal-600 rounded-full flex items-center justify-center mb-2">
                    <i class="fas fa-user-check"></i>
                </div>
                <span class="text-xs font-semibold text-slate-600">Izin/Cuti</span>
            </a>
        </div>
    </div>

    <nav class="fixed bottom-6 left-6 right-6 bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/50 px-6 py-4 flex justify-between items-center z-50">
        <a href="#" class="flex flex-col items-center text-blue-600 space-y-1">
            <i class="fas fa-home text-xl"></i>
            <span class="text-[10px] font-bold">Home</span>
        </a>
        <a href="#" class="flex flex-col items-center text-slate-400 hover:text-slate-600 space-y-1">
            <i class="fas fa-user text-xl"></i>
            <span class="text-[10px] font-medium">Profil</span>
        </a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="flex flex-col items-center text-rose-400 hover:text-rose-600 space-y-1">
                <i class="fas fa-power-off text-xl"></i>
                <span class="text-[10px] font-medium">Keluar</span>
            </button>
        </form>
    </nav>

</body>
</html>