<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil ID Admin pertama. Gunakan fallback ID 1 jika user belum ada.
        $admin = User::where('role', 'admin')->first();
        $adminId = $admin ? $admin->id : 1;

        $announcements = [
            [
                'title' => 'Pembaruan Kebijakan Lembur 2026',
                'content' => 'Mulai 1 Maret 2026, pengajuan lembur wajib dilakukan H-1 melalui aplikasi mobile Happy Momo.',
                'type' => 'info',
                'target' => 'all', // Ditambahkan agar scopeForUser bisa membacanya
                'is_active' => true,
            ],
            [
                'title' => 'Pengumuman Libur Nasional (Idul Fitri)',
                'content' => 'Sehubungan dengan hari raya Idul Fitri, operasional kantor akan diliburkan mulai tanggal 30 Maret.',
                'type' => 'warning',
                'target' => 'all',
                'is_active' => true,
            ],
            [
                'title' => 'Maintenance Sistem Absensi Kiosk',
                'content' => 'Akan dilakukan pemeliharaan sistem pada hari Sabtu ini pukul 22:00 WIB.',
                'type' => 'danger',
                'target' => 'all',
                'is_active' => true,
            ],
            [
                'title' => 'Selamat Kepada Karyawan Terbaik Bulan Ini!',
                'content' => 'Selamat kepada Budi Santoso dari Departemen Gudang atas dedikasinya sebagai Employee of the Month.',
                'type' => 'success',
                'target' => 'all',
                'is_active' => true,
            ],
        ];

        foreach ($announcements as $item) {
            // Menggunakan updateOrCreate untuk mencegah duplikasi saat re-seed
            Announcement::updateOrCreate(
                ['title' => $item['title']], // Kunci pengecekan
                [
                    'user_id'       => $adminId,
                    'content'       => $item['content'],
                    'type'          => $item['type'],
                    'target'        => $item['target'], // Pastikan target masuk ke DB
                    'is_active'     => $item['is_active'],
                    'slug'          => Str::slug($item['title']),
                    'department_id' => null, // Set null agar bisa dilihat semua departemen
                ]
            );
        }
    }
}