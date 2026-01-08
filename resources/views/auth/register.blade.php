<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - HRIS PT. Happy Momo Indonesia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        
        /* Animasi Background & Blob (Sama seperti Login) */
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
        .blob {
            position: absolute;
            filter: blur(40px);
            z-index: 0;
            opacity: 0.6;
            animation: float 10s infinite ease-in-out;
        }
        .blob-1 { top: -10%; left: -10%; width: 300px; height: 300px; background: #fdba74; }
        .blob-2 { bottom: -10%; right: -10%; width: 350px; height: 350px; background: #fed7aa; animation-delay: 2s; }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .shadow-orange-glow { box-shadow: 0 10px 30px -5px rgba(249, 115, 22, 0.3); }
    </style>
</head>
<body class="bg-animate-gradient min-h-screen flex justify-center items-center relative overflow-hidden py-10">

    <div class="blob blob-1 rounded-full"></div>
    <div class="blob blob-2 rounded-full"></div>

    <div class="bg-white/90 backdrop-blur-sm p-8 rounded-2xl shadow-2xl w-full max-w-lg border-t-4 border-orange-500 relative z-10 mx-4">
        
        <div class="text-center mb-6">
            <img src="{{ asset('logo.png') }}" alt="Logo Happy Momo" class="h-12 object-contain mx-auto mb-3 drop-shadow-md">
            <h1 class="text-xl font-bold text-slate-800">Registrasi Karyawan</h1>
            <p class="text-slate-500 text-xs mt-1">Isi data diri Anda dengan lengkap dan benar</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 text-red-600 p-3 rounded-lg mb-4 text-xs border-l-4 border-red-500">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.process') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-slate-700 text-xs font-bold uppercase mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:outline-none text-sm" placeholder="Contoh: Budi Santoso" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-1">NIK (Nomor Induk)</label>
                    <input type="text" name="nik" value="{{ old('nik') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:outline-none text-sm" placeholder="cth: 2025001" required>
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-1">Nomor HP / WA</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:outline-none text-sm" placeholder="0812..." required>
                </div>
            </div>

            <div>
                <label class="block text-slate-700 text-xs font-bold uppercase mb-1">Alamat Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:outline-none text-sm" placeholder="email@anda.com" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-1">Password</label>
                    <input type="password" name="password" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:outline-none text-sm" placeholder="Min. 8 karakter" required>
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-1">Ulangi Password</label>
                    <input type="password" name="password_confirmation" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:outline-none text-sm" placeholder="Ketik ulang..." required>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-amber-600 text-white font-bold py-3 px-4 rounded-lg hover:from-orange-600 hover:to-amber-700 shadow-orange-glow transition-all duration-300 transform hover:-translate-y-1">
                    DAFTAR SEKARANG
                </button>
            </div>
        </form>

        <div class="mt-6 text-center border-t border-slate-200 pt-4">
            <p class="text-xs text-slate-500">Sudah memiliki akun?</p>
            <a href="{{ route('login') }}" class="text-sm font-bold text-orange-600 hover:text-orange-700 hover:underline transition-colors">
                Login disini
            </a>
        </div>
    </div>

</body>
</html>