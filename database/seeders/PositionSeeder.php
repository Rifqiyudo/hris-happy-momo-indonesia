<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name' => 'Manager Operasional',
                'base_salary' => 8000000,
            ],
            [
                'name' => 'Admin HR / Finance',
                'base_salary' => 5000000,
            ],
            [
                'name' => 'Kepala Gudang',
                'base_salary' => 5500000,
            ],
            [
                'name' => 'Staff Gudang',
                'base_salary' => 4500000, // UMR (Contoh)
            ],
            [
                'name' => 'Driver / Kurir',
                'base_salary' => 4500000,
            ],
            [
                'name' => 'Helper / Kernet',
                'base_salary' => 3800000,
            ]
        ];

        foreach ($data as $item) {
            Position::create($item);
        }
    }
}