<!DOCTYPE html>
<html>
<head>
    <title>Slip Gaji</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>SLIP GAJI KARYAWAN</h2>
        <p>PT. HAPPY MOMO INDONESIA</p>
    </div>

    <p><strong>Periode:</strong> {{ $payroll->month }}</p>
    <p><strong>Nama:</strong> {{ Auth::user()->name }}</p>
    <p><strong>Jabatan:</strong> {{ Auth::user()->position->name }}</p>

    <hr>

    <h3>Rincian Penerimaan</h3>
    <div class="row">
        <span>Gaji Pokok</span>
        <span>Rp {{ number_format($payroll->basic_salary, 0, ',', '.') }}</span>
    </div>
    <hr>
    
    <div class="row bold" style="font-size: 18px; margin-top: 20px;">
        <span>Total Diterima (THP)</span>
        <span>Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}</span>
    </div>

    <div style="margin-top: 50px; text-align: right;">
        <p>Surabaya, {{ date('d M Y') }}</p>
        <br><br>
        <p>( Finance Dept )</p>
    </div>

    <script>
        window.print(); // Otomatis buka dialog print saat dibuka
    </script>
</body>
</html>