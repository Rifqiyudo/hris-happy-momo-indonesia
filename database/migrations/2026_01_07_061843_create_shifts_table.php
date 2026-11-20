<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('shifts')) {
            Schema::create('shifts', function (Blueprint $table) {
                $table->id();

                // 1. Identitas Shift
                $table->string('name'); // Contoh: "Shift Pagi", "Shift Malam"
                $table->string('code')->unique(); // Contoh: "SH-01" (Wajib unik untuk mapping import CSV)

                // 2. Jam Kerja Utama (Format: HH:mm:ss)
                $table->time('start_time'); // Jam Masuk (Contoh: 08:00:00)
                $table->time('end_time');   // Jam Pulang (Contoh: 17:00:00)

                // 3. Jam Istirahat
                // Digunakan untuk menghitung total jam kerja bersih (Work Hours)
                $table->time('break_start')->nullable(); // Mulai Istirahat (12:00:00)
                $table->time('break_end')->nullable();   // Selesai Istirahat (13:00:00)

                // 4. Aturan Toleransi & Kiosk Logic
                // Toleransi dalam menit. Jika masuk 08:16 (Toleransi 15), sistem belum menganggap 'Telat'.
                $table->integer('late_tolerance')->default(0); 
                $table->integer('early_leave_tolerance')->default(0);

                // 5. Fitur Shift Malam (Lintas Hari)
                // Jika True, sistem akan tahu bahwa absen pulang dilakukan di tanggal berikutnya.
                $table->boolean('is_night_shift')->default(false); 
                
                // Jika True, maka hari tersebut tidak mewajibkan absensi.
                $table->boolean('is_holiday')->default(false); 

                // 6. UI & Dashboard Branding
                $table->string('color', 7)->default('#6366f1'); // Hex Color (e.g., #6366f1)

                // Deskripsi Tambahan
                $table->text('description')->nullable();

                // Standar Laravel
                $table->timestamps();
                $table->softDeletes(); // Penting: Agar riwayat absen lama tidak rusak jika shift dihapus
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};