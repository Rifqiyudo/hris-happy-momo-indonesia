<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Allowance;

class AllowanceSeeder extends Seeder
{
    public function run(): void
    {
        // Tunjangan Harian (Cair dikali jumlah kehadiran)
        Allowance::create([
            'name' => 'Uang Makan Harian',
            'amount' => 25000, 
            'type' => 'daily',
        ]);
        
        Allowance::create([
            'name' => 'Uang Transport',
            'amount' => 15000, 
            'type' => 'daily',
        ]);

        // Tunjangan Bulanan (Fixed)
        Allowance::create([
            'name' => 'Tunjangan Jabatan',
            'amount' => 500000,
            'type' => 'monthly',
        ]);
        
        Allowance::create([
            'name' => 'Tunjangan BPJS Kesehatan',
            'amount' => 150000,
            'type' => 'monthly',
        ]);
    }
}