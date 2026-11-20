@extends('admin.layouts.master')
@section('title', 'Data Synchronizer - Happy Momo')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
    {{-- 1. HEADER & TEMPLATE --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Data Synchronizer</h1>
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mt-1 italic">Impor Masal & Sinkronisasi SDM PT. Happy Momo Indonesia</p>
        </div>
        <a href="{{ route('admin.master.user.download-template') }}" class="group flex items-center gap-3 bg-white border border-slate-200 px-5 py-3 rounded-2xl hover:border-indigo-500 transition-all shadow-sm">
            <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all">
                <i class="fas fa-file-download text-xs"></i>
            </div>
            <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Unduh Template CSV</span>
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200 border border-slate-100 overflow-hidden">
        {{-- Form Start --}}
        <form action="{{ route('admin.master.user.import.process') }}" method="POST" enctype="multipart/form-data" id="mainImportForm">
            @csrf
            <div class="p-10 space-y-8">
                
                {{-- 2. DRAG & DROP AREA --}}
                <div class="relative group">
                    <input type="file" name="file_csv" id="file_csv" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required onchange="processFilePreview(this)">
                    <div id="drop-zone" class="border-4 border-dashed border-slate-100 rounded-[2.5rem] p-12 text-center group-hover:border-indigo-400 group-hover:bg-indigo-50/30 transition-all duration-500">
                        <div id="icon-container" class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto mb-6 transition-all">
                            <i class="fas fa-file-csv text-4xl text-slate-300"></i>
                        </div>
                        <h3 id="file-name-label" class="text-slate-700 font-black text-lg mb-2 uppercase tracking-tight">Pilih File CSV Karyawan</h3>
                        <p id="file-size-label" class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em]">WAJIB FORMAT SEMICOLON ( ; )</p>
                    </div>
                </div>

                {{-- 3. LIVE PREVIEW TABLE (Akan muncul setelah file dipilih) --}}
                <div id="preview-container" class="hidden space-y-4 animate-in slide-in-from-bottom-4 duration-700">
                    <div class="flex items-center justify-between px-2">
                        <h4 class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">Pratinjau Data (Baris 1-5)</h4>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                            <span class="text-[9px] font-black text-emerald-600 uppercase">Struktur Sesuai</span>
                        </div>
                    </div>
                    <div class="overflow-x-auto rounded-[1.5rem] border border-slate-100 shadow-sm bg-slate-50/30">
                        <table class="w-full text-left text-[10px] border-collapse">
                            <thead class="bg-slate-900 text-white font-black uppercase">
                                <tr>
                                    <th class="px-5 py-4 border-r border-white/10">NIK (5)</th>
                                    <th class="px-5 py-4 border-r border-white/10">Nama (4)</th>
                                    <th class="px-5 py-4 border-r border-white/10">Email (11)</th>
                                    <th class="px-5 py-4 border-r border-white/10">Bank (18)</th>
                                    <th class="px-5 py-4">Masuk (22)</th>
                                </tr>
                            </thead>
                            <tbody id="preview-body" class="text-slate-600 font-bold divide-y divide-slate-100">
                                {{-- JS Injection Here --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 4. COLUMN MAPPING GUIDE (Sesuai Controller Anda) --}}
                <div class="bg-slate-50 rounded-[2.5rem] p-8 border border-slate-100">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                        <i class="fas fa-project-diagram text-indigo-500"></i> Logic Pemetaan Kolom (Happy Momo Standard)
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @php
                            $cols = [
                                'Idx 0' => 'Branch/PT', 'Idx 4' => 'Nama Karyawan', 'Idx 5' => 'NIK (Primary)',
                                'Idx 8' => 'Tgl Lahir', 'Idx 11' => 'Email Aktif', 'Idx 15' => 'No. KTP',
                                'Idx 18' => 'Bank', 'Idx 19' => 'No. Rekening', 'Idx 22' => 'Join Date',
                                'Idx 23' => 'Status (P/C)', 'Idx 25' => 'Denda Late', 'Idx 27' => 'Bidang Jurusan'
                            ];
                        @endphp
                        @foreach($cols as $idx => $name)
                        <div class="flex items-center gap-3 bg-white p-4 rounded-2xl border border-slate-100 shadow-sm group hover:border-indigo-300 transition-all">
                            <span class="text-[9px] font-black text-indigo-500 bg-indigo-50 px-2 py-1 rounded-lg">{{ $idx }}</span>
                            <span class="text-[10px] font-black text-slate-700 uppercase tracking-tighter">{{ $name }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- 5. ACTION BUTTONS --}}
                <div class="flex items-center gap-4 pt-4">
                    <a href="{{ route('admin.master.user.index') }}" class="flex-1 text-center py-5 rounded-[1.5rem] text-[10px] font-black text-slate-400 uppercase tracking-widest hover:bg-slate-50 transition">Batalkan</a>
                    <button type="submit" id="btn-execute" disabled class="flex-[2] bg-slate-100 text-slate-400 py-5 rounded-[1.5rem] font-black text-[10px] uppercase tracking-[0.2em] transition-all cursor-not-allowed shadow-xl shadow-slate-100">
                        Eksekusi Sinkronisasi Data <i class="fas fa-bolt ml-2 text-amber-400"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    {{-- Footer Info --}}
    <div class="mt-8 text-center">
        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest flex items-center justify-center gap-2">
            <i class="fas fa-shield-alt text-indigo-500"></i> Data akan diperbarui (Update) jika NIK sudah ada di sistem.
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    /**
     * Fungsi untuk memproses Pratinjau File CSV secara real-time
     */
    function processFilePreview(input) {
        const file = input.files[0];
        const btn = document.getElementById('btn-execute');
        const fileNameLabel = document.getElementById('file-name-label');
        const dropZone = document.getElementById('drop-zone');
        const iconContainer = document.getElementById('icon-container');
        const previewContainer = document.getElementById('preview-container');
        const previewBody = document.getElementById('preview-body');

        if (file) {
            // Validasi Ekstensi
            if (!file.name.endsWith('.csv')) {
                Swal.fire({ icon: 'error', title: 'Format Salah', text: 'Hanya menerima file berformat .csv' });
                input.value = '';
                return;
            }

            fileNameLabel.innerText = file.name;
            
            // 1. Visual Feedback - Success State
            btn.disabled = false;
            btn.classList.remove('bg-slate-100', 'text-slate-400', 'cursor-not-allowed');
            btn.classList.add('bg-indigo-600', 'text-white', 'hover:bg-indigo-700', 'shadow-indigo-200');
            dropZone.classList.add('border-emerald-400', 'bg-emerald-50/10');
            iconContainer.classList.add('bg-emerald-500', 'text-white');
            iconContainer.innerHTML = '<i class="fas fa-check text-2xl animate-bounce"></i>';

            // 2. CSV Parsing Logic (Semicolon Delimited)
            const reader = new FileReader();
            reader.onload = function(e) {
                const text = e.target.result;
                const lines = text.split(/\r?\n/);
                let html = '';
                
                // Mulai dari baris 1 (lewati header)
                let dataRowsCount = 0;
                for (let i = 1; i < lines.length && dataRowsCount < 5; i++) {
                    const columns = lines[i].split(';');
                    
                    // Validasi baris memiliki konten
                    if (columns.length > 5 && columns[5].trim() !== '') {
                        html += `
                        <tr class="hover:bg-white transition-colors">
                            <td class="px-5 py-4 border-r border-slate-100 text-indigo-600 font-black tracking-tighter italic">${columns[5] || '-'}</td>
                            <td class="px-5 py-4 border-r border-slate-100 uppercase truncate max-w-[150px]">${columns[4] || '-'}</td>
                            <td class="px-5 py-4 border-r border-slate-100 lowercase text-slate-400 font-medium truncate max-w-[150px]">${columns[11] || '-'}</td>
                            <td class="px-5 py-4 border-r border-slate-100 uppercase">${columns[18] || '-'}</td>
                            <td class="px-5 py-4">${columns[22] || '-'}</td>
                        </tr>`;
                        dataRowsCount++;
                    }
                }
                
                if (dataRowsCount > 0) {
                    previewBody.innerHTML = html;
                    previewContainer.classList.remove('hidden');
                }
            };
            reader.readAsText(file);
        }
    }

    // Submit Handling with Loading State
    document.getElementById('mainImportForm').addEventListener('submit', function() {
        const btn = document.getElementById('btn-execute');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Sedang Sinkronisasi...';
        Swal.fire({
            title: 'Memproses Sinkronisasi',
            text: 'Harap jangan menutup halaman ini...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
    });
</script>

<style>
    /* Smooth Animation for Preview */
    .animate-in { animation: fadeIn 0.5s ease-out forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Custom Scrollbar for Table */
    .overflow-x-auto::-webkit-scrollbar { height: 6px; }
    .overflow-x-auto::-webkit-scrollbar-track { background: transparent; }
    .overflow-x-auto::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
@endsection