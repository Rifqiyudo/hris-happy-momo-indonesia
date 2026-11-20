<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel pola jadwal mingguan sebagai template default karyawan.
     */
    public function up(): void
    {
        Schema::create('weekly_schedules', function (Blueprint $table) {
            $table->id();

            // Indeks Hari: 0 (Minggu) sampai 6 (Sabtu) sesuai standar PHP date('w')
            $table->integer('day_index')->unique(); 
            $table->string('day_name'); // Nama hari (Senin, Selasa, dsb)

            // Relasi ke Master Shift
            // onDelete('set null') agar jika master shift dihapus, jadwal mingguan tidak ikut terhapus (hanya jadi kosong)
            $table->foreignId('shift_id')
                  ->nullable()
                  ->constrained('shifts')
                  ->onDelete('set null');

            // Penanda apakah hari tersebut adalah hari libur (Off)
            $table->boolean('is_off')->default(false);

            $table->timestamps();
        });

        // --- AUTOMATIC SEEDER ---
        // Mengisi data awal 7 hari agar sistem langsung siap digunakan tanpa input manual ke database.
        $days = [
            ['day_index' => 1, 'day_name' => 'Senin', 'is_off' => false],
            ['day_index' => 2, 'day_name' => 'Selasa', 'is_off' => false],
            ['day_index' => 3, 'day_name' => 'Rabu', 'is_off' => false],
            ['day_index' => 4, 'day_name' => 'Kamis', 'is_off' => false],
            ['day_index' => 5, 'day_name' => 'Jumat', 'is_off' => false],
            ['day_index' => 6, 'day_name' => 'Sabtu', 'is_off' => false],
            ['day_index' => 0, 'day_name' => 'Minggu', 'is_off' => true],
        ];

        foreach ($days as $day) {
            DB::table('weekly_schedules')->insert(array_merge($day, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_schedules');
    }
};