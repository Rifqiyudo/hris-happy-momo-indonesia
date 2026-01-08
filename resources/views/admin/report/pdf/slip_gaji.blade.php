<!DOCTYPE html>
<html>
<head>
    <title>Slip Gaji</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; color: #333; }
        .container { width: 100%; padding: 10px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #1a202c; }
        .header p { margin: 2px 0; font-size: 9pt; color: #555; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .info-table td { padding: 4px; }
        
        .details-table th, .details-table td { border-bottom: 1px solid #eee; padding: 8px; text-align: left; }
        .details-table th { background-color: #f7fafc; font-weight: bold; }
        
        .total-row td { border-top: 2px solid #333; font-weight: bold; font-size: 11pt; }
        .amount { text-align: right; }
        
        .footer { margin-top: 30px; text-align: right; font-size: 9pt; }
        .signature { height: 60px; }

        /* --- PERBAIKAN: STYLE HELPER --- */
        .text-red { color: #dc2626; }
        .text-black { color: #000000; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>PT. HAPPY MOMO INDONESIA</h2>
            <p>Pergudangan Margomulyo Permai, Surabaya, Jawa Timur</p>
            <p>SLIP GAJI KARYAWAN</p>
        </div>

        <table class="info-table">
            <tr>
                <td width="15%">Nama</td>
                <td width="35%">: <strong>{{ $payroll->user->name }}</strong></td>
                <td width="15%">Periode</td>
                <td width="35%">: {{ \Carbon\Carbon::create()->month($payroll->month)->locale('id')->monthName }} {{ $payroll->year }}</td>
            </tr>
            <tr>
                <td>NIK</td>
                <td>: {{ $payroll->user->nik ?? '-' }}</td>
                <td>Jabatan</td>
                <td>: {{ $payroll->user->position->name ?? '-' }}</td>
            </tr>
        </table>

        <br>

        <table class="details-table">
            <thead>
                <tr>
                    <th>Keterangan</th>
                    <th class="amount">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Gaji Pokok</td>
                    <td class="amount">{{ number_format($payroll->base_salary, 0, ',', '.') }}</td>
                </tr>

                @foreach($payroll->details as $detail)
                <tr class="{{ $detail->type == 'deduction' ? 'text-red' : 'text-black' }}">
                    <td>
                        {{ $detail->name }}
                        @if($detail->type == 'deduction') (Potongan) @endif
                    </td>
                    <td class="amount">
                        {{ $detail->type == 'deduction' ? '-' : '' }} 
                        {{ number_format($detail->amount, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td>TOTAL DITERIMA (NET)</td>
                    <td class="amount">Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            <p>Surabaya, {{ date('d F Y') }}</p>
            <p>Manager Keuangan</p>
            <div class="signature"></div>
            <p>( _______________________ )</p>
        </div>
    </div>
</body>
</html>