<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bukti Presensi - {{ $attendance->user->name }}</title>
    <style>
        /* Setup Halaman PDF */
        @page { 
            size: A4; 
            margin: 15mm; 
        }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            color: #334155; 
            line-height: 1.5; 
            margin: 0; 
            padding: 0;
        }

        /* Header Section */
        .header { 
            border-bottom: 3px solid #4f46e5; 
            padding-bottom: 20px; 
            margin-bottom: 30px; 
            position: relative;
            display: table;
            width: 100%;
        }
        
        .logo-container {
            display: table-cell;
            vertical-align: middle;
            width: 15%;
        }

        .logo { 
            width: 70px; 
            height: auto;
            border-radius: 12px;
        }

        .company-info { 
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 85%;
        }

        .company-name { 
            color: #1e1b4b; 
            font-size: 20px; 
            font-weight: 900; 
            text-transform: uppercase; 
            margin: 0; 
            letter-spacing: -0.5px;
        }

        .report-title { 
            color: #6366f1; 
            font-size: 10px; 
            font-weight: bold; 
            text-transform: uppercase; 
            margin: 5px 0 0 0; 
            letter-spacing: 2px;
        }

        /* Info Grid */
        .info-table { 
            width: 100%; 
            margin-bottom: 30px; 
            border-collapse: collapse; 
            background: #f8fafc;
            border-radius: 20px;
        }

        .info-table td { 
            padding: 15px 20px; 
            vertical-align: top;
            border: 1px solid #f1f5f9;
        }

        .label { 
            font-weight: 900; 
            font-size: 9px; 
            color: #94a3b8; 
            text-transform: uppercase; 
            margin-bottom: 4px;
            display: block;
            letter-spacing: 1px;
        }

        .value { 
            font-size: 12px; 
            font-weight: bold; 
            color: #1e293b;
        }

        /* Status Badges */
        .status { 
            display: inline-block;
            padding: 4px 12px; 
            border-radius: 8px; 
            font-size: 10px; 
            font-weight: 900; 
            text-transform: uppercase; 
        }
        .late { background-color: #fee2e2; color: #b91c1c; }
        .ontime { background-color: #dcfce7; color: #15803d; }

        /* Clock Section */
        .clock-container {
            width: 100%;
            margin-bottom: 30px;
            text-align: center;
        }
        .clock-box {
            display: inline-block;
            width: 45%;
            padding: 15px;
        }
        .time-value {
            font-size: 32px;
            font-weight: 900;
            color: #1e293b;
            font-family: 'Courier', monospace;
        }

        /* Photo Section */
        .photo-section { 
            width: 100%; 
            border-spacing: 15px 0;
            display: table;
        }

        .photo-box { 
            display: table-cell;
            width: 50%;
            background: #f1f5f9;
            padding: 15px;
            border-radius: 25px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }

        /* Fix Foto: Menjaga rasio dan frame */
        .photo-frame {
            width: 100%;
            height: 280px;
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            margin-top: 10px;
            border: 2px solid #fff;
        }

        .photo-frame img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
        }

        .no-image {
            line-height: 280px;
            color: #cbd5e1;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Footer */
        .location-footer {
            margin-top: 30px;
            padding: 15px 20px;
            background: #f8fafc;
            border-radius: 15px;
            border-left: 4px solid #4f46e5;
        }

        .footer { 
            margin-top: 40px; 
            font-size: 9px; 
            color: #94a3b8; 
            text-align: center; 
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-container">
            {{-- Menggunakan public_path untuk memastikan DomPDF bisa membaca file lokal --}}
            <img src="{{ public_path('assets/img/logo-happymomo.png.jpg') }}" class="logo">
        </div>
        <div class="company-info">
            <h1 class="company-name">PT. Happy Momo Indonesia</h1>
            <p class="report-title">E-Attendance Official Report</p>
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td>
                <span class="label">Nama Karyawan</span>
                <div class="value">{{ strtoupper($attendance->user->name) }}</div>
            </td>
            <td>
                <span class="label">Tanggal Presensi</span>
                <div class="value">{{ $attendance->date->translatedFormat('l, d F Y') }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Nomor Induk Karyawan (NIK)</span>
                <div class="value">{{ $attendance->user->nik }}</div>
            </td>
            <td>
                <span class="label">Status Verifikasi</span>
                <span class="status {{ $attendance->is_late ? 'late' : 'ontime' }}">
                    {{ $attendance->is_late ? 'Terlambat' : 'Tepat Waktu' }}
                </span>
            </td>
        </tr>
    </table>

    <div class="clock-container">
        <div class="clock-box">
            <span class="label">Waktu Masuk</span>
            <div class="time-value">{{ $attendance->clock_in }}</div>
        </div>
        <div class="clock-box">
            <span class="label">Waktu Pulang</span>
            <div class="time-value">{{ $attendance->clock_out ?? '--:--' }}</div>
        </div>
    </div>

    <table class="photo-section">
        <tr>
            <td class="photo-box">
                <span class="label">Bukti Foto Masuk</span>
                <div class="photo-frame">
                    @if($attendance->image_in && file_exists(public_path('storage/' . $attendance->image_in)))
                        <img src="{{ public_path('storage/' . $attendance->image_in) }}">
                    @else
                        <div class="no-image">Tidak Ada Foto</div>
                    @endif
                </div>
            </td>
            <td class="photo-box">
                <span class="label">Bukti Foto Pulang</span>
                <div class="photo-frame">
                    @if($attendance->image_out && file_exists(public_path('storage/' . $attendance->image_out)))
                        <img src="{{ public_path('storage/' . $attendance->image_out) }}">
                    @else
                        <div class="no-image">Tidak Ada Foto</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="location-footer">
        <span class="label">Titik Koordinat Lokasi</span>
        <div class="value" style="color: #4f46e5; font-family: monospace;">
            <img src="https://cdn-icons-png.flaticon.com/512/684/684908.png" width="10" style="margin-right: 5px;">
            {{ $attendance->latitude }}, {{ $attendance->longitude }}
        </div>
    </div>

    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh <strong>Sistem HRD Happy Momo</strong> pada {{ now()->translatedFormat('d F Y H:i') }}.<br>
        Laporan ini sah dan terekam secara permanen dalam database sistem cloud perusahaan.<br>
        <strong>© 2026 PT. Happy Momo Indonesia - HR Management System</strong>
    </div>
</body>
</html>