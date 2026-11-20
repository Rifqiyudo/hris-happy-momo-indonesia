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
        Schema::create('shift_schedules', function (Blueprint $table) {
            $table->id();

            // 1. Informasi Hari
            $table->string('day_name'); // Senin, Selasa, Rabu, dst.
            
            // Angka urutan hari (1=Senin, 7=Minggu)
            // Kita beri 'unique' agar tidak ada duplikasi hari (misal Senin ada 2 kali)
            $table->integer('day_number')->unique(); 

            // 2. Relasi ke Master Shift
            // Kolom ini menentukan shift apa yang dipakai di hari tersebut.
            // constrained('shifts'): Terkoneksi ke tabel 'shifts'.
            // onDelete('cascade'): Jika Master Shift dihapus, jadwal ini ikut terhapus (agar tidak error).
            $table->foreignId('shift_id')
                  ->constrained('shifts')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_schedules');
    }
};