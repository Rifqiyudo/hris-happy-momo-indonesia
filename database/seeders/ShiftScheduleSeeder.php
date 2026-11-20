<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;
use App\Models\ShiftSchedule;

class ShiftScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil Shift Default (Pastikan Seeder Shift sudah dijalankan duluan)
        $regular = Shift::where('name', 'LIKE', '%Pagi%')->first() ?? Shift::first();
        $libur = Shift::where('is_holiday', true)->first() ?? Shift::first();

        $days = [
            1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 
            4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
        ];

        foreach ($days as $num => $name) {
            // Senin-Jumat Masuk, Sabtu-Minggu Libur (Default)
            $selectedShift = ($num <= 5) ? $regular->id : $libur->id;

            ShiftSchedule::create([
                'day_name' => $name,
                'day_number' => $num,
                'shift_id' => $selectedShift
            ]);
        }
    }
}