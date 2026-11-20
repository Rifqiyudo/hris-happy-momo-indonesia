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
        Schema::create('allowances', function (Blueprint $table) {
            $table->id();
            
            // 1. Identitas Tunjangan
            $table->string('name'); // Contoh: "Tunjangan Jabatan", "Uang Makan"
            
            // 2. Logika Perhitungan (PENTING)
            // 'fixed' = Tunjangan Tetap (Diterima penuh bulanan, tidak peduli absen)
            // 'daily' = Tunjangan Variabel (Dikali jumlah kehadiran/absensi)
            $table->enum('type', ['fixed', 'daily'])->default('fixed')
                  ->comment('fixed: Bulanan Tetap, daily: Berdasarkan Kehadiran');

            // 3. Nominal
            $table->decimal('amount', 15, 2)->default(0); // Nominal default
            
            // 4. Aturan Pajak & Gaji
            // Apakah tunjangan ini kena pajak PPh21? (Fitur HRIS Pro)
            // Default true, karena rata-rata tunjangan adalah objek pajak.
            $table->boolean('is_taxable')->default(true); 

            // 5. Status Aktif
            // Agar admin bisa menonaktifkan tunjangan tanpa menghapusnya
            $table->boolean('is_active')->default(true);

            // 6. Deskripsi Tambahan
            $table->text('description')->nullable();

            // 7. Standar Laravel
            $table->timestamps();
            $table->softDeletes(); // Menambahkan fitur "Sampah" (Data tidak langsung hilang permanen)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allowances');
    }
};