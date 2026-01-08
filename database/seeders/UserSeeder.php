<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Position;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID Jabatan untuk relasi
        $adminPos = Position::where('name', 'like', '%Admin%')->first();
        $staffPos = Position::where('name', 'like', '%Staff Gudang%')->first();

        // 1. AKUN ADMIN (Super Admin)
        User::create([
            'name' => 'Administrator HR',
            'email' => 'admin@happymomo.com',
            'password' => Hash::make('password'), // Password: password
            'role' => 'admin',
            'position_id' => $adminPos ? $adminPos->id : 1,
            'nik' => 'ADM001',
            'phone' => '081234567890',
            'address' => 'Kantor Margomulyo',
            'is_active' => true,
        ]);

        // 2. AKUN KARYAWAN (Contoh Staff Gudang)
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@happymomo.com',
            'password' => Hash::make('password'), // Password: password
            'role' => 'employee',
            'position_id' => $staffPos ? $staffPos->id : 2,
            'nik' => 'GDG001',
            'phone' => '089876543210',
            'address' => 'Jl. Tandes Lor No. 5',
            'bank_name' => 'BCA',
            'bank_account' => '1234567890',
            'is_active' => true,
        ]);
    }
}