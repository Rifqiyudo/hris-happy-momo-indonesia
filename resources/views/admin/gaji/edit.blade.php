@extends('admin.layouts.master')
@section('title', 'Edit Komponen Gaji - ' . $salary->user->name)

@section('content')
<div class="max-w-5xl mx-auto pb-20 px-4">
    {{-- 1. BREADCRUMB --}}
    <nav class="flex mb-6 px-2" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest text-slate-400">
            <li><a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 transition">Home</a></li>
            <li><i class="fas fa-chevron-right text-[8px]"></i></li>
            <li><a href="{{ route('admin.gaji.index') }}" class="hover:text-indigo-600 transition">Gaji</a></li>
            <li><i class="fas fa-chevron-right text-[8px]"></i></li>
            <li class="text-slate-600">Edit Komponen</li>
        </ol>
    </nav>

    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        {{-- 2. HEADER PROFILE --}}
        <div class="p-8 border-b border-slate-50 bg-gradient-to-r from-white to-slate-50/50 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 bg-indigo-600 rounded-3xl flex items-center justify-center text-white shadow-lg shadow-indigo-100 relative">
                    <i class="fas fa-user-edit text-3xl"></i>
                    <div class="absolute -top-2 -right-2 bg-emerald-500 w-6 h-6 rounded-full border-4 border-white"></div>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-slate-700 tracking-tight">{{ $salary->user->name }}</h1>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">
                        {{ $salary->user->position->name ?? 'Staff' }} • {{ $salary->user->nik }}
                    </p>
                </div>
            </div>
            <div class="bg-indigo-50 px-6 py-3 rounded-2xl border border-indigo-100 text-center">
                <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-1">Estimasi Potongan BPJS</p>
                <h3 id="total_potongan_label" class="text-lg font-black text-indigo-600 tracking-tighter">Rp 0</h3>
            </div>
        </div>

        <form action="{{ route('admin.gaji.update', $salary->id) }}" method="POST" id="editSalaryForm">
            @csrf
            @method('PUT')

            <div class="p-8 space-y-10">
                {{-- SECTION 1: PENDAPATAN POKOK (DASAR BPJS) --}}
                <div class="bg-slate-50/50 p-8 rounded-[2rem] border border-slate-100/50">
                    <h3 class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-indigo-50 flex items-center justify-center"><i class="fas fa-money-bill-wave"></i></span>
                        Komponen Tetap (Dasar BPJS)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Gaji Pokok</label>
                            <input type="text" name="base_salary" id="base_salary"
                                value="{{ number_format($salary->base_salary, 0, ',', '.') }}"
                                class="input-rupiah w-full bg-white border border-slate-200 rounded-2xl py-4 px-6 text-sm font-black text-slate-700 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Tj. Jabatan</label>
                            <input type="text" name="position_allowance" id="position_allowance"
                                value="{{ number_format($salary->position_allowance, 0, ',', '.') }}"
                                class="input-rupiah w-full bg-white border border-slate-200 rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 outline-none focus:border-indigo-500 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Tj. Keluarga</label>
                            <input type="text" name="family_allowance" id="family_allowance"
                                value="{{ number_format($salary->family_allowance, 0, ',', '.') }}"
                                class="input-rupiah w-full bg-white border border-slate-200 rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 outline-none focus:border-indigo-500 transition-all">
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: TUNJANGAN LAIN & INSENTIF --}}
                <div>
                    <h3 class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-emerald-50 flex items-center justify-center"><i class="fas fa-plus-circle"></i></span>
                        Tunjangan Harian & Insentif
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest text-nowrap">Uang Makan / Hari</label>
                            <input type="text" name="meal_allowance" 
                                value="{{ number_format($salary->meal_allowance, 0, ',', '.') }}"
                                class="input-rupiah w-full bg-slate-50 border border-slate-100 rounded-2xl py-4 px-4 text-sm font-bold text-slate-700 outline-none focus:border-emerald-500 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest text-nowrap">Transport / Hari</label>
                            <input type="text" name="transport_allowance" 
                                value="{{ number_format($salary->transport_allowance, 0, ',', '.') }}"
                                class="input-rupiah w-full bg-slate-50 border border-slate-100 rounded-2xl py-4 px-4 text-sm font-bold text-slate-700 outline-none focus:border-emerald-500 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest">Nominal THR</label>
                            <input type="text" name="thr_amount" 
                                value="{{ number_format($salary->thr_amount, 0, ',', '.') }}"
                                class="input-rupiah w-full bg-amber-50/30 border border-amber-100 rounded-2xl py-4 px-4 text-sm font-black text-amber-700 outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase ml-2 tracking-widest text-nowrap">Bonus / Lainnya</label>
                            <input type="text" name="bonus_amount" 
                                value="{{ number_format($salary->bonus_amount, 0, ',', '.') }}"
                                class="input-rupiah w-full bg-slate-50 border border-slate-100 rounded-2xl py-4 px-4 text-sm font-bold text-slate-700 outline-none">
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: POTONGAN & BPJS --}}
                <div class="pt-8 border-t border-slate-50">
                    <h3 class="text-[10px] font-black text-rose-500 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-rose-50 flex items-center justify-center"><i class="fas fa-shield-alt"></i></span>
                        Konfigurasi Potongan
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-indigo-50/30 p-6 rounded-3xl border border-indigo-100">
                            <div class="flex justify-between items-center mb-4">
                                <label class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">BPJS JHT (%)</label>
                                <span id="nominal_jht" class="text-[10px] font-black text-indigo-600">Rp 0</span>
                            </div>
                            <input type="number" step="0.01" name="jht_percent" id="jht_percent"
                                value="{{ $salary->jht_percent ?? 2.00 }}"
                                class="input-persen w-full bg-white border border-slate-200 rounded-2xl py-4 px-6 text-sm font-black text-slate-700 outline-none focus:border-indigo-500 transition-all text-center shadow-inner">
                        </div>

                        <div class="bg-indigo-50/30 p-6 rounded-3xl border border-indigo-100">
                            <div class="flex justify-between items-center mb-4">
                                <label class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">BPJS Kesehatan (%)</label>
                                <span id="nominal_kes" class="text-[10px] font-black text-indigo-600">Rp 0</span>
                            </div>
                            <input type="number" step="0.01" name="kes_percent" id="kes_percent"
                                value="{{ $salary->kes_percent ?? 1.00 }}"
                                class="input-persen w-full bg-white border border-slate-200 rounded-2xl py-4 px-6 text-sm font-black text-slate-700 outline-none focus:border-indigo-500 transition-all text-center shadow-inner">
                        </div>

                        <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                            <div class="mb-4">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Potongan Rutin / Koperasi</label>
                            </div>
                            <input type="text" name="routine_deduction" id="routine_deduction"
                                value="{{ number_format($salary->routine_deduction, 0, ',', '.') }}"
                                class="input-rupiah w-full bg-white border border-slate-200 rounded-2xl py-4 px-6 text-sm font-black text-rose-500 outline-none focus:border-rose-500 transition-all text-right shadow-inner">
                        </div>
                    </div>
                    <p class="text-[9px] text-slate-400 mt-4 italic text-center">*BPJS dihitung otomatis dari Gaji Pokok + Tunjangan Jabatan + Tunjangan Keluarga.</p>
                </div>
            </div>

            {{-- 4. FOOTER BUTTONS --}}
            <div class="p-8 bg-slate-50 border-t border-slate-100 flex justify-between items-center">
                <a href="{{ route('admin.gaji.index') }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition">Batal & Kembali</a>
                <button type="submit" class="bg-indigo-600 text-white px-10 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition active:scale-95">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('editSalaryForm');
        
        function formatRupiah(angka) {
            let number_string = angka.toString().replace(/[^,\d]/g, ''),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        }

        /**
         * Kalkulasi BPJS Real-time
         * DPI = Pokok + Jabatan + Keluarga
         */
        function calculateBPJS() {
            const gapok = parseFloat(document.getElementById('base_salary').value.replace(/\./g, '')) || 0;
            const pos = parseFloat(document.getElementById('position_allowance').value.replace(/\./g, '')) || 0;
            const fam = parseFloat(document.getElementById('family_allowance').value.replace(/\./g, '')) || 0;
            const rutin = parseFloat(document.getElementById('routine_deduction').value.replace(/\./g, '')) || 0;

            const dpi = gapok + pos + fam;

            const jhtPersen = parseFloat(document.getElementById('jht_percent').value) || 0;
            const kesPersen = parseFloat(document.getElementById('kes_percent').value) || 0;

            const nomJht = (dpi * jhtPersen) / 100;
            const nomKes = (dpi * kesPersen) / 100;
            const totalPot = nomJht + nomKes + rutin;

            document.getElementById('nominal_jht').innerText = 'Rp ' + formatRupiah(Math.round(nomJht));
            document.getElementById('nominal_kes').innerText = 'Rp ' + formatRupiah(Math.round(nomKes));
            document.getElementById('total_potongan_label').innerText = 'Rp ' + formatRupiah(Math.round(totalPot));
        }

        document.querySelectorAll('.input-rupiah').forEach(input => {
            input.addEventListener('keyup', function() {
                this.value = formatRupiah(this.value);
                calculateBPJS();
            });
        });

        document.querySelectorAll('.input-persen').forEach(input => {
            input.addEventListener('input', calculateBPJS);
        });

        form.addEventListener('submit', function() {
            document.querySelectorAll('.input-rupiah').forEach(input => {
                input.value = input.value.replace(/\./g, '');
            });
            Swal.fire({ 
                title: 'Sedang Memproses...', 
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading() 
            });
        });

        // Inisialisasi awal
        calculateBPJS();
    });
</script>
@endsection