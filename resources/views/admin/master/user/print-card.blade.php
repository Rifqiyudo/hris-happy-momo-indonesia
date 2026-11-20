<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Card - {{ $user->name }}</title>
    <style>
        /* Standar Ukuran Kartu PVC Portrait ISO ID-1 (54mm x 85.6mm) */
        @page { 
            size: 54mm 85.6mm; 
            margin: 0; 
        }
        
        body { 
            font-family: 'Inter', 'Segoe UI', Helvetica, Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            background: #ffffff; 
            color: #1e293b; 
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .card-container {
            width: 54mm;
            height: 85.6mm;
            background: #fff;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            border: 0.1mm solid #f1f5f9;
        }

        /* Header Accent Oranye Happy Momo */
        .header-bg {
            height: 24mm;
            background: #ff6b00; 
            width: 100%;
            position: absolute;
            top: 0;
            z-index: 1;
            clip-path: polygon(0 0, 100% 0, 100% 80%, 0% 100%);
        }

        .content {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            height: 100%;
            padding-top: 3mm;
        }

        .logo-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 1.5mm;
            background: white;
            padding: 1.2mm;
            border-radius: 50%;
            box-shadow: 0 0.5mm 1.5mm rgba(0,0,0,0.1);
        }
        
        .logo-box img {
            height: 7mm; 
            width: 7mm;
            object-fit: contain;
        }

        .company-name {
            font-size: 5.5pt;
            font-weight: 800;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-top: 0.5mm;
        }

        /* Photo Section */
        .photo-box {
            width: 22mm; 
            height: 28mm;
            background: #f8fafc;
            border-radius: 1.5mm;
            overflow: hidden;
            border: 1.5pt solid white;
            box-shadow: 0 1.2mm 3mm rgba(0,0,0,0.15);
            margin-bottom: 3mm;
            margin-top: 1.5mm;
        }
        .photo-box img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
        }

        /* User Info */
        .info-section {
            text-align: center;
            width: 100%;
            padding: 0 3mm;
            box-sizing: border-box;
        }

        .info-section h1 { 
            margin: 0; 
            font-size: 8pt; 
            font-weight: 800; 
            text-transform: uppercase; 
            color: #0f172a;
            line-height: 1.1;
        }

        .info-section .position { 
            font-size: 5pt; 
            font-weight: 700; 
            color: #ff6b00; 
            margin-top: 1.5mm;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #fff7ed;
            padding: 0.4mm 2mm;
            border-radius: 0.8mm;
            display: inline-block;
        }

        /* Metadata Table */
        .user-meta {
            margin-top: 3mm;
            width: 85%;
            border-top: 0.15pt solid #f1f5f9;
            padding-top: 2mm;
            font-size: 5pt;
            color: #64748b;
        }
        
        .user-meta div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.8mm;
        }

        .user-meta strong { 
            color: #475569; 
            font-weight: 700; 
            text-transform: uppercase;
            font-size: 4.2pt;
        }

        /* QR AREA - PERMANEN BERDASARKAN NIK */
        .qr-footer {
            margin-top: auto;
            margin-bottom: 3.5mm;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .qr-code-wrapper {
            background: #ffffff;
            display: inline-block;
            line-height: 0;
            padding: 1mm;
            border: 0.5pt solid #f1f5f9;
            border-radius: 1mm;
        }
        
        .qr-code-wrapper svg {
            width: 16mm !important;
            height: 16mm !important;
            shape-rendering: crispEdges;
        }

        .qr-label {
            font-size: 4pt;
            font-weight: 800;
            color: #cbd5e1;
            margin-top: 1.5mm;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        @media print {
            body { background: none; }
            html, body {
                height: 85.6mm;
                width: 54mm;
            }
            .card-container {
                border: none;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="card-container">
        <div class="header-bg"></div>

        <div class="content">
            <div class="logo-box">
                <img src="{{ asset('assets/img/logo-happymomo.png') }}" alt="Logo">
            </div>
            <div class="company-name">PT. Happy Momo Indonesia</div>

            <div class="photo-box">
                @php
                    $photoPath = $user->avatar && Storage::disk('public')->exists($user->avatar) 
                                 ? asset('storage/' . $user->avatar) 
                                 : asset('assets/img/default-avatar.png');
                @endphp
                <img src="{{ $photoPath }}" alt="Photo Profile">
            </div>

            <div class="info-section">
                <h1>{{ Str::limit(strtoupper($user->name), 35) }}</h1>
                <div class="position">{{ $user->position->name ?? 'Karyawan' }}</div>
            </div>

            <div class="user-meta">
                <div>
                    <strong>Employee ID</strong>
                    <span>{{ trim($user->nik) }}</span>
                </div>
                <div>
                    <strong>Department</strong>
                    <span>{{ Str::limit($user->department->name ?? '-', 18) }}</span>
                </div>
                <div>
                    <strong>Issued Date</strong>
                    <span>{{ now()->format('d/m/Y') }}</span>
                </div>
            </div>

            <div class="qr-footer">
                <div class="qr-code-wrapper">
                    {{-- Menggunakan Full Namespace Facade SimpleQR untuk keamanan render --}}
                    {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(100)->margin(0)->generate(trim($user->nik)) !!}
                </div>
                <div class="qr-label">Scan for Attendance</div>
            </div>
        </div>
    </div>

</body>
</html>