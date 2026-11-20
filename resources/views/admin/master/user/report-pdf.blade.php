<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Laporan Kontrak Karyawan' }}</title>
    <style>
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 10px; 
            color: #1e293b; 
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        
        .header { 
            text-align: center; 
            margin-bottom: 25px; 
            border-bottom: 2px solid #0f172a; 
            padding-bottom: 12px; 
        }
        
        .company-name { 
            font-size: 16px; 
            font-weight: bold; 
            color: #4f46e5; 
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .report-title {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 1px;
            margin-top: 6px;
            text-transform: uppercase;
        }

        .meta-info {
            font-size: 9px;
            color: #64748b;
            margin-top: 4px;
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px; 
        }

        th { 
            background-color: #f1f5f9; 
            color: #475569; 
            text-transform: uppercase; 
            padding: 8px 6px; 
            border: 1px solid #cbd5e1; 
            font-size: 8.5px; 
            font-weight: bold;
            text-align: center;
        }

        td { 
            padding: 7px 6px; 
            border: 1px solid #cbd5e1; 
            text-align: center; 
            font-size: 9.5px;
        }

        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        /* Status Warna Baris & Kolom */
        .urgent { 
            color: #e11d48; 
            font-weight: bold; 
            background-color: #fff1f2; 
        }

        .expired-row { 
            background-color: #f8fafc; 
            color: #94a3b8; 
        }

        .expired-badge {
            background-color: #334155; 
            color: #ffffff; 
            font-weight: bold;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 8.5px;
        }

        .safe-badge {
            color: #047857;
            font-weight: bold;
        }

        .footer { 
            margin-top: 25px; 
            text-align: right; 
            font-style: italic; 
            font-size: 8.5px; 
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="company-name">{{ $company ?? 'PT. Happy Momo Indonesia' }}</div>
        <div class="report-title">{{ $title ?? 'Laporan Masa Berlaku Kontrak Karyawan' }}</div>
        <div class="meta-info">Dicetak pada: {{ $date ?? now()->translatedFormat('d F Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 10%;">NIK</th>
                <th style="width: 20%;" class="text-left">Nama Karyawan</th>
                <th style="width: 14%;">Cabang</th>
                <th style="width: 13%;">Departemen</th>
                <th style="width: 10%;">Tgl Masuk</th>
                <th style="width: 8%;">Durasi</th>
                <th style="width: 10%;">Jatuh Tempo</th>
                <th style="width: 11%;">Sisa Masa Kerja</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $index => $user)
                @php
                    $isExpired = isset($user->days_left) && $user->days_left < 0;
                    $isUrgent = isset($user->days_left) && $user->days_left <= 30 && $user->days_left >= 0;
                @endphp
                <tr class="{{ $isExpired ? 'expired-row' : '' }}">
                    <td>{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $user->nik ?? '-' }}</td>
                    <td class="text-left"><strong>{{ $user->name ?? '-' }}</strong></td>
                    <td>{{ $user->branch->name ?? $user->unit_kerja ?? '-' }}</td>
                    <td>{{ $user->department->name ?? '-' }}</td>
                    <td>
                        {{ !empty($user->join_date) ? \Carbon\Carbon::parse($user->join_date)->format('d/m/Y') : '-' }}
                    </td>
                    <td>{{ $user->contract_duration ?? '-' }} Bln</td>
                    <td>
                        {{ !empty($user->expiry_date) ? \Carbon\Carbon::parse($user->expiry_date)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="{{ $isUrgent ? 'urgent' : '' }}">
                        @if($isExpired)
                            <span class="expired-badge">KONTRAK HABIS</span>
                        @elseif(isset($user->days_left))
                            <span class="{{ $isUrgent ? '' : 'safe-badge' }}">
                                {{ $user->days_left }} Hari Lagi
                            </span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px; color: #94a3b8; font-style: italic;">
                        Tidak ada data karyawan yang ditemukan untuk laporan ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Sistem HRIS PT. Happy Momo Indonesia — Dokumen ini sah dan dihasilkan secara otomatis oleh sistem pada {{ date('Y') }}.
    </div>

</body>
</html>