<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Slip Gaji - {{ $user->nik }} - {{ $payroll->month }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #334155; line-height: 1.5; padding: 20px; }
        
        /* UPDATE: Penyesuaian Kop Surat untuk Logo */
        .header { margin-bottom: 25px; border-bottom: 2px solid #1e293b; padding-bottom: 15px; width: 100%; }
        .header-table { width: 100%; border-collapse: collapse; }
        .company-logo { width: 55px; height: auto; object-fit: contain; }
        .company-name { font-size: 18px; font-weight: 900; margin: 0; color: #1e293b; text-transform: uppercase; letter-spacing: -0.025em; }
        .company-address { font-size: 10px; color: #64748b; margin-top: 2px; margin-bottom: 0; }
        .header-right { text-align: right; vertical-align: bottom; }
        .badge-title { display: inline-block; background: #e0e7ff; color: #4f46e5; padding: 4px 12px; border-radius: 6px; font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; }
        .period-text { margin: 0; color: #64748b; font-weight: bold; text-transform: uppercase; font-size: 10px; letter-spacing: 0.1em; }
        
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 3px 0; vertical-align: top; }
        
        .table { width: 100%; border-collapse: collapse; margin-bottom: 15px; border: 1px solid #e2e8f0; }
        .table th { background-color: #f8fafc; text-align: left; padding: 10px; border-bottom: 1px solid #e2e8f0; font-weight: 900; font-size: 9px; letter-spacing: 0.05em; }
        .table td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; }
        
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; color: #0f172a; }
        .deduction { color: #e11d48; }
        
        .thp-container { background: #4f46e5; color: white; padding: 15px; border-radius: 12px; margin-top: 20px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); }
        .thp-table { width: 100%; border: none; }
        .thp-label { font-size: 11px; font-weight: 900; letter-spacing: 0.05em; }
        .thp-value { font-size: 20px; font-weight: 900; text-align: right; }
        
        .terbilang { font-style: italic; margin-top: 10px; color: #64748b; font-size: 10px; padding-left: 5px; border-left: 3px solid #e2e8f0; }
        
        .footer { margin-top: 40px; }
        .ttd-box { text-align: center; vertical-align: bottom; }
        .ttd-name { font-weight: 900; text-decoration: underline; text-transform: uppercase; }

        @media print {
            body { padding: 0; }
            .thp-container { background: #4f46e5 !important; -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    @php
        // FIX TYPEERROR CARBON: Cek apakah month numeric atau string
        $monthName = $payroll->month;
        if (is_numeric($payroll->month)) {
            $monthName = \Carbon\Carbon::createFromDate(null, (int)$payroll->month, 1)->translatedFormat('F');
        }
    @endphp

    {{-- UPDATE: Kop Surat Menggunakan Tabel agar Sejajar dengan Logo --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td width="60%">
                    <table style="text-align: left;">
                        <tr>
                            <td width="65" style="vertical-align: middle;">
                                {{-- Pemanggilan Logo Resmi Perusahaan --}}
                                <img src="{{ asset('images/PT-Happy-Momo-Indonesia-removebg-preview (1).png') }}" alt="Logo Happy Momo" class="company-logo">
                            </td>
                            <td style="vertical-align: middle; padding-left: 10px;">
                                <h2 class="company-name">PT. HAPPY MOMO INDONESIA</h2>
                                <p class="company-address">Jl. Pergudangan Margomulyo Permai, Surabaya</p>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="40%" class="header-right">
                    <span class="badge-title">Slip Gaji Resmi</span>
                    <p class="period-text">PERIODE {{ $monthName }} {{ $payroll->year }}</p>
                </td>
            </tr>
        </table>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>NIK</strong></td><td width="35%">: {{ $user->nik }}</td>
            <td width="15%"><strong>Cabang</strong></td><td width="35%">: {{ $user->branch->name ?? 'KANTOR PUSAT' }}</td>
        </tr>
        <tr>
            <td><strong>Nama</strong></td><td>: {{ $user->name }}</td>
            <td><strong>Jabatan</strong></td><td>: {{ $user->position->name ?? '-' }}</td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr><th colspan="2">PENDAPATAN (EARNINGS)</th></tr>
        </thead>
        <tbody>
            <tr><td>Gaji Pokok</td><td class="text-right">Rp {{ number_format($payroll->basic_salary ?? $payroll->base_salary, 0, ',', '.') }}</td></tr>
            <tr><td>Tunjangan Tetap</td><td class="text-right">Rp {{ number_format($payroll->total_allowance, 0, ',', '.') }}</td></tr>
            <tr><td>Upah Lembur</td><td class="text-right">Rp {{ number_format($payroll->overtime_pay, 0, ',', '.') }}</td></tr>
            <tr class="text-bold" style="background: #f8fafc;">
                <td>Total Pendapatan Kotor</td>
                <td class="text-right">Rp {{ number_format(($payroll->basic_salary ?? $payroll->base_salary) + $payroll->total_allowance + $payroll->overtime_pay, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="table">
        <thead>
            <tr><th colspan="2">POTONGAN (DEDUCTIONS)</th></tr>
        </thead>
        <tbody>
            <tr><td>BPJS Kesehatan / Ketenagakerjaan</td><td class="text-right deduction">Rp {{ number_format(($payroll->bpjs_health_deduction ?? 0) + ($payroll->bpjs_tk_deduction ?? 0), 0, ',', '.') }}</td></tr>
            <tr><td>Denda Keterlambatan / Absen</td><td class="text-right deduction">Rp {{ number_format($payroll->late_deduction ?? $payroll->late_penalty, 0, ',', '.') }}</td></tr>
            <tr class="text-bold" style="background: #f8fafc;">
                <td>Total Potongan</td>
                <td class="text-right deduction">Rp {{ number_format($payroll->total_deduction, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="thp-container">
        <table class="thp-table">
            <tr>
                <td class="thp-label">TOTAL GAJI BERSIH (TAKE HOME PAY)</td>
                <td class="thp-value">Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <p class="terbilang">
        Terbilang: {{ ucwords(\App\Helpers\Terbilang::make($payroll->net_salary)) }} Rupiah
    </p>

    <div class="footer">
        <table width="100%">
            <tr>
                <td class="ttd-box" width="40%">
                    <p style="margin-bottom: 60px;">Penerima,</p>
                    <p class="ttd-name">{{ $user->name }}</p>
                </td>
                <td width="20%"></td>
                <td class="ttd-box" width="40%">
                    <p style="margin-bottom: 60px;">Surabaya, {{ date('d F Y') }}<br>Finance Dept,</p>
                    <p class="ttd-name">PT. HAPPY MOMO INDONESIA</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="no-print" style="margin-top: 30px; text-align: center;">
        <button onclick="window.print()" style="background: #4f46e5; color: white; padding: 10px 20px; border-radius: 8px; border: none; cursor: pointer; font-weight: bold;">
            Cetak Dokumen
        </button>
    </div>
</body>
</html>