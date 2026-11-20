<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk membuat tabel notifications.
     */
    public function up(): void
    {
        // Tabel ini akan menyimpan semua notifikasi (Cuti, Gaji, Pengumuman, dll)
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Menggunakan UUID agar ID notifikasi unik secara global
            $table->string('type');        // Nama class notifikasi (misal: LeaveApprovedNotification)
            $table->morphs('notifiable');  // Menyimpan ID dan tipe User yang menerima notif
            $table->text('data');          // Isi pesan (JSON) yang dikirim dari controller
            $table->timestamp('read_at')->nullable(); // Status kapan notif dibaca oleh karyawan
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};