<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;
use App\Models\Department;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data Jabatan Lengkap dengan Level & Target Departemennya
        $data = [
            // --- LEVEL DIRECTOR ---
            [
                'name' => 'Direktur Utama',
                'department_name' => 'Board of Directors',
                'level' => 'director',
                'base_salary' => 25000000,
                'description' => 'Bertanggung jawab penuh atas jalannya operasional perusahaan secara keseluruhan.',
            ],

            // --- LEVEL MANAGER ---
            [
                'name' => 'Manager Operasional',
                'department_name' => 'Operasional',
                'level' => 'manager',
                'base_salary' => 12000000,
                'description' => 'Mengawasi jalannya operasional harian dan memastikan target tercapai.',
            ],
            [
                'name' => 'HR & GA Manager',
                'department_name' => 'HRGA',
                'level' => 'manager',
                'base_salary' => 10000000,
                'description' => 'Mengelola sumber daya manusia dan aset umum perusahaan.',
            ],

            // --- LEVEL SUPERVISOR ---
            [
                'name' => 'Kepala Gudang (Ka. Gudang)',
                'department_name' => 'Gudang & Logistik',
                'level' => 'supervisor',
                'base_salary' => 6500000,
                'description' => 'Memimpin tim gudang, kontrol stok opname, dan alur keluar masuk barang.',
            ],
            [
                'name' => 'Finance Supervisor',
                'department_name' => 'Finance & Accounting',
                'level' => 'supervisor',
                'base_salary' => 7000000,
                'description' => 'Memastikan laporan keuangan akurat dan cashflow terjaga.',
            ],

            // --- LEVEL STAFF ---
            [
                'name' => 'Admin HR / Finance',
                'department_name' => 'HRGA', // Bisa double departemen untuk admin rangkap
                'level' => 'staff',
                'base_salary' => 5000000,
                'description' => 'Mengurus administrasi karyawan, absensi, dan petty cash.',
            ],
            [
                'name' => 'Staff Gudang',
                'department_name' => 'Gudang & Logistik',
                'level' => 'staff',
                'base_salary' => 4800000, // UMR + Tunjangan
                'description' => 'Melakukan packing, loading, unloading, dan menjaga kebersihan gudang.',
            ],
            [
                'name' => 'Driver / Kurir',
                'department_name' => 'Gudang & Logistik',
                'level' => 'staff',
                'base_salary' => 4500000,
                'description' => 'Mengantar barang ke customer dan menjaga kondisi kendaraan.',
            ],
            [
                'name' => 'Helper / Kernet',
                'department_name' => 'Gudang & Logistik',
                'level' => 'staff',
                'base_salary' => 3800000,
                'description' => 'Membantu driver dalam pengiriman dan bongkar muat barang.',
            ],
            [
                'name' => 'Sales Executive',
                'department_name' => 'Marketing',
                'level' => 'staff',
                'base_salary' => 4500000,
                'description' => 'Mencari customer baru dan menjaga hubungan dengan klien.',
            ],
        ];

        foreach ($data as $item) {
            // 1. Cari atau Buat Departemennya dulu agar ID-nya valid
            // Ini menjaga agar seeder tidak error walau tabel departments masih kosong
            $dept = Department::firstOrCreate(
                ['name' => $item['department_name']]
            );

            // 2. Simpan Jabatan
            Position::updateOrCreate(
                ['name' => $item['name']], // Kunci pengecekan (agar tidak duplikat)
                [
                    'department_id' => $dept->id,
                    'level'       => $item['level'],
                    'base_salary' => $item['base_salary'],
                    'description' => $item['description'],
                ]
            );
        }
    }
}