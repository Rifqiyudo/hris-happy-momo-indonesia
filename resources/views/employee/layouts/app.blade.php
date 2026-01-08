<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HRIS Mobile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
    @stack('styles')
</head>
<body class="bg-gray-50 pb-20"> @yield('content')

    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg pb-safe z-50">
        <div class="flex justify-around items-center h-16">
            
            <a href="{{ route('employee.dashboard') }}" class="flex flex-col items-center w-full h-full justify-center {{ request()->routeIs('employee.dashboard') ? 'text-blue-600' : 'text-gray-400 hover:text-blue-500' }}">
                <i class="fas fa-home text-xl mb-1"></i>
                <span class="text-[10px] font-medium">Beranda</span>
            </a>

            <div class="relative -top-6">
                <a href="{{ route('employee.attendance.index') }}" class="flex items-center justify-center w-14 h-14 bg-blue-600 rounded-full text-white shadow-lg border-4 border-gray-50 transform transition hover:scale-105">
                    <i class="fas fa-fingerprint text-2xl"></i>
                </a>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="flex flex-col items-center w-full h-full justify-center">
                @csrf
                <button type="submit" class="flex flex-col items-center text-gray-400 hover:text-red-500">
                    <i class="fas fa-sign-out-alt text-xl mb-1"></i>
                    <span class="text-[10px] font-medium">Keluar</span>
                </button>
            </form>

        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>