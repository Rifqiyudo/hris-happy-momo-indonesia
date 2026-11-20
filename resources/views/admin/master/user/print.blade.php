<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Profil - {{ $user->name }}</title>
    
    {{-- Load Tailwind untuk Styling Cepat --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* PENGATURAN KERTAS A4 STRICT */
        @page {
            size: A4 portrait;
            margin: 10mm; /* Margin tipis agar muat banyak */
        }
        
        body {
            background-color: #f1f5f9;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Mode Layar vs Mode Cetak */
        @media print {
            body { background-color: white !important; }
            .no-print { display: none !important; }
            .print-container { 
                box-shadow: none !important; 
                margin: 0 !important; 
                padding: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }

        /* Kanvas Simulasi A4 di Monitor */
        .print-container {
            width: 210mm;
            min-height: 297mm;
            background: white;
            margin: 2rem auto;
            padding: 15mm;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            position: relative;
        }
    </style>
</head>
<body class="antialiased text-slate-800">

    {{-- TOMBOL AKSI (Akan hilang saat diprint) --}}
    <div class="max-w-[210mm] mx-auto mt-6 flex justify-between items-center no-print">
        <button onclick="window.history.back()" class="px-4 py-2 bg-white text-slate-600 rounded-lg text-xs font-black uppercase tracking-widest border border-slate-200 hover:bg-slate-50 transition shadow-sm flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </button>
        <button onclick="window.print()" class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-xs font-black uppercase tracking-widest hover:bg-indigo-700 transition shadow-md flex items-center gap-2">
            <i class="fas fa-print"></i> Cetak Sekarang
        </button>
    </div>

    {{-- ======================================================== --}}
    {{-- AREA KERTAS A4 --}}
    {{-- ======================================================== --}}
    <div class="print-container">
        
        {{-- 1. KOP SURAT / HEADER --}}
        <div class="flex justify-between items-end border-b-2 border-slate-800 pb-4 mb-6">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase leading-none">Profil Karyawan</h1>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">HRIS - PT. Happy Momo Indonesia</p>
            </div>
            <div class="text-right">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Dicetak pada: {{ now()->translatedFormat('d M Y H:i') }}</p>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Oleh: {{ auth()->user()->name ?? 'Administrator' }}</p>
            </div>
        </div>

        {{-- 2. IDENTITAS UTAMA (PADAT & HORIZONTAL) --}}
        <div class="flex items-center gap-6 mb-6 p-4 bg-slate-50 rounded-xl border border-slate-200">
            {{-- Foto --}}
            <img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : asset('assets/img/default-avatar.png') }}" 
                 alt="Foto Profil" class="w-24 h-24 rounded-lg object-cover border-2 border-white shadow-sm shrink-0 bg-slate-200">
            
            {{-- Data Singkat --}}
            <div class="flex-1">
                <div class="flex gap-2 mb-1.5">
                    <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-[10px] font-black uppercase tracking-widest rounded border border-indigo-200">
                        NIK: {{ $user->nik ?? '-' }}
                    </span>
                    <span class="px-2 py-0.5 bg-slate-200 text-slate-700 text-[10px] font-black uppercase tracking-widest rounded border border-slate-300">
                        {{ $user->position->name ?? 'Staff' }}
                    </span>
                </div>
                <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight leading-none mb-1.5">
                    {{ $user->name }}
                </h2>
                <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">
                    <i class="fas fa-building mr-1"></i> {{ $user->unit_kerja ?? 'Pusat' }} 
                    @if($user->branch_id && $user->branchDetail)
                        ({{ $user->branchDetail->code ?? '-' }})
                    @endif
                </p>
            </div>
        </div>

        {{-- 3. ISI DATA (MENGGUNAKAN GRID & TABEL AGAR RAPI) --}}
        <div class="grid grid-cols-2 gap-x-8 gap-y-6">
            
            {{-- KOLOM KIRI: KEPEGAWAIAN --}}
            <div>
                <h3 class="text-[11px] font-black text-slate-800 uppercase tracking-widest border-b-2 border-slate-200 pb-1.5 mb-3 flex items-center gap-2">
                    <i class="fas fa-briefcase text-slate-400"></i> Data Kepegawaian
                </h3>
                <table class="w-full text-[10px] text-left border-collapse">
                    <tbody>
                        <tr class="border-b border-slate-100">
                            <td class="py-2 text-slate-500 font-bold w-[40%]">Departemen</td>
                            <td class="py-2 font-black text-slate-800 uppercase">{{ $user->department->name ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-2 text-slate-500 font-bold">Status Karyawan</td>
                            <td class="py-2 font-black text-slate-800 uppercase">{{ $user->employee_status ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-2 text-slate-500 font-bold">Tgl Bergabung</td>
                            <td class="py-2 font-black text-slate-800">{{ $user->join_date ? \Carbon\Carbon::parse($user->join_date)->translatedFormat('d M Y') : '-' }}</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-2 text-slate-500 font-bold">Masa Kerja</td>
                            <td class="py-2 font-black text-slate-800 uppercase">{{ $user->service_period ?? 'Baru' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- KOLOM KANAN: DATA PRIBADI --}}
            <div>
                <h3 class="text-[11px] font-black text-slate-800 uppercase tracking-widest border-b-2 border-slate-200 pb-1.5 mb-3 flex items-center gap-2">
                    <i class="fas fa-id-card text-slate-400"></i> Data Pribadi
                </h3>
                <table class="w-full text-[10px] text-left border-collapse">
                    <tbody>
                        <tr class="border-b border-slate-100">
                            <td class="py-2 text-slate-500 font-bold w-[35%]">Email</td>
                            <td class="py-2 font-black text-slate-800">{{ $user->email ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-2 text-slate-500 font-bold">No. Telp / WA</td>
                            <td class="py-2 font-black text-slate-800">{{ $user->phone ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-2 text-slate-500 font-bold">TTL</td>
                            <td class="py-2 font-black text-slate-800">{{ $user->birth_place ?? '-' }}, {{ $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-2 text-slate-500 font-bold">Pendidikan</td>
                            <td class="py-2 font-black text-slate-800 uppercase">{{ $user->last_education ?? '-' }} ({{ $user->educational_major ?? '-' }})</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- LEGAL & FINANSIAL (FULL WIDTH BAWAHNYA) --}}
            <div class="col-span-2 mt-2">
                <h3 class="text-[11px] font-black text-slate-800 uppercase tracking-widest border-b-2 border-slate-200 pb-1.5 mb-3 flex items-center gap-2">
                    <i class="fas fa-file-signature text-slate-400"></i> Legal & Finansial
                </h3>
                <table class="w-full text-[10px] text-left border-collapse">
                    <tbody>
                        <tr class="border-b border-slate-100">
                            <td class="py-2.5 text-slate-500 font-bold w-[20%]">Nomor KTP</td>
                            <td class="py-2.5 font-black text-slate-800 w-[30%]">{{ $user->ktp_no ?? '-' }}</td>
                            <td class="py-2.5 text-slate-500 font-bold w-[20%]">Nama Bank</td>
                            <td class="py-2.5 font-black text-slate-800 w-[30%]">{{ $user->bank_name ?? '-' }}</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-2.5 text-slate-500 font-bold">No. BPJS Kesehatan</td>
                            <td class="py-2.5 font-black text-slate-800">{{ $user->bpjs_kesehatan_no ?? '-' }}</td>
                            <td class="py-2.5 text-slate-500 font-bold">Nomor Rekening</td>
                            <td class="py-2.5 font-black text-slate-800">{{ $user->bank_account_no ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- INVENTARIS ASET --}}
            <div class="col-span-2 mt-2">
                <h3 class="text-[11px] font-black text-slate-800 uppercase tracking-widest border-b-2 border-slate-200 pb-1.5 mb-3 flex items-center gap-2">
                    <i class="fas fa-laptop text-slate-400"></i> Aset & Inventaris Terdaftar
                </h3>
                <table class="w-full text-[10px] text-left border-collapse border border-slate-200">
                    <thead>
                        <tr class="bg-slate-100">
                            <th class="py-2 px-3 border border-slate-200 text-slate-600">No</th>
                            <th class="py-2 px-3 border border-slate-200 text-slate-600">Kategori</th>
                            <th class="py-2 px-3 border border-slate-200 text-slate-600">Nama Aset</th>
                            <th class="py-2 px-3 border border-slate-200 text-slate-600">Serial Number</th>
                            <th class="py-2 px-3 border border-slate-200 text-slate-600">Tgl Penyerahan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        
                        {{-- Data Master Asset --}}
                        @foreach($user->masterAssets ?? [] as $mAsset)
                        <tr>
                            <td class="py-1.5 px-3 border border-slate-200 text-center">{{ $no++ }}</td>
                            <td class="py-1.5 px-3 border border-slate-200 font-bold uppercase">{{ $mAsset->category ?? '-' }}</td>
                            <td class="py-1.5 px-3 border border-slate-200 font-bold">{{ $mAsset->name }}</td>
                            <td class="py-1.5 px-3 border border-slate-200 font-mono">{{ $mAsset->serial_number ?? '-' }}</td>
                            <td class="py-1.5 px-3 border border-slate-200">{{ $mAsset->assigned_date ? \Carbon\Carbon::parse($mAsset->assigned_date)->format('d/m/Y') : '-' }}</td>
                        </tr>
                        @endforeach

                        {{-- Data Legacy Asset --}}
                        @foreach($user->assets ?? [] as $asset)
                        <tr>
                            <td class="py-1.5 px-3 border border-slate-200 text-center">{{ $no++ }}</td>
                            <td class="py-1.5 px-3 border border-slate-200 font-bold uppercase">{{ $asset->category ?? '-' }}</td>
                            <td class="py-1.5 px-3 border border-slate-200 font-bold">{{ $asset->asset_name }}</td>
                            <td class="py-1.5 px-3 border border-slate-200 font-mono">{{ $asset->asset_serial_number ?? '-' }}</td>
                            <td class="py-1.5 px-3 border border-slate-200">{{ $asset->handover_date ? \Carbon\Carbon::parse($asset->handover_date)->format('d/m/Y') : '-' }}</td>
                        </tr>
                        @endforeach

                        @if($no == 1)
                        <tr>
                            <td colspan="5" class="py-4 text-center text-slate-400 font-bold italic">Tidak ada aset yang terdaftar pada karyawan ini.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 4. FOOTER DOKUMEN --}}
        <div class="absolute bottom-10 left-10 right-10 border-t border-slate-800 pt-3 text-center">
            <p class="text-[8px] font-black text-slate-500 uppercase tracking-[0.2em]">
                Dokumen Cetak Internal HRIS | Berlaku Sejak {{ now()->translatedFormat('d M Y') }}
            </p>
        </div>

    </div>

</body>
</html>