<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Position;
use App\Models\Branch;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // =====================================================================
        // 1. SIAPKAN DATA RELASI (Mencegah Error Integrity Constraint)
        // =====================================================================
        
        // Ambil Cabang berdasarkan Seeder BranchSeeder sebelumnya
        // Fallback ke cabang pertama jika ID spesifik tidak ditemukan
        $branchPusat     = Branch::find(1) ?? Branch::first();
        $branchLagoon    = Branch::find(4) ?? Branch::first();
        $branchSepanjang = Branch::find(5) ?? Branch::first();

        // Jika benar-benar tidak ada cabang sama sekali, buat darurat
        if (!$branchPusat) {
            $branchPusat = Branch::create([
                'name'      => 'Kantor Pusat Margomulyo',
                'code'      => 'HO-SUB',
                'address'   => 'Jl. Margomulyo Indah No. 1, Surabaya',
                'latitude'  => -7.234567,
                'longitude' => 112.678901,
                'radius'    => 100,
                'is_active' => true
            ]);
            $branchLagoon = $branchPusat;
            $branchSepanjang = $branchPusat;
        }

        // Ambil Jabatan & Departemen (Gunakan fallback agar tidak NULL)
        $posAdmin = Position::where('name', 'like', '%Admin%')->first() ?? Position::first();
        $posStaff = Position::where('name', 'like', '%Staff%')->first() ?? Position::first();
        $posSpv   = Position::where('name', 'like', '%Supervisor%')->first() ?? $posAdmin;
        
        $deptHR   = Department::where('name', 'like', '%HR%')->first() ?? Department::first();
        $deptFin  = Department::where('name', 'like', '%Finance%')->first() ?? Department::first();
        $deptOps  = Department::where('name', 'like', '%Gudang%')->first() ?? 
                    Department::where('name', 'like', '%Operasional%')->first() ?? 
                    Department::first();

        // =====================================================================
        // 2. BUAT AKUN ADMIN UTAMA (Super Admin)
        // =====================================================================
        User::firstOrCreate(
            ['email' => 'admin@happymomo.com'],
            [
                'name'              => 'Administrator HR',
                'password'          => Hash::make('password2026'),
                'role'              => 'admin',
                'nik'               => 'ADM001',
                'phone'             => '081234567890',
                'address'           => 'Kantor Margomulyo Indah',
                'is_active'         => true,
                'join_date'         => '2023-01-01',
                'employee_status'   => 'Permanent',
                'last_education'    => 'S1',
                'educational_major' => 'Psikologi',
                'branch_id'         => $branchPusat->id,
                'position_id'       => $posAdmin ? $posAdmin->id : 1,
                'department_id'     => $deptHR ? $deptHR->id : 1,
                'barcode_token'     => Str::random(32),
            ]
        );

        // =====================================================================
        // 3. BUAT AKUN MAS RIFQI (Akses Penuh / Admin)
        // =====================================================================
        // Dibuat berdasarkan profil aktual agar tidak muncul "Unit Belum Terdaftar"
        $joinDateExpired = '2024-01-01';
        
        User::firstOrCreate(
            ['email' => 'rifqi@happymomo.com'],
            [
                'name'              => 'RIFQI YUDO DEWANTORO',
                'password'          => Hash::make('password'),
                'role'              => 'admin', // Diset admin agar leluasa mengakses sistem
                'nik'               => 'IT001',
                'phone'             => '082233445566',
                'address'           => 'Surabaya Kota',
                'is_active'         => true,
                'join_date'         => '2025-12-22',
                'employee_status'   => 'Contract',
                'contract_duration' => 12, 
                'last_education'    => 'D4',
                'educational_major' => 'Manajemen Informatika',
                'branch_id'         => $branchPusat->id, // Ditetapkan di Pusat
                'position_id'       => $posSpv ? $posSpv->id : 1,
                'department_id'     => $deptFin ? $deptFin->id : 1,
                'barcode_token'     => Str::random(32),
            ]
        );

        // =====================================================================
        // 4. BUAT AKUN KARYAWAN KONTRAK (Simulasi Jatuh Tempo 30 Hari Lagi)
        // =====================================================================
        // Join 5 bulan lalu dengan durasi kontrak 6 bulan (Sisa 1 bulan/30 hari)
        $joinDateSoon = now()->subMonths(5)->format('Y-m-d');
        
        User::firstOrCreate(
            ['email' => 'budi@happymomo.com'],
            [
                'name'              => 'Budi Santoso',
                'password'          => Hash::make('password'),
                'role'              => 'employee',
                'nik'               => 'GDG001',
                'phone'             => '089876543210',
                'address'           => 'Jl. Tandes Lor No. 5',
                'is_active'         => true,
                'join_date'         => $joinDateSoon,
                'employee_status'   => 'Contract',
                'contract_duration' => 6,
                'last_education'    => 'SMA',
                'educational_major' => 'IPS',
                'branch_id'         => $branchPusat->id, // Ditetapkan di Pusat
                'position_id'       => $posStaff ? $posStaff->id : 1,
                'department_id'     => $deptOps ? $deptOps->id : 1,
                'barcode_token'     => Str::random(32),
            ]
        );

        // =====================================================================
        // 5. BUAT AKUN KARYAWAN TETAP (Cabang Lagoon)
        // =====================================================================
        User::firstOrCreate(
            ['email' => 'siti@happymomo.com'],
            [
                'name'              => 'Siti Aminah',
                'password'          => Hash::make('password'),
                'role'              => 'employee',
                'nik'               => 'STF002',
                'phone'             => '081122334455',
                'is_active'         => true,
                'join_date'         => '2022-05-20',
                'employee_status'   => 'Permanent',
                'last_education'    => 'S1',
                'educational_major' => 'Akuntansi',
                'branch_id'         => $branchLagoon->id, // Ditetapkan di Lagoon
                'position_id'       => $posStaff ? $posStaff->id : 1,
                'department_id'     => $deptHR ? $deptHR->id : 1,
                'barcode_token'     => Str::random(32),
            ]
        );

        // =====================================================================
        // 6. TAMBAHAN DATA MONITORING UNIT (Cabang Sepanjang)
        // =====================================================================
        User::firstOrCreate(
            ['email' => 'alyandi@happymomo.com'],
            [
                'name'              => 'Muhammad Alyandi',
                'password'          => Hash::make('password'),
                'role'              => 'employee',
                'nik'               => '231129H34',
                'phone'             => '082269010051',
                'address'           => 'Jl. Kupang Indah 2 No 43',
                'is_active'         => true,
                'join_date'         => '2023-11-29',
                'employee_status'   => 'Permanent',
                'last_education'    => 'SMA',
                'educational_major' => 'IPA',
                'branch_id'         => $branchSepanjang->id, // Ditetapkan di Sepanjang
                'position_id'       => $posStaff ? $posStaff->id : 1,
                'department_id'     => $deptOps ? $deptOps->id : 1,
                'barcode_token'     => Str::random(32),
            ]
        );
    }
}