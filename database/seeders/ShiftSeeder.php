<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = [
            [
                'name' => 'Shift Pagi (Regular)',
                'code' => 'SH-PAGI',
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'break_start' => '12:00:00',
                'break_end' => '13:00:00',
                'late_tolerance' => 15, // Toleransi telat 15 menit
                'color' => '#10b981',   // Emerald Green
                'is_night_shift' => false,
                'description' => 'Shift kantor reguler Senin-Jumat',
            ],
            [
                'name' => 'Shift Sabtu (Setengah Hari)',
                'code' => 'SH-SABTU',
                'start_time' => '08:00:00',
                'end_time' => '13:00:00',
                'break_start' => null, // Tidak ada istirahat
                'break_end' => null,
                'late_tolerance' => 10,
                'color' => '#f59e0b',   // Amber
                'is_night_shift' => false,
                'description' => 'Shift khusus hari Sabtu',
            ],
            [
                'name' => 'Shift Malam (Gudang)',
                'code' => 'SH-MALAM',
                'start_time' => '22:00:00',
                'end_time' => '06:00:00',
                'break_start' => '02:00:00',
                'break_end' => '03:00:00',
                'late_tolerance' => 15,
                'color' => '#6366f1',   // Indigo
                'is_night_shift' => true, // Lintas hari
                'description' => 'Shift malam untuk tim operasional gudang',
            ],
            [
                'name' => 'Libur / Off',
                'code' => 'SH-OFF',
                'start_time' => '00:00:00',
                'end_time' => '00:00:00',
                'break_start' => null,
                'break_end' => null,
                'late_tolerance' => 0,
                'color' => '#94a3b8',   // Slate Gray
                'is_night_shift' => false,
                'is_holiday' => true, // Penanda Libur
                'description' => 'Jadwal libur mingguan',
            ],
        ];

        foreach ($shifts as $shift) {
            // Gunakan updateOrCreate agar tidak duplikat saat di-seed ulang
            Shift::updateOrCreate(
                ['code' => $shift['code']], // Cek berdasarkan kode
                $shift
            );
        }
    }
}