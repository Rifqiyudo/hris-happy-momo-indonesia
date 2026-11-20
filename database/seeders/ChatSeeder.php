<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{User, Chat};

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            $this->command->error('Gagal: Admin tidak ditemukan.');
            return;
        }

        $employees = User::where('role', 'employee')->get();

        $count = 0;
        foreach ($employees as $employee) {
            // Gunakan substr untuk memastikan data tidak melebihi kapasitas kolom
            // jika database belum diubah.
            $status = substr('active', 0, 20); 
            $subject = substr('Percakapan Internal', 0, 100);

            $chat = Chat::firstOrCreate(
                [
                    'user_id' => $employee->id,
                    'hrd_id'  => $admin->id
                ],
                [
                    'subject'    => $subject,
                    'status'     => $status,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            if ($chat->wasRecentlyCreated) {
                $count++;
            }
        }

        $this->command->info("Chat threads berhasil diinisialisasi untuk $count karyawan baru.");
    }
}