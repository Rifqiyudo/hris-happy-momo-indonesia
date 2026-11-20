<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Allowance;

class AllowanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allowances = [
            // --- 1. TUNJANGAN HARIAN (DAILY) ---
            // Dihitung berdasarkan jumlah kehadiran (dikali hari masuk)
            [
                'name'        => 'Uang Makan Harian',
                'amount'      => 35000, 
                'type'        => 'daily',
                'is_taxable'  => true, // Kena PPh21
                'description' => 'Diberikan sesuai jumlah kehadiran karyawan.',
            ],
            [
                'name'        => 'Uang Transport',
                'amount'      => 25000, 
                'type'        => 'daily',
                'is_taxable'  => true,
                'description' => 'Pengganti biaya bensin/transportasi harian.',
            ],

            // --- 2. TUNJANGAN TETAP (FIXED) ---
            // Diterima utuh setiap bulan (Fixed Allowance)
            [
                'name'        => 'Tunjangan Jabatan',
                'amount'      => 1500000,
                'type'        => 'fixed', // Ubah 'monthly' jadi 'fixed' sesuai migrasi
                'is_taxable'  => true,
                'description' => 'Diberikan kepada level Supervisor ke atas.',
            ],
            [
                'name'        => 'Tunjangan Komunikasi / Pulsa',
                'amount'      => 100000,
                'type'        => 'fixed',
                'is_taxable'  => true,
                'description' => 'Untuk karyawan yang membutuhkan koordinasi tinggi.',
            ],
            [
                'name'        => 'Tunjangan Kehadiran (Rajin)',
                'amount'      => 250000,
                'type'        => 'fixed',
                'is_taxable'  => true,
                'description' => 'Diberikan jika karyawan hadir full 1 bulan tanpa telat.',
            ],
            
            // --- 3. CONTOH NON-PAJAK ---
            [
                'name'        => 'Reimbursement Medis (Rawat Jalan)',
                'amount'      => 0, // Nominal variatif, diset 0 dulu
                'type'        => 'fixed',
                'is_taxable'  => false, // Tidak kena pajak (Natura/Benefit tertentu)
                'description' => 'Klaim pengobatan ringan.',
            ],
        ];

        foreach ($allowances as $item) {
            // updateOrCreate: Cek berdasarkan 'name', jika ada update, jika tidak create.
            Allowance::updateOrCreate(
                ['name' => $item['name']], // Kunci pencarian
                $item                      // Data yang disimpan
            );
        }
    }
}