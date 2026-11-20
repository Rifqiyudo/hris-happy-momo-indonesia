<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk sistem KPI (Key Performance Indicator).
     */
    public function up(): void
    {
        // 1. Tabel Master Indikator KPI
        // Digunakan untuk menentukan apa saja yang dinilai (Contoh: Absensi, Target, Attitude)
        Schema::create('kpi_indicators', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->text('description')->nullable();
            $table->integer('weight')->comment('Bobot dalam persen, total harus 100%');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Tabel Transaksi Penilaian KPI
        Schema::create('kpis', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke karyawan
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Periode Penilaian
            $table->string('period_month', 2); // 01 - 12
            $table->string('period_year', 4);  // 2026
            
            // Detail Nilai (Disimpan dalam JSON agar fleksibel mengikuti jumlah indikator)
            // Format: {"indikator_id": "nilai", "1": 80, "2": 95}
            $table->json('score_details')->comment('Rincian nilai per indikator');
            
            // Hasil Akhir
            $table->decimal('final_score', 5, 2)->default(0); 
            $table->enum('grade', ['A', 'B', 'C', 'D', 'E'])->nullable();
            
            // Catatan & Metadata
            $table->text('feedback')->nullable()->comment('Komentar dari penilai');
            $table->foreignId('rated_by')->constrained('users')->onDelete('cascade');
            
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();

            // Indexing untuk pencarian cepat laporan bulanan
            $table->index(['period_month', 'period_year', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpis');
        Schema::dropIfExists('kpi_indicators');
    }
};