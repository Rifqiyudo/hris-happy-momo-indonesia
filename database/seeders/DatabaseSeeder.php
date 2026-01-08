<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Urutan Panggilan (PENTING)
        // Master data harus duluan sebelum User
        $this->call([
            PositionSeeder::class,  // Buat jabatan dulu
            ShiftSeeder::class,     // Buat jam kerja
            AllowanceSeeder::class, // Buat tunjangan
            UserSeeder::class,      // Baru buat user yang pakai jabatan
        ]);
    }
}