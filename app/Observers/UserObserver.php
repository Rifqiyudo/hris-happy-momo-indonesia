<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Chat;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "created" event.
     * Membuat ruang chat otomatis saat karyawan baru didaftarkan.
     */
    public function created(User $user): void
    {
        // Pastikan hanya role 'employee' yang dibuatkan ruang chat
        if ($user->role === 'employee') {
            // Mengambil Admin/HRD utama untuk dihubungkan dengan karyawan baru
            $hrd = User::where('role', 'admin')->first();

            if ($hrd) {
                Chat::create([
                    'user_id' => $user->id,
                    'hrd_id'  => $hrd->id,
                    'status'  => 'open'
                ]);
            }
        }
    }

    /**
     * Handle the User "updated" event.
     * Kita bisa mengupdate subject chat jika nama karyawan berubah.
     */
    public function updated(User $user): void
    {
        // Opsional: Jika Anda ingin nama chat tetap sinkron dengan nama user
        // Chat::where('user_id', $user->id)->update(['subject' => 'Chat dengan ' . $user->name]);
    }

    /**
     * Handle the User "deleted" event.
     * Database akan menghapus chat secara otomatis via onDelete('cascade').
     * Kita hanya menambahkan log untuk kebutuhan audit HRD.
     */
    public function deleted(User $user): void
    {
        Log::info("Karyawan {$user->name} dihapus, data chat terkait telah dibersihkan.");
    }
    
    // Metode restored dan forceDeleted tidak perlu diubah kecuali 
    // Anda menggunakan fitur SoftDeletes pada model User.
}