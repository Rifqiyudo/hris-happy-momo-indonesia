<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    protected $startDate;
    protected $endDate;
    protected $periodDates;
    protected $attendancesData;

    // Menerima parameter start_date & end_date dari Controller (bukan month & year)
    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        
        // Parsing ke objek Carbon terlebih dahulu agar tidak memicu InvalidTimeZoneException
        $startObj = Carbon::parse($this->startDate);
        $endObj = Carbon::parse($this->endDate);
        
        // Buat array tanggal dari awal sampai akhir periode
        $this->periodDates = CarbonPeriod::create($startObj, $endObj)->toArray();

        // Siapkan data absensi di memori agar cepat saat proses mapping
        $attendances = Attendance::whereBetween('date', [$this->startDate, $this->endDate])->get();
        $this->attendancesData = [];
        
        foreach ($attendances as $att) {
            $this->attendancesData[$att->user_id][$att->date->format('Y-m-d')] = $att;
        }
    }

    /**
     * Menyusun Baris Data (ID, NIK, Nama, dan Jam IN OUT / Status per tanggal)
     */
    public function collection()
    {
        // Ambil semua user aktif atau user hasil generate mesin FP
        $users = User::with('position')
                   ->where(function ($query) {
                       $query->where('is_active', true)
                             ->orWhere('nik', 'like', 'FP-%'); 
                   })
                   ->orderBy('name', 'asc')
                   ->get();

        $rows = collect();

        // Looping per karyawan (Satu karyawan = Satu baris)
        foreach ($users as $user) {
            $row = [
                'id'       => $user->id,
                'nik'      => $user->nik ?? '-',
                'name'     => strtoupper($user->name),
                'position' => $user->position->name ?? 'STAFF'
            ];

            // Looping setiap hari pada rentang tanggal (Misal: 26 Okt s.d 25 Nov)
            foreach ($this->periodDates as $date) {
                $dateStr = $date->format('Y-m-d');
                $cellValue = '-';

                if (isset($this->attendancesData[$user->id][$dateStr])) {
                    $log = $this->attendancesData[$user->id][$dateStr];
                    
                    // Deteksi jika hadir (ada jam masuk)
                    if ($log->clock_in) {
                        $in = Carbon::parse($log->clock_in)->format('H:i');
                        $cellValue = $in;
                        
                        // Gabungkan jam IN dan OUT (contoh: 08:00 17:00)
                        if ($log->clock_out) {
                            $out = Carbon::parse($log->clock_out)->format('H:i');
                            $cellValue = $in . ' ' . $out; 
                        }
                    } 
                    // Deteksi status tidak hadir (Cuti, Izin, Sakit, Alpha)
                    else if (in_array(strtolower($log->status), ['izin', 'sakit', 'cuti', 'alpha', 'alpha/mangkir'])) {
                        $cellValue = strtoupper(substr($log->status, 0, 1));
                    }
                }
                
                $row['date_' . $dateStr] = $cellValue;
            }

            $rows->push($row);
        }

        return $rows;
    }

    /**
     * Judul Kolom di Excel (Header Berlapis: Bulan/Tahun & Angka Tanggal)
     */
    public function headings(): array
    {
        // Baris Pertama Header (Bulan/Tahun di atas angka tanggal)
        $headerRow1 = ['ID', 'NIK', 'Nama Karyawan', 'Posisi / Jabatan'];
        
        // Baris Kedua Header (Angka Tanggal)
        $headerRow2 = ['', '', '', '']; 

        foreach ($this->periodDates as $date) {
            $headerRow1[] = $date->format('M Y');
            $headerRow2[] = $date->format('d');
        }

        return [
            $headerRow1,
            $headerRow2
        ];
    }

    /**
     * Styling Sederhana untuk Header Excel agar terlihat rapi dan elegan
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:A2')->getFont()->setBold(true);
        $highestColumn = $sheet->getHighestColumn();
        
        $sheet->getStyle('A1:' . $highestColumn . '2')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF1E293B', // Slate 900
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        // Merge Cell Header untuk 4 kolom identitas karyawan pertama
        $sheet->mergeCells('A1:A2');
        $sheet->mergeCells('B1:B2');
        $sheet->mergeCells('C1:C2');
        $sheet->mergeCells('D1:D2');

        return [];
    }

    /**
     * Menambahkan border ke seluruh sel yang terisi data (Register Events)
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $cellRange = 'A1:' . $highestColumn . $highestRow;
                
                $sheet->getStyle($cellRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FFCBD5E1'], // Slate 300
                        ],
                    ],
                ]);
            },
        ];
    }
}