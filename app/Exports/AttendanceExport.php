<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $month;
    protected $year;

    // Menerima parameter bulan & tahun dari Controller
    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    /**
    * Mengambil Data dari Database
    */
    public function collection()
    {
        return Attendance::with('user.position')
                ->whereMonth('date', $this->month)
                ->whereYear('date', $this->year)
                ->orderBy('date', 'asc')
                ->get();
    }

    /**
    * Judul Kolom di Excel (Header)
    */
    public function headings(): array
    {
        return [
            'Tanggal',
            'NIK',
            'Nama Karyawan',
            'Jabatan',
            'Jam Masuk',
            'Jam Pulang',
            'Status',
            'Keterlambatan (Menit)',
        ];
    }

    /**
    * Mapping Data (Menentukan isi per baris)
    */
    public function map($attendance): array
    {
        return [
            $attendance->date->format('d-m-Y'),
            $attendance->user->nik ?? '-',
            $attendance->user->name,
            $attendance->user->position->name ?? '-',
            $attendance->clock_in ?? '-',
            $attendance->clock_out ?? '-',
            ucfirst($attendance->status), // Huruf depan besar (Present -> Present)
            $attendance->late_minutes . ' Menit',
        ];
    }
}