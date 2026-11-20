<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekap Absensi</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 8px; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h2 { margin: 0; font-size: 16px; font-weight: bold; text-transform: uppercase; }
        .header p { margin: 5px 0 0 0; font-size: 10px; color: #555; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 0.5px solid #333; padding: 3px; text-align: center; vertical-align: middle; word-wrap: break-word; }
        
        .bg-dark { background-color: #1e293b; color: #ffffff; }
        .bg-gray { background-color: #f1f5f9; }
        .bg-weekend { background-color: #fee2e2; }
        
        .text-left { text-align: left; }
        .text-in { color: #059669; }
        .text-out { color: #d97706; }
        .text-danger { color: #e11d48; font-weight: bold; }
        
        .identitas { padding-left: 5px; }
        .nama { font-weight: bold; font-size: 9px; text-transform: uppercase; display: block; }
        .nik { font-size: 7px; color: #64748b; }
    </style>
</head>
<body>

    <div class="header">
        <h2>LAPORAN REKAPITULASI ABSENSI KARYAWAN</h2>
        <p>Periode Tabulasi: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</p>
    </div>

    @php
        $periodDates = \Carbon\CarbonPeriod::create($startDate, $endDate);
    @endphp

    <table>
        <thead>
            <tr class="bg-dark">
                <th rowspan="2" style="width: 140px;">IDENTITAS KARYAWAN</th>
                @foreach($periodDates as $date)
                    <th colspan="2" class="{{ $date->isWeekend() ? 'bg-danger' : '' }}" style="font-size: 7px;">
                        {{ $date->format('d/m') }}
                    </th>
                @endforeach
            </tr>
            <tr class="bg-dark">
                @foreach($periodDates as $date)
                    <th style="font-size: 6px; background-color: #334155;">IN</th>
                    <th style="font-size: 6px; background-color: #475569;">OUT</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td class="text-left identitas">
                    <span class="nama">{{ $user->name }}</span>
                    <span class="nik">ID: {{ $user->nik ?? '-' }}</span>
                </td>
                
                @foreach($periodDates as $date)
                    @php
                        $dateStr = $date->format('Y-m-d');
                        $isWeekend = $date->isWeekend();
                        
                        $inVal = '-';
                        $outVal = '-';
                        
                        if(isset($attendanceRecords[$user->id][$dateStr])) {
                            $log = $attendanceRecords[$user->id][$dateStr];
                            
                            if ($log->clock_in) {
                                $inVal = \Carbon\Carbon::parse($log->clock_in)->format('H:i');
                                if ($log->clock_out) {
                                    $outVal = \Carbon\Carbon::parse($log->clock_out)->format('H:i');
                                }
                            } else if (in_array(strtolower($log->status), ['izin', 'sakit', 'cuti', 'alpha', 'alpha/mangkir'])) {
                                $inVal = strtoupper(substr($log->status, 0, 1));
                            }
                        }
                    @endphp
                    <td class="{{ $isWeekend ? 'bg-weekend' : '' }} text-in">
                        {{ $inVal }}
                    </td>
                    <td class="{{ $isWeekend ? 'bg-weekend' : '' }} text-out">
                        {{ $outVal }}
                    </td>
                @endforeach
            </tr>
            @empty
            <tr>
                <td colspan="{{ (count($periodDates) * 2) + 1 }}" style="padding: 20px;">Belum Ada Data Karyawan</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>