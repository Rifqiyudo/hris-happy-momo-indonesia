<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - HRIS Happy Momo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-slate-800">

    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-64 bg-white border-r border-gray-200 hidden md:flex flex-col">
            <div class="p-6 flex items-center gap-3">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold">H</div>
                <div>
                    <h1 class="font-bold text-sm text-slate-900 leading-tight">HRIS PT. Happy<br>Momo Indonesia</h1>
                </div>
            </div>

            <nav class="flex-1 px-4 space-y-1 overflow-y-auto">
                <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-4">Menu Utama</p>
                
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-gray-50' }}">
                    <i class="fas fa-home w-6"></i> Dashboard
                </a>

                <a href="{{ route('admin.master.user.index') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.master.user.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-gray-50' }}">
                    <i class="fas fa-users w-6"></i> Data Karyawan
                </a>

                <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-6">Master Data</p>

                <a href="{{ route('admin.master.shift.index') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.master.shift.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-gray-50' }}">
                    <i class="fas fa-clock w-6"></i> Jadwal Shift
                </a>
                <a href="{{ route('admin.master.allowance.index') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.master.allowance.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-gray-50' }}">
                    <i class="fas fa-wallet w-6"></i> Tunjangan
                </a>

                <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-6">Keuangan & Shift</p>

                <a href="{{ route('admin.transaksi.attendance.index') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.transaksi.attendance.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-gray-50' }}">
                    <i class="fas fa-calendar-check w-6"></i> Monitoring Absen
                </a>
                <a href="{{ route('admin.transaksi.payroll.index') }}" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.transaksi.payroll.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-gray-50' }}">
                    <i class="fas fa-money-bill-wave w-6"></i> Penggajian
                </a>
            </nav>

            <div class="p-4 border-t border-gray-100">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-2.5 text-red-600 hover:bg-red-50 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-sign-out-alt w-6"></i> Keluar
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-8">
                <h2 class="text-xl font-bold text-slate-800">@yield('title', 'Overview')</h2>
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-sm font-bold text-slate-800">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-500">Administrator</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-8">
                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
                        <p class="font-bold">Sukses</p>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

</body>
</html>