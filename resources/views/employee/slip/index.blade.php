<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji Saya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; } </style>
</head>
<body class="bg-gray-100 text-slate-800 pb-24">

    <div class="bg-blue-600 px-6 pt-8 pb-10 rounded-b-[30px] shadow-lg sticky top-0 z-10">
        <div class="flex items-center gap-4">
            <a href="{{ route('employee.dashboard') }}" class="text-white hover:bg-white/20 p-2 rounded-full transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-xl font-bold text-white">Slip Gaji Saya</h1>
        </div>
    </div>

    <div class="px-6 -mt-6 relative z-20">
        
        @forelse($payrolls as $slip)
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 mb-4 hover:shadow-md transition">
            <div class="flex justify-between items-start mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-full flex items-center justify-center text-lg">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <p class="font-bold text-slate-700">Gaji Bulan {{ $slip->month }}</p> <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($slip->created_at)->format('d M Y') }}</p>
                    </div>
                </div>
                <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-1 rounded-full uppercase">
                    Lunas
                </span>
            </div>
            
            <div class="border-t border-gray-100 my-3 pt-3 flex justify-between items-center">
                <div>
                    <p class="text-[10px] text-gray-400 uppercase font-bold">Total Diterima (THP)</p>
                    <p class="text-lg font-bold text-slate-800">Rp {{ number_format($slip->net_salary, 0, ',', '.') }}</p>
                </div>
                <a href="{{ route('employee.slip.pdf', $slip->id) }}" class="bg-indigo-50 text-indigo-600 p-2 rounded-lg text-sm font-bold hover:bg-indigo-100 transition" title="Lihat Detail">
                    <i class="fas fa-download"></i>
                </a>
            </div>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center py-10 mt-10">
            <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mb-4 text-gray-400 text-4xl">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <h3 class="font-bold text-gray-600">Belum Ada Slip Gaji</h3>
            <p class="text-xs text-gray-400 text-center max-w-xs mt-1">Slip gaji akan muncul di sini setelah Admin menerbitkan penggajian periode ini.</p>
        </div>
        @endforelse

    </div>

    <div class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 px-6 py-3 flex justify-between items-center z-50 shadow-[0_-5px_10px_rgba(0,0,0,0.02)]">
        <a href="{{ route('employee.dashboard') }}" class="flex flex-col items-center gap-1 text-gray-400 hover:text-blue-600 transition">
            <i class="fas fa-home text-xl"></i><span class="text-[10px] font-bold">Home</span>
        </a>
        <a href="#" class="flex flex-col items-center gap-1 text-gray-400 hover:text-blue-600 transition">
            <i class="fas fa-user text-xl"></i><span class="text-[10px] font-bold">Profile</span>
        </a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="flex flex-col items-center gap-1 text-gray-400 hover:text-red-600 transition">
                <i class="fas fa-power-off text-xl"></i><span class="text-[10px] font-bold">Logout</span>
            </button>
        </form>
    </div>

</body>
</html>