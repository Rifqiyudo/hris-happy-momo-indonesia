<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel untuk menyimpan jatah kuota cuti tahunan karyawan.
     */
    public function up(): void
    {
        Schema::create('leave_allowances', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel users
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade')
                  ->comment('ID Karyawan');

            // Tahun anggaran kuota (Contoh: 2025, 2026)
            $table->integer('year');

            // Jumlah total jatah cuti (Contoh: 12)
            $table->integer('quota')->default(0);

            // Kolom catatan (Opsional, misal: "Bonus cuti dari project")
            $table->string('note')->nullable();

            $table->timestamps();

            /** * PENTING: Unique Constraint
             * Mencegah satu karyawan memiliki 2 baris kuota di tahun yang sama.
             */
            $table->unique(['user_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_allowances');
    }
};