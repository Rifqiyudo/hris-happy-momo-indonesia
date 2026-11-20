<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mengisi data unit kerja/cabang untuk integrasi Profil, Monitoring, dan Absensi Geofencing.
     */
    public function run(): void
    {
        // 1. PUSAT - PT. HAPPY MOMO INDONESIA
        Branch::updateOrCreate(
            ['id' => 1],
            [
                'code'      => 'HO-SBY',
                'name'      => 'PT. Happy Momo Indonesia (Pusat)',
                'pic_name'  => 'Operational Manager',
                'phone'     => '031-1234567',
                'email'     => 'ho@happymomo.id',
                'address'   => 'Jl. Pergudangan Margomulyo Permai, Gedangasin, Kec. Asem Rowo',
                'city'      => 'Surabaya',
                'province'  => 'Jawa Timur',
                'latitude'  => -7.25115760,
                'longitude' => 112.68169460,
                'radius'    => 100,
                'timezone'  => 'Asia/Jakarta',
                'is_active' => true,
            ]
        );

        // 2. CABANG - PT. AIMI PLASTIK INDONESIA
        Branch::updateOrCreate(
            ['id' => 2],
            [
                'code'      => 'AIMI-SBY',
                'name'      => 'PT. Aimi Plastik Indonesia',
                'pic_name'  => 'Plant Manager',
                'phone'     => '031-7654321',
                'email'     => 'admin.aimi@happymomo.id',
                'address'   => 'Jl. Pergudangan Margomulyo Permai, Gedangasin, Kec. Asem Rowo',
                'city'      => 'Surabaya',
                'province'  => 'Jawa Timur',
                'latitude'  => -7.25115760,
                'longitude' => 112.68169460,
                'radius'    => 100,
                'timezone'  => 'Asia/Jakarta',
                'is_active' => true,
            ]
        );

        // 3. CABANG - PT. MOLION TOYS INDONESIA
        Branch::updateOrCreate(
            ['id' => 3],
            [
                'code'      => 'MOL-SDA',
                'name'      => 'PT. Molion Toys Indonesia',
                'pic_name'  => 'Site Manager',
                'phone'     => '031-888999',
                'email'     => 'molion@happymomo.id',
                'address'   => 'Jl. Raya Sadang, Sadang, Kec. Taman',
                'city'      => 'Sidoarjo',
                'province'  => 'Jawa Timur',
                'latitude'  => -7.37749600,
                'longitude' => 112.67948830,
                'radius'    => 75,
                'timezone'  => 'Asia/Jakarta',
                'is_active' => true,
            ]
        );

        // 4. CABANG - PT. PANDA PLAYHOUSE INDONESIA (LAGOON AVENUE MALL)
        Branch::updateOrCreate(
            ['id' => 4],
            [
                'code'      => 'PND-LGN',
                'name'      => 'PT. Panda Playhouse Indonesia (Lagoon)',
                'pic_name'  => 'Store Leader',
                'phone'     => '031-555444',
                'email'     => 'lagoon@happymomo.id',
                'address'   => 'Lagoon Avenue Mall Sungkono, Jl. KH Abdulমঙ্গল Wahab Siamin Surabaya No.Kav. 9-10, Putat Gede',
                'city'      => 'Surabaya',
                'province'  => 'Jawa Timur',
                'latitude'  => -7.28945030, 
                'longitude' => 112.70425780,
                'radius'    => 100, 
                'timezone'  => 'Asia/Jakarta',
                'is_active' => true,
            ]
        );

        // 5. CABANG - PT. PANDA PLAYHOUSE (SITE 2 - SEPANJANG)
        Branch::updateOrCreate(
            ['id' => 5],
            [
                'code'      => 'PND-SPJ',
                'name'      => 'PT. Panda Playhouse (Sepanjang)',
                'pic_name'  => 'Site Leader',
                'phone'     => '031-333222',
                'email'     => 'sepanjang@happymomo.id',
                'address'   => 'Ruko Sepanjang Town House, Jl. Raya Kalijaten Sepanjang - Taman',
                'city'      => 'Sidoarjo',
                'province'  => 'Jawa Timur',
                'latitude'  => -7.35058100,
                'longitude' => 112.69224600,
                'radius'    => 50,
                'timezone'  => 'Asia/Jakarta',
                'is_active' => true,
            ]
        );
    }
}