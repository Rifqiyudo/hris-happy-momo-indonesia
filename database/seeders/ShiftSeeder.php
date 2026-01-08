<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Senin s/d Jumat (Hari Kerja Biasa)
        // Pulang jam 17:00
        for ($i = 1; $i <= 5; $i++) {
            Shift::create([
                'name' => 'Regular Weekday',
                'day_number' => $i, // 1=Senin, 2=Selasa, dst
                'clock_in' => '08:00:00',
                'clock_out' => '17:00:00',
                'is_holiday' => false,
            ]);
        }

        // 2. Sabtu (Short Day)
        // Pulang jam 16:00 (Sesuai request)
        Shift::create([
            'name' => 'Happy Saturday',
            'day_number' => 6, // Sabtu
            'clock_in' => '08:00:00',
            'clock_out' => '16:00:00',
            'is_holiday' => false,
        ]);

        // 3. Minggu (Libur)
        Shift::create([
            'name' => 'Sunday Holiday',
            'day_number' => 7,
            'clock_in' => '00:00:00',
            'clock_out' => '00:00:00',
            'is_holiday' => true,
        ]);
    }
}