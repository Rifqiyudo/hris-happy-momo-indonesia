<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Shift;
use App\Models\WorkingPattern;
use Carbon\Carbon;

class WorkingPatternSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Kosongkan tabel terlebih dahulu agar tidak duplikat saat di-seed ulang
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        WorkingPattern::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Ambil referensi ID dari Master Shift yang sudah dibuat oleh ShiftSeeder
        // Menggunakan "like" agar lebih fleksibel jika ada sedikit perbedaan penamaan
        $shiftReguler = Shift::where('name', 'like', '%Reguler Staff%')
                             ->orWhere('name', 'like', '%Pagi%')
                             ->first()->id ?? null;

        $shiftSabtu   = Shift::where('name', 'like', '%Reguler Sabtu%')
                             ->orWhere('name', 'like', '%Sabtu%')
                             ->first()->id ?? null;

        $shiftMalam   = Shift::where('name', 'like', '%Malam%')
                             ->first()->id ?? null;

        $shiftLibur   = Shift::where('name', 'like', '%Libur%')
                             ->orWhere('name', 'like', '%Off%')
                             ->first()->id ?? null;

        // 3. Definisikan Cluster Pola Mingguan
        $patterns = [
            [
                'name'         => 'Pola Reguler (Senin - Sabtu)',
                'mon_shift_id' => $shiftReguler, // Senin: 08.00 - 17.00
                'tue_shift_id' => $shiftReguler, // Selasa: 08.00 - 17.00
                'wed_shift_id' => $shiftReguler, // Rabu: 08.00 - 17.00
                'thu_shift_id' => $shiftReguler, // Kamis: 08.00 - 17.00
                'fri_shift_id' => $shiftReguler, // Jumat: 08.00 - 17.00
                'sat_shift_id' => $shiftSabtu,   // Sabtu: 08.00 - 16.00 (Berbeda Sendiri)
                'sun_shift_id' => $shiftLibur,   // Minggu: Libur
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),
            ],
            [
                'name'         => 'Pola Reguler (Senin - Jumat)',
                'mon_shift_id' => $shiftReguler, 
                'tue_shift_id' => $shiftReguler, 
                'wed_shift_id' => $shiftReguler, 
                'thu_shift_id' => $shiftReguler, 
                'fri_shift_id' => $shiftReguler, 
                'sat_shift_id' => $shiftLibur,   // Sabtu: Libur
                'sun_shift_id' => $shiftLibur,   // Minggu: Libur
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),
            ],
            [
                'name'         => 'Pola Produksi (Shift Malam)',
                'mon_shift_id' => $shiftMalam,   // Senin: 22.00 - 06.00
                'tue_shift_id' => $shiftMalam,   
                'wed_shift_id' => $shiftMalam,   
                'thu_shift_id' => $shiftMalam,   
                'fri_shift_id' => $shiftMalam,   
                'sat_shift_id' => $shiftLibur,   // Sabtu: Libur
                'sun_shift_id' => $shiftLibur,   // Minggu: Libur
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),
            ]
        ];

        // 4. Masukkan data (Insert) ke tabel working_patterns
        WorkingPattern::insert($patterns);
    }
}