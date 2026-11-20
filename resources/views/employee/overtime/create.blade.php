@extends('employee.layouts.app')
@section('title', 'Ajukan Lembur')

@section('content')
<div class="p-6 pb-24">

    {{-- HEADER --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('employee.overtime.index') }}" class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 rounded-xl shadow-sm hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left text-slate-600"></i>
        </a>
        <h1 class="text-xl font-bold text-slate-800">Form Lembur</h1>
    </div>

    {{-- FORM CONTAINER --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        
        <form action="{{ route('employee.overtime.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- 1. TANGGAL --}}
            <div class="mb-5">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Tanggal Lembur</label>
                <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-semibold focus:outline-none focus:border-indigo-500">
            </div>

            {{-- 2. JAM MULAI & SELESAI --}}
            <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Jam Mulai</label>
                    <input type="time" name="start_time" id="start_time" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-semibold focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Jam Selesai</label>
                    <input type="time" name="end_time" id="end_time" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-semibold focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            {{-- INFO DURASI OTOMATIS --}}
            <div id="duration-box" class="hidden mb-5 bg-indigo-50 border border-indigo-100 rounded-xl p-3 flex items-center justify-between">
                <span class="text-xs text-indigo-600 font-bold">Estimasi Durasi:</span>
                <span class="text-sm font-black text-indigo-700" id="duration-text">0 Jam</span>
            </div>

            {{-- 3. ALASAN LEMBUR --}}
            <div class="mb-5">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Kegiatan / Alasan</label>
                <textarea name="reason" rows="3" required placeholder="Contoh: Menyelesaikan laporan akhir bulan..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:border-indigo-500"></textarea>
            </div>

            {{-- 4. LAMPIRAN (OPSIONAL) --}}
            <div class="mb-8">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Bukti / SPKL (Opsional)</label>
                <input type="file" name="attachment" accept="image/*"
                    class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="text-[10px] text-gray-400 mt-1">*Upload foto surat perintah atau hasil kerja.</p>
            </div>

            {{-- TOMBOL SUBMIT --}}
            <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transition transform active:scale-95">
                KIRIM PENGAJUAN
            </button>

        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // SCRIPT MENGHITUNG DURASI OTOMATIS
    const startInput = document.getElementById('start_time');
    const endInput = document.getElementById('end_time');
    const durationBox = document.getElementById('duration-box');
    const durationText = document.getElementById('duration-text');

    function calculateDuration() {
        const start = startInput.value; // Format "HH:mm"
        const end = endInput.value;     // Format "HH:mm"

        if (start && end) {
            // Konversi ke Date Object (Tanggal dummy, fokus jam)
            const startDate = new Date(`2000-01-01T${start}`);
            const endDate = new Date(`2000-01-01T${end}`);

            // Hitung selisih milidetik
            let diff = endDate - startDate;

            // Jika minus (misal lembur lintas hari 23:00 - 01:00), logic sederhana anggap input salah dulu
            // Atau bisa ditambahkan logic +24 jam. Disini kita validasi simple saja.
            if (diff > 0) {
                // Konversi ke Jam (milidetik / 1000 / 60 / 60)
                const hours = (diff / (1000 * 60 * 60)).toFixed(1); // 1 desimal
                
                durationText.innerText = hours + " Jam";
                durationBox.classList.remove('hidden');
            } else {
                durationBox.classList.add('hidden');
            }
        }
    }

    startInput.addEventListener('change', calculateDuration);
    endInput.addEventListener('change', calculateDuration);
</script>
@endsection