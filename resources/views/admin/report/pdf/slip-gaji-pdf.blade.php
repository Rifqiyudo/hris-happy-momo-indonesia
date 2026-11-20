<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    @php
        // Memastikan nama bulan aman meskipun atribut month_label tidak ada di model
        $bulanSlip = $payroll->month_label ?? \Carbon\Carbon::createFromFormat('m', $payroll->month)->translatedFormat('F');
        
        // Smart Fallback untuk Hari Kerja (Mencegah nilai 0 jika nama kolom di DB berbeda)
        $hariKerja = $payroll->total_days_worked ?? $payroll->present_days ?? $payroll->attendance_days ?? 0;
    @endphp
    <title>Slip Gaji - {{ $payroll->user->name }} - {{ $bulanSlip }} {{ $payroll->year }}</title>
    <style>
        @page { size: A4 portrait; margin: 1cm; }
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 10pt; color: #333; line-height: 1.4; margin: 0; padding: 0; }
        .container { width: 100%; }
        
        /* Header Section */
        .header { text-align: center; margin-bottom: 25px; border-bottom: 3px solid #1e293b; padding-bottom: 15px; position: relative; }
        .header .logo-left { position: absolute; left: 10px; top: 0; height: 55px; width: auto; }
        .header h2 { margin: 0; color: #1e293b; letter-spacing: 2px; text-transform: uppercase; font-size: 18pt; }
        .header p { margin: 2px 0; font-size: 8pt; color: #64748b; }
        .periode-tag { display: inline-block; margin-top: 10px; padding: 6px 20px; background-color: #1e293b; color: #ffffff; font-weight: bold; border-radius: 5px; text-transform: uppercase; font-size: 9pt; }

        /* Info Karyawan */
        .info-table { width: 100%; margin-bottom: 25px; border: 1px solid #e2e8f0; padding: 15px; border-radius: 10px; }
        .info-table td { padding: 4px 5px; vertical-align: top; }
        .label { color: #64748b; font-size: 8pt; text-transform: uppercase; letter-spacing: 0.5px; font-weight: bold; }
        .value { font-weight: bold; color: #1e293b; }

        /* Rincian Tabel */
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .details-table th { background-color: #f8fafc; border-top: 1px solid #e2e8f0; border-bottom: 2px solid #1e293b; padding: 12px 10px; text-align: left; font-size: 8pt; color: #1e293b; text-transform: uppercase; }
        .details-table td { padding: 10px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        
        .amount { text-align: right; font-family: 'Courier New', Courier, monospace; font-weight: bold; font-size: 11pt; }
        .text-red { color: #e11d48; }
        .text-indigo { color: #4f46e5; }
        .text-small { font-size: 8pt; color: #64748b; font-weight: normal; display: block; margin-top: 3px; font-style: italic; }

        /* Total Box */
        .total-box { margin-top: 20px; background-color: #f8fafc; border: 2px solid #1e293b; color: #1e293b; padding: 15px; border-radius: 10px; }
        .total-box table { width: 100%; border: none; }
        .total-box td { font-size: 11pt; font-weight: bold; }
        .total-box .amount-total { font-size: 14pt; color: #1e293b; text-align: right; }
        .net-amount { font-size: 20px; font-weight: bold; color: #15803d; margin: 5px 0 0 0; }
        .terbilang { font-size: 8pt; color: #64748b; font-style: italic; margin-top: 5px; border-top: 1px solid #e2e8f0; padding-top: 5px; text-transform: capitalize; }

        /* Footer & Signature */
        .footer { margin-top: 40px; width: 100%; }
        .sig-table { width: 100%; }
        .sig-table td { text-align: center; width: 50%; vertical-align: bottom; }
        .signature-space { height: 80px; }
        .note { font-size: 7pt; color: #94a3b8; font-style: italic; margin-top: 50px; border-top: 1px dashed #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        {{-- 1. HEADER --}}
        <div class="header">
            {{-- Menyematkan File Logo Transparan Tanpa Background Lokal Absolute --}}
            @if(file_exists(public_path('logo.png')))
                <img src="{{ public_path('logo.png') }}" class="logo-left" alt="Logo">
            @endif
            
            <h2>PT. HAPPY MOMO INDONESIA</h2>
            {{-- [DIPERBAIKI] Alamat Dipatenkan Sesuai Request --}}
            <p>Jl. Pergudangan Margomulyo Permai, Greges, Kec. Asem Rowo, Surabaya, Jawa Timur 60183</p>
            <p>Email: happymomoindonesia@gmail.com | Hubungi: {{ $payroll->user->branchDetail->phone ?? $payroll->user->branch->phone ?? '0823 3816 5747' }}</p>
            <div class="periode-tag">
                SLIP GAJI PERIODE: {{ strtoupper($bulanSlip) }} {{ $payroll->year }}
            </div>
        </div>

        {{-- 2. INFORMASI KARYAWAN --}}
        <table class="info-table">
            <tr>
                <td class="label" width="18%">Nama Karyawan</td>
                <td class="value" width="32%">: {{ strtoupper($payroll->user->name) }}</td>
                <td class="label" width="18%">Unit Kerja</td>
                {{-- [DIPERBAIKI] Memanggil unit kerja dari accessor otomatis di Model User --}}
                <td class="value" width="32%">: {{ $payroll->user->unit_kerja ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">NIK / ID</td>
                <td class="value">: {{ $payroll->user->nik ?? '-' }}</td>
                <td class="label">Departemen</td>
                <td class="value">: {{ $payroll->user->department->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Jabatan</td>
                <td class="value">: {{ $payroll->user->position->name ?? '-' }}</td>
                <td class="label">Hari Kerja</td>
                {{-- [DIPERBAIKI] Menggunakan variabel $hariKerja yang sudah di-backup fallback-nya --}}
                <td class="value">: {{ $hariKerja }} Hari</td>
            </tr>
        </table>

        {{-- 3. TABEL RINCIAN --}}
        <table class="details-table">
            <thead>
                <tr>
                    <th width="70%">Deskripsi Penghasilan & Potongan</th>
                    <th width="30%" class="amount">Jumlah (IDR)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Gaji Pokok</strong></td>
                    <td class="amount">{{ number_format($payroll->base_salary, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Tunjangan Jabatan & Keluarga</strong></td>
                    <td class="amount">{{ number_format($payroll->position_allowance + $payroll->family_allowance, 0, ',', '.') }}</td>
                </tr>

                <tr>
                    <td>
                        <strong>Tunjangan Makan & Transport</strong>
                        <span class="text-small">Sesuai total kehadiran ({{ $hariKerja }} Hari)</span>
                    </td>
                    <td class="amount">{{ number_format($payroll->meal_allowance_total + $payroll->transport_allowance_total, 0, ',', '.') }}</td>
                </tr>

                @if(($payroll->thr_amount + $payroll->bonus_amount + $payroll->other_allowance) > 0)
                <tr>
                    <td><strong>Bonus, THR & Tunjangan Lainnya</strong></td>
                    <td class="amount">{{ number_format($payroll->thr_amount + $payroll->bonus_amount + $payroll->other_allowance, 0, ',', '.') }}</td>
                </tr>
                @endif

                {{-- NEW: Potongan Unpaid Leave (Transparansi) --}}
                @if(isset($payroll->unpaid_deduction_amount) && $payroll->unpaid_deduction_amount > 0)
                <tr>
                    <td class="text-red"><strong>Potongan Izin / Mangkir / Cuti (Unpaid)</strong></td>
                    <td class="amount text-red">- {{ number_format($payroll->unpaid_deduction_amount, 0, ',', '.') }}</td>
                </tr>
                @endif

                @if($payroll->total_deduction > 0)
                <tr>
                    <td>
                        <strong class="text-red">Potongan Lainnya</strong>
                        <span class="text-small">BPJS Ketenagakerjaan, Kesehatan, Telat & Kasbon</span>
                    </td>
                    <td class="amount text-red">- {{ number_format($payroll->total_deduction - ($payroll->unpaid_deduction_amount ?? 0), 0, ',', '.') }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        {{-- 4. TOTAL BOX --}}
        <div class="total-box">
            <table border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="55%">TOTAL GAJI BERSIH (TAKE HOME PAY)</td>
                    <td class="amount-total">Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}</td>
                </tr>
            </table>
            <div class="terbilang">
                <strong>Terbilang:</strong> 
                @if(class_exists('\App\Helpers\Terbilang'))
                    {{ ucwords(\App\Helpers\Terbilang::make($payroll->net_salary)) }} Rupiah
                @else
                    {{ ucwords(number_format($payroll->net_salary, 0, ',', ' ')) }} Rupiah
                @endif
            </div>
        </div>

        {{-- 5. FOOTER & SIGNATURE --}}
        <div class="footer">
            <table class="sig-table">
                <tr>
                    <td>
                        <p>Penerima,</p>
                        <div class="signature-space"></div>
                        <p><strong>( {{ strtoupper($payroll->user->name) }} )</strong></p>
                    </td>
                    <td>
                        <p>Surabaya, {{ $payroll->payment_date ? \Carbon\Carbon::parse($payroll->payment_date)->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}</p>
                        <p>HR Department,</p>
                        <div class="signature-space"></div>
                        <p><strong>PT. HAPPY MOMO INDONESIA</strong></p>
                    </td>
                </tr>
            </table>
            
            <p class="note">
                * Dokumen ini sah dan dihasilkan secara elektronik oleh Sistem HRIS Happy Momo Indonesia.<br>
                * Harap simpan slip gaji ini sebagai bukti penghasilan resmi Anda.
            </p>
        </div>
    </div>
</body>
</html>