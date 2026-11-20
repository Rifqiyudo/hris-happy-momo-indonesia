<!DOCTYPE html>
<html>
<head>
    <title>Slip Gaji - {{ $user->name ?? $payroll->user->name }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .header { border-bottom: 2px solid #6366f1; padding-bottom: 10px; position: relative; height: 60px; }
        .logo-box { position: absolute; left: 0; top: 0; }
        .logo-box img { height: 50px; width: auto; } /* Diperbarui: typo hieght -> height */
        .company-info { text-align: right; float: right; }
        .company-name { font-size: 16px; font-weight: bold; color: #6366f1; text-transform: uppercase; }
        .title { font-size: 14px; font-weight: bold; margin-top: 20px; text-transform: uppercase; text-align: center; clear: both; }
        .info-table, .content-table { width: 100%; margin-top: 15px; border-collapse: collapse; }
        .info-table td { padding: 2px; }
        .content-table th { background: #f8fafc; text-align: left; padding: 8px; border: 1px solid #e2e8f0; }
        .content-table td { padding: 8px; border: 1px solid #e2e8f0; }
        .text-right { text-align: right; }
        .total-box { background: #6366f1; color: white; padding: 10px; margin-top: 15px; font-weight: bold; }
        .footer { margin-top: 30px; }
        .signature { float: right; text-align: center; width: 150px; }
        
        /* Tambahan CSS untuk kerapian detail rincian baru */
        .section-title { font-weight: bold; background-color: #f1f5f9; }
        .text-red { color: #c53030; }
        .text-small { font-size: 10px; color: #666; display: block; margin-top: 2px;}
    </style>
</head>
<body>
    <div class="header">
        {{-- Logo Perusahaan Transparan Tanpa Background --}}
        <div class="logo-box">
            @if(file_exists(public_path('logo.png')))
                <img src="{{ public_path('logo.png') }}" alt="Logo Happy Momo">
            @endif
        </div>
        <div class="company-info">
            <div class="company-name">PT. HAPPY MOMO INDONESIA</div>
            <div style="color: #666; font-size: 10px;">Margomulyo Permai DD-III No.19, Surabaya</div>
        </div>
    </div>
    
    {{-- Menyesuaikan penarikan variabel periode agar fleksibel --}}
    <div class="title">SLIP GAJI KARYAWAN - {{ $periode ?? ($payroll->month . '/' . $payroll->year) }}</div>

    <table class="info-table">
        <tr>
            <td width="15%">NIK</td><td>: {{ $user->nik ?? $payroll->user->nik ?? '-' }}</td>
            <td width="15%">Departemen</td><td>: {{ $user->department->name ?? $payroll->user->department->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Nama</td><td>: <strong>{{ $user->name ?? $payroll->user->name }}</strong></td>
            <td>Jabatan</td><td>: {{ $user->position->name ?? $payroll->user->position->name ?? '-' }}</td>
        </tr>
    </table>

    <table class="content-table">
        <thead>
            <tr>
                <th>Deskripsi Pendapatan & Potongan</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <!-- PENDAPATAN -->
            <tr class="section-title">
                <td colspan="2">A. PENDAPATAN</td>
            </tr>
            <tr>
                <td>
                    Gaji Pokok (Gaji Harian)<br>
                    <small style="color: #666;">{{ $daysPresent ?? $payroll->total_days_worked ?? 0 }} Hari Hadir</small>
                </td>
                <td class="text-right">Rp {{ number_format($payroll->basic_salary ?? $payroll->base_salary, 0, ',', '.') }}</td>
            </tr>
            
            {{-- TAMBAHAN: Rincian Tunjangan dari Controller --}}
            @if(($payroll->position_allowance ?? 0) > 0)
            <tr>
                <td>Tunjangan Jabatan (Paten)</td>
                <td class="text-right">Rp {{ number_format($payroll->position_allowance, 0, ',', '.') }}</td>
            </tr>
            @endif
            
            @if(($payroll->family_allowance ?? 0) > 0)
            <tr>
                <td>Tunjangan Keluarga</td>
                <td class="text-right">Rp {{ number_format($payroll->family_allowance, 0, ',', '.') }}</td>
            </tr>
            @endif

            @if(($payroll->meal_allowance_total ?? 0) > 0)
            <tr>
                <td>Uang Makan <span class="text-small">({{ $payroll->total_days_worked ?? 0 }} Hari)</span></td>
                <td class="text-right">Rp {{ number_format($payroll->meal_allowance_total, 0, ',', '.') }}</td>
            </tr>
            @endif

            @if(($payroll->transport_allowance_total ?? 0) > 0)
            <tr>
                <td>Uang Transport <span class="text-small">({{ $payroll->total_days_worked ?? 0 }} Hari)</span></td>
                <td class="text-right">Rp {{ number_format($payroll->transport_allowance_total, 0, ',', '.') }}</td>
            </tr>
            @endif

            @if(($payroll->other_allowance ?? 0) > 0)
            <tr>
                <td>Tunjangan Lainnya / Master Dinamis</td>
                <td class="text-right">Rp {{ number_format($payroll->other_allowance, 0, ',', '.') }}</td>
            </tr>
            @endif

            @if(($payroll->bonus_amount ?? 0) > 0 || ($payroll->thr_amount ?? 0) > 0)
            <tr>
                <td>Bonus / THR / KPI</td>
                <td class="text-right">Rp {{ number_format(($payroll->bonus_amount ?? 0) + ($payroll->thr_amount ?? 0), 0, ',', '.') }}</td>
            </tr>
            @endif

            @if(isset($payroll->overtime_pay) && $payroll->overtime_pay > 0)
            <tr>
                <td>Lembur (Approved)</td>
                <td class="text-right">Rp {{ number_format($payroll->overtime_pay, 0, ',', '.') }}</td>
            </tr>
            @endif

            <tr style="background: #f8fafc; font-weight: bold;">
                <td>Total Pendapatan (A)</td>
                <td class="text-right">Rp {{ number_format(($payroll->base_salary ?? 0) + ($payroll->total_allowance ?? 0), 0, ',', '.') }}</td>
            </tr>

            <!-- POTONGAN -->
            <tr class="section-title">
                <td colspan="2">B. POTONGAN</td>
            </tr>

            {{-- TAMBAHAN: Rincian Potongan dari Controller --}}
            @if(($payroll->unpaid_deduction_amount ?? 0) > 0)
            <tr style="background: #fff5f5;">
                <td class="text-red">Potongan Mangkir / Alpha</td>
                <td class="text-right text-red">- Rp {{ number_format($payroll->unpaid_deduction_amount, 0, ',', '.') }}</td>
            </tr>
            @endif

            @if(($payroll->late_deduction ?? 0) > 0)
            <tr style="background: #fff5f5;">
                <td class="text-red">Potongan Keterlambatan</td>
                <td class="text-right text-red">- Rp {{ number_format($payroll->late_deduction, 0, ',', '.') }}</td>
            </tr>
            @endif

            @if(($payroll->jht_deduction ?? 0) > 0 || ($payroll->kes_deduction ?? 0) > 0 || ($payroll->jp_deduction ?? 0) > 0)
            <tr style="background: #fff5f5;">
                <td class="text-red">Potongan BPJS (JHT, Kes, JP)</td>
                <td class="text-right text-red">- Rp {{ number_format(($payroll->jht_deduction ?? 0) + ($payroll->kes_deduction ?? 0) + ($payroll->jp_deduction ?? 0), 0, ',', '.') }}</td>
            </tr>
            @endif

            @if(($payroll->routine_deduction ?? 0) > 0)
            <tr style="background: #fff5f5;">
                <td class="text-red">Potongan Lainnya / Kasbon</td>
                <td class="text-right text-red">- Rp {{ number_format($payroll->routine_deduction, 0, ',', '.') }}</td>
            </tr>
            @endif

            <tr style="background: #fff5f5; font-weight: bold;">
                <td class="text-red">Total Potongan (B)</td>
                <td class="text-right text-red">- Rp {{ number_format($payroll->total_deduction ?? $payroll->deductions ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-box">
        <table width="100%">
            <tr>
                <td>TOTAL GAJI BERSIH (TAKE HOME PAY)</td>
                <td class="text-right" style="font-size: 16px;">Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <div class="signature">
            {{-- Menggunakan waktu cetak real-time atau waktu generate --}}
            <p>Surabaya, {{ $payroll->payment_date ? \Carbon\Carbon::parse($payroll->payment_date)->translatedFormat('d F Y') : date('d F Y') }}</p>
            <p style="margin-top: 60px;">( HRD Manager )</p>
        </div>
        <div style="clear: both;"></div>
        <p style="font-size: 10px; color: #999; font-style: italic;">*Slip ini digenerate secara otomatis oleh Sistem HRIS PT. Happy Momo.</p>
    </div>
</body>
</html>