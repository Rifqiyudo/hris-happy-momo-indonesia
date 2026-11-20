<!DOCTYPE html>
<html>
<head>
    <title>Cetak Kartu - {{ $user->name }}</title>
    <style>
        @page { size: 8.6cm 5.4cm; margin: 0; }
        body { margin: 0; font-family: 'Arial', sans-serif; }
        .card {
            width: 8.6cm; height: 5.4cm;
            background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
            color: white; position: relative; overflow: hidden;
        }
        .photo {
            position: absolute; top: 0.5cm; left: 0.5cm;
            width: 2.2cm; height: 2.8cm; border: 2px solid white;
            object-fit: cover; background: #eee;
        }
        .info { position: absolute; left: 3cm; top: 0.5cm; }
        .name { font-size: 10pt; font-weight: bold; margin-bottom: 2px; }
        .position { font-size: 7pt; opacity: 0.9; margin-bottom: 10px; }
        .barcode-box {
            position: absolute; bottom: 0.4cm; left: 50%;
            transform: translateX(-50%); background: white;
            padding: 5px; border-radius: 3px; text-align: center;
        }
        .barcode-img { width: 4cm; height: 0.8cm; }
        .nik-text { color: black; font-size: 7pt; font-weight: bold; margin-top: 2px; }
        .logo { position: absolute; right: 0.5cm; top: 0.5cm; font-size: 8pt; font-weight: bold; }
    </style>
</head>
<body onload="window.print()">
    <div class="card">
        <div class="logo">HAPPY MOMO</div>
        
        <img src="{{ public_path('storage/'.$user->photo) }}" class="photo">
        
        <div class="info">
            <div class="name">{{ strtoupper($user->name) }}</div>
            <div class="position">{{ $user->position->name ?? 'STAFF' }}</div>
        </div>

        <div class="barcode-box">
            <img src="data:image/png;base64,{{ $barcode }}" class="barcode-img">
            <div class="nik-text">{{ $user->nik }}</div>
        </div>
    </div>
</body>
</html>